<?php 

/**
 * Form Helper
 * <example> // in template
 	<?php $this->form->begin(); ?>
	<?php $this->form->text( 'core_page_id' ); ?>
	<?php $this->form->select( 'selecttest', array('123'=>'123', '345'=>'345'), '', array() ); ?>
	<?php $this->form->checkbox( 'ch1', true ); ?>
	<?php $this->form->textarea( 'ta1', null, array('cols'=>80, 'rows'=>40) ); ?>
	<?php $this->form->submit(); ?>
	<?php $this->form->end(); ?>
 * </example>
 */

//K_Loader::load('Helpers/IFormHelper');

class ruHelper {
	
    /**
     * Изменяет второе слово следующее за числом, например 1 год, 2 года, 5 лет 
     * @param int $number
     * @param array $variants    => 1 => 'год', 2 => 'года', 5 => 'лет'
     * @return string
     */
    public function number( $number, $variants ) {
        $number = (int)abs($number);
        $string = (string)$number;
        
        if (strlen($string)>2) {
            $string = substr( $string, strlen($string)-2, 2);
            $number = (int)$string;
        }
        
        if ($number >= 5 && $number <= 20  ) {
            return $variants[5];
        }
        
        $mod10 = $number % 10;
        
        if ( $mod10 == 1 ) {
            return $variants[1];
        }
        
        if ( $mod10 == 2 || $mod10 == 3 || $mod10 == 4 ) {
            return $variants[2];    
        }
        
        return $variants[5];
    }
	
}


//год 1 21 31
//года 2 3 4 22 23 24 32 33 34
//лет 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 25 26 27 28 29 30 35