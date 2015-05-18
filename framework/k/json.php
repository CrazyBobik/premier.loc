<?php 
/**
 * @package    DM
 * @category   Helpers
 */
class K_Json{



	// Кодирует json с сохранением функций
	public static function json($foo)
	{
       	$value_arr = array();
        $replace_keys = array();
        foreach($foo as $key => &$value){
         // Проверяем значения на наличие определения функции
         if(strpos($value, 'function(')===0){
            // Запоминаем значение для послудующей замены.
           
            $value_arr[] = $value;
            // Заменяем определение функции 'уникальной' меткой..
            $value = '%' . $key . '%';
            // Запоминаем метку для послудующей замены.
            $replace_keys[] = '"' . $value . '"';
         }
        }
        
        // Кодируем массив в JSON и заменяем 
       return str_replace($replace_keys, $value_arr, json_encode($foo));	
   	}

} // End arr
