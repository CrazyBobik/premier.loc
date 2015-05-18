<?php

	include(dirname(__FILE__).'/www/libraries/img_tool_kit/AcImage.php');

	$pi = pathinfo($_GET['src']);
	
	//  накладываем лого	
			
	$image = AcImage::createImage('./upload/'.trim($_GET['src']));
		
	$image->resize(intval($_GET['w'], intval($_GET['h'])->show()));
	
?>