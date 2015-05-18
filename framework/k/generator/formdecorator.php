<?php

class K_Generator_FormDecorator {
    
    var $rules = [
        'text' => [
            'render' => [
                'html' => '<?php echo $this->form->text( \'{attr/name}\', {value}, {attr/*} ); ?>',
            ],
            'view' => [
                'default' => '<div><label for={attr/id}>{render/html}</label></div>',
                'error' => '<div class="error"><label for={attr/id}>{render/html}</label><?php $error->render( \'{name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => 'input-text',
                'name' => 'text'
            ]            
        ],
        
        'password' => [
            'render' => [
                'html' => '<?php echo $this->form->password( \'{attr/name}\', {value}, {attr/*} ); ?>',
            ],
            'view' => [
                'default' => '<div><label for="{attr/id}">{render/html}</label></div>',
                'error' => '<div class="error"><label for={attr/id}>{render/html}</label><?php $error->render( \'{name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => ['input-text', 'input-password'],
                'name' => 'password'
            ]
        ],
        
        'hidden' => [
            'render' => [
                'html' => '<?php echo $this->form->hidden( \'{attr/name}\', {value}, {attr/*} ); ?>',
            ],
            'view' => [
                'default' => '{render/html}',
                'error' => '{render/html}<div><?php $error->render( \'{name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => 'input-hidden',
                'name' => 'hidden'
            ]            
        ],
        
        'select' => [
            'render' => [
                'html' => '<?php echo $this->form->select( \'{attr/name}\', {options/*}, {value}, {attr/*} ); ?>',
            ],
            'view' => [
                'default' => '<div><label for={attr/id}>{render/html}</label></div>',
                'error' => '<div class="error"><label for={attr/id}>{render/html}</label><?php $error->render( \'{name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => 'input-select',
                'name' => 'select'
            ]
        ],
        
        'checkbox' => [
            'render' => [
                'html' => '<?php echo $this->form->checkbox( \'{attr/name}\', {value}, {attr/*} ); ?>',
            ],
            'view' => [
                'default' => '<div><label for={attr/id}>{render/html}</label></div>',
                'error' => '<div class="error"><label for={attr/id}>{render/html}</label><?php $error->render( \'{name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => 'input-checkbox',
                'name' => 'checkbox'
            ]
        ],
        
        'radio' => [
            'render' => [
                'html' => '<?php echo $this->form->radio( \'{attr/name}\', {value}, {attr/*} ); ?>',
            ],
            'view' => [
                'default' => '<div><label for={attr/id}>{render/html}</label></div>',
                'error' => '<div class="error"><label for={attr/id}>{render/html}</label><?php $error->render( \'{name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => 'input-radio',
                'name' => 'radio'
            ]
        ],
        
        'file' => [
            'render' => [
                'html' => '<?php echo $this->form->file( \'{attr/name}\', {attr/*} ); ?>',
            ],
            'view' => [
                'default' => '<div><label for={attr/id}>{render/html}</label></div>',
                'error' => '<div class="error"><label for={attr/id}>{render/html}</label><?php $error->render( \'{name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => 'input-file',
                'name' => 'file'
            ]
        ],
        
        'submit' => [
            'render' => [
                'html' => '<?php echo $this->form->submit( \'{value}\', \'{form/attr/name}\' ); ?>',
            ],
            'view' => [
                'default' => '<div>{render/html}</div>',
                'error' => '<div class="error">{render/html}</div>',
            ],
            'attr' => [
                'class' => 'input-submit input-submit-{form/attr/name}'
            ]
        ],
        
        'reset' => [
            'render' => [
                'html' => '<?php echo $this->form->reset( \'{value:n}\', \'{form/attr/name}\' ); ?>',
            ],
            'view' => [
                'default' => '<div class="input-wrapper input-wrapper-{form/attr/name}">{form/render/html}</div>',
                'error' => '<div class="input-error input-reset-error input-error-{form/attr/name}">{form/render/html}</div>',
            ],
            'attr' => [
                'class' => 'input-reset input-reset-{form/attr/name}',
                'name' => 'input-reset'
            ]
        ],
        
        'textarea' => [
            'render' => [
                'html' => '<?php echo $this->form->textarea( \'{form/attr/name}\', {value:n}, {form/attr/*} ); ?>',
            ],
            'view' => [
                'default' => '<div><label for={form/attr/id}>{form/render/html}</label></div>',
                'error' => '<div class="error"><label for={form/attr/id}>{form/render/html}</label><?php $error->render( \'{form/attr/name}\', $this->errosList )</div>',
            ],
            'attr' => [
                'class' => 'input-textarea input-name-{form/attr/name}',
                'name' => 'textarea'
            ]
        ],
        
    ];
    
    /*
     * $info = [ 'form' => [ 'type' => 'text' ] ];
     */
    public function renderElement( $name, &$info ) {
        $elementData = $info;
        if ( isset($elementData['form'], $elementData['form']['type'] ) && array_key_exists( $elementData['form']['type'], $this->rules) ) {
            $elementData['form'] = array_merge_recursive( $this->rules[ $elementData['form']['type'] ], $elementData['form'] );
        } else {
            throw new Exception('FormDecorator -> renderElement -> data is undefined');
            return;
        }

        if ( isset($elementData['visible']) && $elementData['visible'] == false ) {
            return;
        }
                
        $elementData['form']['attr']['name'] = $name;
        $elementName = $name;
        
        $elementData['value'] = isset($_POST[ $name ]) ? $_POST[ $name ] : null;
        
        if ( isset($elementData['editable']) && $elementData['editable'] == false ) {
            // @TODO RENDER AS HIDDEN ELEMENTS
            $elementData['render']['html'] = isset( $_POST[ $elementName ] ) ? $_POST[ $elementName ] : '';
        }
        
        
        // search variables
        $searchPatches = ['form/render/html', 'form/attr/class', 'form/view/default', 'form/view/error'];
        $searchString = '';
        
        
        foreach( $searchPatches as $path ) {
            $searchString .= K_Library::arrayPathSearch($elementData, $path, true);    
        }
        
        $vars = [];      
        $matches = [];
        if ( preg_match_all('/(?<name>\{(?<var>[a-z0-9\_\-\/\*]+)(?<method>[\:][sn*])?\})/is', $searchString, $matches) && count($matches['var']) ) {
            for( $i=0; $i<count($matches['var']); $i++) {
                $varValue = K_Library::arrayPathSearch( $elementData, $matches['var'][$i], $matches['method'][$i] == ':s' ? true : false );
                if ( empty($varValue) ) {
                    if ( $matches['method'][$i] === ':n' ) { // show 'null' on null ;)
                        $varValue = 'null';
                    } else {
                        $varValue = '';
                    }
                }
                $vars[ $matches['name'][$i] ] = $varValue;                        
            }
            foreach( $vars as &$varValue ) {
                if ( is_string($varValue) ) {
                    $varValue = strtr( $varValue, $vars );
                } else {
                    $varValue = strtr( K_Library::buildVariableValue( $varValue, true ), $vars );
                }
            }
        }
        
        var_dump($vars);
        
        echo '<br><br><br>';
        var_dump($elementData);
        //K_Library::arrayPathSearch();
        
        
        
    }
    
}