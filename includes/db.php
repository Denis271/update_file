<?php
$connection = mysqli_connect('server','username','passvorw','host');
if ($connection == false)
	{
		echo 'подключение не удалось';
		echo mysqli_connect_error();
		exit();
	}
mysqli_query($connection,'SET character_set_client="utf8", character_set_connection="utf8", character_set_results="utf8", character_set_database="utf8", character_set_server="utf8"');
?>