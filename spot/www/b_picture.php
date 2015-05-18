<?php
require_once '../classes/image.class.php';

$size = 600; 

$image = new thumbnail($_GET['src']);
//$image->size_width($size);   //Фіксована ширина
//$image->size_height($size);  //Фіксована висота
$image->size_auto($size);   //Фіксована ширина або висота
//$image->size_crop($size);    //Одинакові ширина та висота
//$image->size_width_height($size,$size_h); //Довільна ширина та висота
$image->add_logo("watermark.png"); //Додати лого до картинки 
$image->show();
?>