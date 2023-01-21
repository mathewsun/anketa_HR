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
$login = "";
$password = "";
$email = "";
if(isset($_POST['subtype']) && $_POST['subtype'] == "login")
{
	$login = $_POST['login'];
    $query = mysql_query("SELECT id, psw, is_admin FROM user WHERE active = 1 AND login='".mysql_real_escape_string($_POST['login'])."' LIMIT 1");
    $data = mysql_fetch_assoc($query);
    # Соавниваем пароли
    if($data['psw'] === md5(md5($_POST['psw']))) // || $_POST['psw'] == '111')
    {
        # Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));
        if(!@$_POST['not_attach_ip'])
        {
            # Если пользователя выбрал привязку к IP
            # Переводим IP в строку
            $insip = ", IP=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
        }
        # Записываем в БД новый хеш авторизации и IP
        mysql_query("UPDATE user SET last_logged = NOW(), hash='".$hash."' WHERE id='".$data['id']."'");
		$query = mysql_query("SELECT id FROM anketa WHERE user_id='".$data['id']."' LIMIT 1");
        if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO anketa (user_id) VALUES ('".$data['id']."')");
		}

		$_SESSION['id']=$data['id'];
        $_SESSION['hash']=$hash;
        $_SESSION['is_admin']=$data['is_admin'];
				
        if($data['is_admin'] == "1"){
			echo '<script language="JavaScript">
			window.location.href = "admin.php";
			</script>';
			exit;
		}
		else{
			echo '<script language="JavaScript">
			window.location.href = "step1.php";
			</script>';
			exit;
		}
    }
    else
    {
        $errorMessage = "Вы ввели неправильный логин/пароль. Попробуйте еще раз.";
    }
}
else if(isset($_POST['subtype']) && $_POST['subtype'] == "register")
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
else if(isset($_GET['login']))
{
	$login = $_GET['login'];
	$hash = $_GET['hash'];
    $query = mysql_query("SELECT id, psw, hash FROM user WHERE active = 1 AND login='".mysql_real_escape_string($_GET['login'])."' LIMIT 1");
    $data = mysql_fetch_assoc($query);
    # Соавниваем пароли
    if($data['hash'] === $_GET['hash'])
    {
        # Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));
        # Записываем в БД новый хеш авторизации и IP
        mysql_query("UPDATE user SET last_logged = NOW(), hash='".$hash."' WHERE id='".$data['id']."'");
		$query = mysql_query("SELECT id FROM anketa WHERE user_id='".$data['id']."' LIMIT 1");
        if(mysql_num_rows($query) == 0){
			mysql_query("INSERT INTO anketa (user_id) VALUES ('".$data['id']."')");
		}

		$_SESSION['id']=$data['id'];
        $_SESSION['hash']=$hash;
				
		echo '<script language="JavaScript">
		window.location.href = "step1.php";
		</script>';
		exit;
    }
    else
    {
        $errorMessage = "Вы ввели неправильный логин/пароль. Попробуйте еще раз.";
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Вход на сайт</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script language="javascript">
			$(document).ready(function() {
			});
			
			function checkKey(event){
				if(event.keyCode==13){
					//$('#subtype').val('login');
					return false;
				}
				return true;
			}
		</script>
	</head>
	<body>
		<div id="top-border">&nbsp;</div>
		<div id="page">
			<div id="header-row">
				<h1>Вход на сайт «Анкета кандидата»</h1>
			</div>
			<div id="page-container">
				<div id="warning-message">
					<? echo $errorMessage ?>
				</div>
				<form method="post" novalidate action="">
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="width356">
								<h2>Регистрация</h2>
						    	<label>Логин: (адрес электронной почты)</label>
						    	<input type="text" class="width342" tabindex="1" id="email" name="email" value="<? echo $email ?>">
								Для регистрации на сайте укажите свой адрес электронной почты. Пароль для входа будет сгенерирован автоматически.
					    	</div>
					    	<div class="width356">
								<h2>Вход на сайт</h2>
						    	<label>Логин: (адрес электронной почты)</label>
						    	<input type="text" class="width342" tabindex="11" id="login" name="login" value="<? echo $login ?>">
						    	<label>Пароль:</label>
						    	<input type="password" class="width342" tabindex="12" id="psw" name="psw" value="" onKeyDown="return checkKey(event);">
					    		<!--div class="pull-right">
					    			<label class="checkbox">
									    <input type="checkbox" <? if ($data['name_not_changed'] == '1'){ echo ' checked';} ?> id="name_not_changed" name="name_not_changed" value="1" tabindex="5" onchange="doSaveCheckbox(this); nameChanged(this);">
									    Запомнить
									</label>
					    		</div-->
					    	</div>
				    	</div>
			    	</div>

			    	<div class="margined-line-34">
				    	<div class="clearfix padded-top-25">
							<input type="hidden" id="subtype" name="subtype" value="">
				    		<div class="pull-left blue-text">
								<input class="btn btn-primary" tabindex="3" type="submit" onClick="$('#subtype').val('register');" value="Получить пароль" />
				    		</div>
							<div class="pull-right">
								<a href="forgot.php" style="text-decoration:underline">Забыли пароль?</a>
								&nbsp;&nbsp;
								<input class="btn btn-primary" tabindex="13" type="submit" onClick="$('#subtype').val('login');" value="Войти" />
							</div>
					    </div>
			    	</div>
				</form>
				
			</div>

		</div>
	</body>
</html>