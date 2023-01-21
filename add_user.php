<? include_once("include/common.php") ?>

<?
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
if(isset($_POST['submit']))
{
	$email = $_POST['email'];
    $query = mysql_query("SELECT id, psw FROM user WHERE active = 1 AND login='".mysql_real_escape_string($_POST['email'])."' LIMIT 1");
    $data = mysql_fetch_assoc($query);
	if(mysql_num_rows($query) > 0){
		$errorMessage = "Пользователь с таким Email уже существует.";
	}
	else{
        $password = generateCode(6);
		mysql_query("INSERT INTO user (active, login, psw) VALUES(1, '".mysql_real_escape_string($_POST['email'])."', '".md5(md5($password))."')");
	}
}
else
{
        //$ur=$_SERVER['REQUEST_URI'];
        $ur=$_SERVER['HTTP_REFERER'];
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Добавление пользователя</title>
	<meta charset="utf-8">
	<link href="css/structure.css" rel="stylesheet">
	<link href="css/form.css" rel="stylesheet">
	<link href="css/base.css" rel="stylesheet">
	<script src="scripts/wufoo.js"></script>

	<!--[if lt IE 10]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body id="public">
	<div id="container" class="ltr">
		<h1 id="logo">&nbsp;</h1>
		<form id="form56" name="form56" class="wufoo topLabel page" autocomplete="off" enctype="multipart/form-data" method="post" novalidate action="">
			<header id="header" class="info">
				<h2>Добавление пользователя</h2>
				<div></div>
			</header>
<?php include("include/notification.php") ?>
			<ul>
				<li class="notranslate">
					<label class="desc" id="title0" for="name">Email</label>
					<span>
						<input id="email" name="email" type="text" class="field text fn" value="<? echo $email ?>" size="50" tabindex="1" />
					</span>
				</li>
<?
if(isset($_POST['submit']))
{
?>
				<li class="notranslate">
					<label class="desc" id="title0" for="name">Пароль</label>
					<span>
						<? echo $password ?>
					</span>
				</li>
<?
}
?>
				<li class="buttons ">
					<div>
						<input id="submit" name="submit" class="btTxt submit" type="submit" value="Добавить" />
						<input id="login" name="login" class="btTxt submit" type="button" onClick="location.href='login.php'" value="Войти" />
					</div>
				</li>
			</ul>
		</form> 
	</div><!--container-->

</body>
</html>