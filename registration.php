<?php
include_once("include/common.php");

# Функция для генерации случайной строки
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}

$errorMessage = "";
$password = "";
$email = "";
if(isset($_POST['email']))
{
	$email = $_POST['email'];
    $query = mysql_query("SELECT id, psw FROM user WHERE active = 1 AND login='".mysql_real_escape_string($_POST['email'])."' LIMIT 1");
    $data = mysql_fetch_assoc($query);
	if(mysql_num_rows($query) > 0){
		$errorMessage = "Пользователь с таким Email уже существует.";
	}
	else{
        $password = generateCode(6);
        $hash = md5(generateCode(10));
		mysql_query("INSERT INTO user (active, login, psw, hash) VALUES(1, '".mysql_real_escape_string($_POST['email'])."', '".md5(md5($password))."', '".$hash."')");

		$to  = $email; 

		$subject = "Регистрация на портале"; 

		$message = ' 
		<html> 
			<head> 
				<title>Регистрационные данные</title> 
			</head> 
			<body> 
				<p>Ваш логин: <b>'.$email.'</b></p> 
				<p>Ваш пароль: <b>'.$password.'</b></p> 
				<p>Вход: <a href="'.baseurl().'/login.php?login='.$email.'&hash='.$hash.'">'.baseurl().'/login.php?login='.$email.'&hash='.$hash.'</a></p> 
			</body> 
		</html>'; 

		$headers  = "Content-type: text/html; charset=utf-8 \r\n"; 
		//$headers .= "From: Birthday Reminder <birthday@example.com>\r\n"; 

		if (mail($to, $subject, $message, $headers)) { 
			$email = "";
			$errorMessage = "Регистрационые данные высланы на email.";
		} else { 
			$errorMessage = 'Проблемы с отправкой email. <a href="/login.php?login='.$email.'&hash='.$hash.'">Войти</a>';
		} 
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Регистрация</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script language="javascript">
			$(document).ready(function() {
			});
		</script>
	</head>
	<body>
		<div id="top-border">&nbsp;</div>
		<div id="page">
			<div id="header-row">
				<h1>Зарегистрироваться</h1>
			</div>
			<div id="page-container">
				<div id="warning-message">
					<? echo $errorMessage ?>
				</div>
				<form method="post" novalidate action="">
					<h2>&nbsp;</h2>
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="width356">
						    	<label>Логин: (адрес электронной почты)</label>
						    	<input type="text" class="width342" tabindex="1" id="email" name="email" value="<? echo $email ?>">
					    	</div>
					    	<div class="width356">
								Для регистрации на сайте укажите свой адрес электронной почты. Пароль для входа будет сгенерирован автоматически.
					    	</div>
				    	</div>
			    	</div>

			    	<div class="margined-line-34">
				    	<div class="clearfix padded-top-25">
					    		<div class="pull-right">
									<input id="submit" name="submit" class="btn btn-primary" type="submit" value="Получить пароль" />
					    		</div>
					    	</div>
			    	</div>
				</form>
				
			</div>

		</div>
	</body>
</html>