<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style/index.css">
	<title>Обновление справочника</title>
</head>
<body>
	<div>
		<form action="includes/update_file.php" method="post" enctype="multipart/form-data">
			<p></p>
			<input class="input__file" type="file" name="file_csv" title="Выберите файл" id="input__file">
			<label for="input__file">
				<span class="input__file_button_text">Выберите файл</span>
			</label>
			<input class="submit" type="submit" value="Обработать" >
		</form>
	</div>
</body>
</html>