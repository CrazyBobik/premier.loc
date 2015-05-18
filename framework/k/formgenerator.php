<?php 

class K_FormGenerator {

	protected $formConfig;
	protected $formTemplates;
	protected $templatePath;
	
	public function __construct() {
	
	}
	
	public function compile( $name, $path ) {
		$path = realpath($path);
		$this->formConfig = K_Config::loadIni( $path.'/configs/'.$name.'.ini' );
		$this->formTemplates = K_FormGenerator_Decorators::getTemplates();
		$this->templatePath = $path.'/templates/'.$name.'.phtml';
		$this->saveScript( $path.'/compiled/'.$name.'.phtml' );
	}
	
	public function setFormConfig( &$config ) {
		$this->formConfig = $config;
	}
	
	public function setFormTemplates( &$config ) {
		$this->formTemplates = $config;
	}
	
	public function setTemplate( $path ) {
		$this->templatePath = $path;
	}
	
	public function saveScript( $toPath ) {
		if ( !is_file($this->templatePath) ) {
			return;
		}
		
		$templateHTML = file_get_contents($this->templatePath);
		
		if ( is_array($this->formConfig) && count($this->formConfig) ) {		
			$replacement = array();			
			foreach( $this->formConfig as $blockName => &$blockInfo ) {
				if ( $blockInfo != null ) {
					$replacement[ '{'.$blockName.'}' ] = $this->buildScript( $blockInfo );
				}
			}
			$templatePHTML = str_replace( array_keys($replacement), $replacement, $templateHTML );			
		}
		
		if ( !empty($toPath) ) {
			file_put_contents( $toPath, $templatePHTML );
		}
	}
	
	public function buildScript( &$config = null ) {	
		if ( empty($config) ) {
			$config = &$this->formConfig;
		}
		
		$php = '';	
			
		if ( is_array($config) && count($config) ) {		
			foreach( $config as $elementName => &$elementInfo ) {
				if ( is_array($elementInfo) && isset($this->formTemplates[ $elementInfo['type'] ]) ) {
					$elementInfo['name'] = $elementName;
					$php .= $this->buildElement( $elementInfo, $this->formTemplates[ $elementInfo['type'] ] ) . "\n";
				}
			}
		}
		return $php;
	}
	
	public function buildElement( &$elementInfo, &$templateInfo ) {
		$replaceData = array();

		if ( isset($templateInfo[ 'replacements' ]) ) {			
			foreach( $templateInfo[ 'replacements' ] as $paramName => $paramType ) {
				if ( isset( $elementInfo[ $paramName ] ) ) {
					if ( is_array($elementInfo[ $paramName ]) ) {
						$replaceData[ '@'.$paramName ] = var_export( $elementInfo[ $paramName ], true );
					} elseif ( is_string($elementInfo[ $paramName ]) || is_numeric($elementInfo[ $paramName ]) ) {
						$replaceData[ '@'.$paramName ] = $elementInfo[ $paramName ];
					}
				} else {
					if ( $paramType == 'array' ) {
						$replaceData[ '@'.$paramName ] = var_export( array(), true );
					} else { // == 'string'
						$replaceData[ '@'.$paramName ] = ( $paramName=='label'?'&nbsp':'' ); // &NBSP; FOR LABEL TAG
					}
				}
			}
		}
		
		$compiled = array();
		
		if ( isset($templateInfo[ 'label' ]) && is_string($templateInfo[ 'label' ]) ) {
			$compiled[ '@this.label' ] = str_replace( 
											array_keys($replaceData), 
											$replaceData, 
											$templateInfo[ 'label' ]
										);
		}
		
		if ( isset($templateInfo[ 'element' ]) && is_string($templateInfo[ 'element' ]) ) {		
			$compiled[ '@this.element' ] = str_replace( array_keys($replaceData), $replaceData, $templateInfo[ 'element' ]  );
		}
		
		if ( isset($templateInfo[ 'decorator' ]) && is_string($templateInfo[ 'decorator' ]) ) {
			return str_replace( array_keys($compiled), $compiled, $templateInfo[ 'decorator' ]  );
		}
		return '';
	}
}