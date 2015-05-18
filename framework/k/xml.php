<?php
 
class K_Xml{

	protected $reader;
	protected $doc;

	public function __construct($file, $version='1.0', $encoding='UTF-8'){
    
		$this->reader = new XMLReader();
	  
		$this->reader->open($file);
	  
		$this->doc = new DOMDocument($version, $encoding);
	
	}
	
	public function next($tag, $array = false){
	
		while ($this->reader->read()){
			if ($this->reader->nodeType == XMLREADER::ELEMENT && $this->reader->localName == $tag){
								
				$item = simplexml_import_dom($this->doc->importNode($this->reader->expand(),true));

				if($array){ 
					// возвращяем массив
					return json_decode(json_encode(simplexml_import_dom($this->doc->importNode($this->reader->expand(),true))), true); 
				
				}else{
					// возвращяем объект
					return simplexml_import_dom($this->doc->importNode($this->reader->expand(),true));
				
				}
			}		
		}
		
		return false;
		
	}
	
	public function reset(){
		return $this;
	}
	
	public function objectToArray($object){
		return $this;
	}
	
	
	public function __destruct(){
	
		$this->reader->close;
		
	}
	
}



