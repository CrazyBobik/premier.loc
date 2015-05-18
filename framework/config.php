<?php

	class Config{
	    var $db_host = "localhost"; //имя хоста
		var $db_user = "sql_dompliz"; //имя пользователя
		var $db_pass = "5HEkrxFy"; //пароль
		var $db_name = "dompliz"; //имя базы
	}
	
	$connect = new Config();
			
	if(($connect->db_host == '')||($connect->db_user == '')||($connect->db_name == ''))
		exit('Подключение к базе данных не настроено.');
		
?>