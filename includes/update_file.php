<?php 
function file_force_download($file) {
 	if (file_exists($file)) {
    	// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
    	if (ob_get_level()) {
    		ob_end_clean();
    	}
    	header('Content-Description: File Transfer');
    	header('Content-Type: application/octet-stream');
    	header('Content-Disposition: attachment; filename=' . basename($file));
    	header('Content-Transfer-Encoding: binary');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate');
    	header('Pragma: public');
    	header('Content-Length: ' . filesize($file));
    	// читаем файл и отправляем его пользователю
    	if ($fd = fopen($file, 'rb')) {
    		while (!feof($fd)) {
        		print fread($fd, 1024);
      		}
      		fclose($fd);
    	}
  	}
}
	include('db.php'); //подключение к БД
	//создание таблицы если ее нету
	$r = mysqli_query($connection, "SELECT * FROM `csv_file_r`");
  	if ($r==false) {
  			mysqli_query($connection,"CREATE TABLE `csv_file_r` (`id` INTEGER(20) AUTO_INCREMENT PRIMARY KEY , `code` VARCHAR(255) NOT NULL UNIQUE,`name` VARCHAR(255))"); 
  	}
	// Загрузка файла
	if (isset($_FILES['file_csv'])) { 
		$file_open=true;
		$file_name = $_FILES['file_csv']['name'];
		$file_size = $_FILES['file_csv']['size'];
		$file_tmp = $_FILES['file_csv']['tmp_name'];
		$file_type = $_FILES['file_csv']['type'];
		$file_ext = strtolower(end(explode('.', $_FILES['file_csv']['name'])));	
		move_uploaded_file($file_tmp, "../file/".$file_name);			
	}

	$code_match = array('_', '№', '"', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', ';', "'", ',', '/', '', '~', '`', '=');//недопустимые символы
	
	$text_file = "Код,Название,Error\r\n"; //заголовк csv файла
	$row = 0;
	
	$handle = fopen("../file/". $file_name, "r"); // открываем файл
	while (($data = fgetcsv($handle, 0, ",")) !== FALSE){ // Разделитель в файле ","
		$fileR[] = array_map(function($val){ return iconv('CP1251', 'UTF-8', $val); }, $data); //записываем строчки в двухмерный массив при этом меняя кодировку	
		
		$no_error_text = str_replace($code_match, '', $fileR[$row][1]); // удаляем недопустимые символы из колонки "название"
		$arr_no_error_text = str_split($no_error_text); //преобразует текст без ошибки в массив
		$error_varchar = str_replace($arr_no_error_text, '', $fileR[$row][1]); // возвращает недопустимые символы
		$code = $name=$fileR[$row][0];
		$name=$fileR[$row][1];
		mysqli_query($connection,"INSERT INTO `csv_file_r` SET `code`= '$code',`name`= '$name' ON DUPLICATE KEY UPDATE `name`= '$name'");//добавляем новую запись или обновляем уже существующую

		//формирование текста файла
		if ($error_varchar) {
			$text_file .= "$code,$name,Недопустимый символ\"".$error_varchar."\"\r\n";
		}else{
			$text_file .= "$code,$name,\r\n";
		}
		$row++;
	}
	fclose($handle); //закрываем файл
	
	// создание файла с обработанными данными	 
	$handle = fopen("../file/dow_file.csv", "a"); 
	iconv('CP1251', 'UTF-8', $text_file);
	fwrite($handle, $text_file);
	fclose($handle);
	file_force_download("../file/dow_file.csv"); //вызываем ф-цию на скачивание файла
	//удаляем все файлы
	$folder = '../file';
	foreach (glob("$folder/*.*") as $file) {
		unlink($file);
	}
?>