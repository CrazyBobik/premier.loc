<?php
require_once '../classes/image.class.php';

$size = 600; 

$image = new thumbnail($_GET['src']);
//$image->size_width($size);   //Գ������� ������
//$image->size_height($size);  //Գ������� ������
$image->size_auto($size);   //Գ������� ������ ��� ������
//$image->size_crop($size);    //�������� ������ �� ������
//$image->size_width_height($size,$size_h); //������� ������ �� ������
$image->add_logo("watermark.png"); //������ ���� �� �������� 
$image->show();
?>