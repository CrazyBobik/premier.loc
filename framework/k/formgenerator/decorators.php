<?php 

class K_FormGenerator_Decorators {
	public static $templates = array(
			'text' => array(
				'replacements' => array( 
					'name' 			=> 'string',
					'label'			=> 'string', 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '<div>@this.label@this.element</div>',
				'label' 	=> '<label for="@name">@label</label>',
				'element' 	=> '<?php $this->form->text( "@name", "@value", @attributes ); ?>'
			),
			'submit' => array(
				'replacements' => array( 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '<div>@this.element</div>',
				'element' 	=> '<?php $this->form->submit( "@value", @attributes ); ?>'
			),
			'hidden' => array(
				'replacements' => array( 
					'name'			=> 'string', 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '@this.element',
				'element' 	=> '<?php $this->form->hidden( "@name", "@value", @attributes ); ?>'
			),
			'password' => array(
				'replacements' => array( 
					'name'			=> 'string', 
					'label'			=> 'string', 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '<div>@this.label@this.element</div>',
				'label' 	=> '<label for="@name">@label</label>',
				'element' 	=> '<?php $this->form->password( "@name", "@value", @attributes ); ?>'
			),
			'reset' => array(
				'replacements' => array( 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '<div>@this.element</div>',
				'element' 	=> '<?php $this->form->reset( "@value", @attributes ); ?>'
			),
			'select' => array(
				'replacements' => array( 
					'name'			=> 'string', 
					'label'			=> 'string', 
					'options'		=> 'array', 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '<div>@this.label@this.element</div>',
				'label' 	=> '<label for="@name">@label</label>',
				'element' 	=> '<?php $this->form->select( "@name", @options, "@value", @attributes ); ?>'
			),
			'checkbox' => array(
				'replacements' => array( 
					'name'			=> 'string', 
					'label'			=> 'string', 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '<div>@this.label@this.element</div>',
				'label' 	=> '<label for="@name">@label</label>',
				'element' 	=> '<?php $this->form->checkbox( "@name", "@value", @attributes ); ?>'
			),
			'textarea' => array(
				'replacements' => array( 
					'name'			=> 'string', 
					'label'			=> 'string', 
					'value'			=> 'string', 
					'attributes'	=> 'array' 
				),
				'decorator' => '<div>@this.label@this.element</div>',
				'label' 	=> '<label for="@name">@label</label>',
				'element' 	=> '<?php $this->form->textarea( "@name", "@value", @attributes ); ?>'
			),
		);
	
	public static function getTemplates() {
		return self::$templates;
	}
}