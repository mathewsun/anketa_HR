<?php
include_once("include/common.php");
include_once("include/security.php");

function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}

function checkAnketa($anketa_data){
	$anketa_id = $anketa_data['id'];
	$errors_list = "";
	if ($anketa_data['last_name'] == '')
	{
		$errors_list .= ", фамилия";
	}
	if ($anketa_data['first_name'] == '')
	{
		$errors_list .= ", имя";
	}
	if ($anketa_data['middle_name'] == '')
	{
		$errors_list .= ", отчество";
	}
	if ($anketa_data['gender'] == '')
	{
		$errors_list .= ", пол";
	}
	if ($anketa_data['name_not_changed'] == '0' || $anketa_data['name_not_changed'] == 0)
	{
		if ($anketa_data['old_last_name'] == '')
		{
			$errors_list .= ", старая фамилия";
		}
		if ($anketa_data['old_first_name'] == '')
		{
			$errors_list .= ", старое имя";
		}
		if ($anketa_data['old_middle_name'] == '')
		{
			$errors_list .= ", старое отчество";
		}
		if ($anketa_data['name_changed_date'] == '0000-00-00' || $anketa_data['name_changed_date'] == '')
		{
			$errors_list .= ", дата смены фамилии";
		}
		if ($anketa_data['name_changed_place'] == '')
		{
			$errors_list .= ", место смены фамилии";
		}
		if ($anketa_data['name_changed_reason'] == '')
		{
			$errors_list .= ", причина смены фамилии";
		}
	}
	if ($anketa_data['birthday'] == '0000-00-00' || $anketa_data['birthday'] == '')
	{
		$errors_list .= ", дата рождения";
	}
	if ($anketa_data['birth_place'] == '')
	{
		$errors_list .= ", место рождения";
	}
	if ($anketa_data['citizenship_not_changed'] == '0' || $anketa_data['citizenship_not_changed'] == 0)
	{
		if ($anketa_data['citizenship_changed_date'] == '0000-00-00' || $anketa_data['citizenship_changed_date'] == '')
		{
			$errors_list .= ", дата смены гражданства";
		}
		if ($anketa_data['citizenship_changed_reason'] == '')
		{
			$errors_list .= ", причина смены гражданства";
		}
	}
	if (strlen($errors_list) > 0)
	{
		$errors_list = substr($errors_list, 2);
		$errors_list = "На <a href='step1.php'>Шаге 1</a> не заполнены поля: ".$errors_list.".<br>";
	}

	$query = mysql_query("
				SELECT e.* 
				FROM education e 
				WHERE e.anketa_id=".$anketa_id);
	if(mysql_num_rows($query) == 0){
		$errors_list .= "На <a href='step2.php'>Шаге 2</a> не заполнено образование.<br>";
	}

	$query = mysql_query("
				SELECT r.* 
				FROM relatives r 
				WHERE r.anketa_id=".$anketa_id);
	if(mysql_num_rows($query) == 0){
		$errors_list .= "На <a href='step4.php'>Шаге 4</a> не заполнены родственники.<br>";
	}
	$has_spouse = false;
	$has_mother = false;
	$has_father = false;
	while ($row = mysql_fetch_assoc($query)) {
		if ($row['relation_id'] == 5 || $row['relation_id'] == '5')
		{
			$has_mother = true;
		}
		if ($row['relation_id'] == 7 || $row['relation_id'] == '7')
		{
			$has_father = true;
		}
		if ($anketa_data['gender'] == 'M' && ($row['relation_id'] == 4 || $row['relation_id'] == '4'))
		{
			$has_spouse = true;
		}
		if ($anketa_data['gender'] == 'F' && ($row['relation_id'] == 6 || $row['relation_id'] == '6'))
		{
			$has_spouse = true;
		}
	}
	mysql_free_result($query);
	if ($anketa_data['marital_status_id'] == 0 || $anketa_data['marital_status_id'] == '0' || $anketa_data['marital_status_id'] == '')
	{
		$errors_list .= "На <a href='step4.php'>Шаге 4</a> информация о семейном положении обязательна к заполнениею.<br>";
	}
	if (!$has_mother || !$has_father)
	{
		$errors_list .= "На <a href='step4.php'>Шаге 4</a> информация об отце и матери обязательна к заполнениею.<br>";
	}
	if (!$has_spouse && ($anketa_data['marital_status_id'] == 2 || $anketa_data['marital_status_id'] == '2'))
	{
		$errors_list .= "На <a href='step4.php'>Шаге 4</a> информация о супруге обязательна к заполнениею.<br>";
	}

	if ($anketa_data['internal_document_id'] == '0' || $anketa_data['internal_document_id'] == '')
	{
		$errors_list .= "На <a href='step5.php'>Шаге 5</a> информация паспорта обязательна к заполнениею.<br>";
	}
	if ($anketa_data['registration_address_id'] == '0' || $anketa_data['registration_address_id'] == '')
	{
		$errors_list .= "На <a href='step5.php'>Шаге 5</a> информация адреса регистрации обязательна к заполнениею.<br>";
	}

	if (strlen($errors_list) > 0)
	{
		return $errors_list;
	}

	return "";
}

$query = mysql_query("
			SELECT a.* 
			FROM anketa a 
			WHERE a.id=".$anketa_id." LIMIT 1");
if(mysql_num_rows($query) == 0){
	exit;
}
$data = mysql_fetch_assoc($query);

$complete = false;
$mail_sent = false;
if(isset($_GET['done']) && $_GET['done'] == "1")
{
	$chekResult = checkAnketa($data);
	if($chekResult == "")
	{
		$file = generateCode(15).".xml";
		$myString = generateXML($anketa_id);
		if (file_put_contents("anketa_files/".$file, $myString) !== false){
			$my_file = $file;
			$my_path = $_SERVER['DOCUMENT_ROOT']."/anketa_files/";
			$my_name = "ABS Group";
			$my_mail = "noreply@absgroup.ru";
			$my_replyto = "noreply@absgroup.ru";
			$my_subject = "Новая анкета от ".$data['last_name']." ".$data['first_name']." ".$data['middle_name'];
			$my_message = "Новая анкета от ".$data['last_name']." ".$data['first_name']." ".$data['middle_name'];
			$mail_sent = mail_attachment($my_file, $my_path, "zupuser@absgroup.ru", $my_mail, $my_name, $my_replyto, $my_subject, $my_message);

			mysql_query("UPDATE user SET anketa_file = '".$file."', ankeda_date = NOW(), full_name='".$data['last_name']." ".$data['first_name']." ".$data['middle_name']."', birthday='".$data['birthday']."' WHERE id = '".$_SESSION['id']."'");

			/*
			mysql_query("UPDATE user SET active = 0, anketa_file = '".$file."', ankeda_date = NOW(), full_name='".$data['last_name']." ".$data['first_name']." ".$data['middle_name']."', birthday='".$data['birthday']."' WHERE id = '".$_SESSION['id']."'");
			
			mysql_query("DELETE FROM address WHERE id IN (SELECT address_id FROM relatives WHERE anketa_id=".$anketa_id.")");
			mysql_query("DELETE FROM address WHERE id IN (SELECT registration_address_id FROM anketa WHERE id=".$anketa_id.")");
			mysql_query("DELETE FROM address WHERE id IN (SELECT residence_address_id FROM anketa WHERE id=".$anketa_id.")");
			mysql_query("DELETE FROM document WHERE id IN (SELECT internal_document_id FROM anketa WHERE id=".$anketa_id.")");
			mysql_query("DELETE FROM document WHERE id IN (SELECT foreign_document_id FROM anketa WHERE id=".$anketa_id.")");

			mysql_query("DELETE FROM additional_education WHERE anketa_id=".$anketa_id);
			mysql_query("DELETE FROM education WHERE anketa_id=".$anketa_id);
			mysql_query("DELETE FROM language_skills WHERE anketa_id=".$anketa_id);
			mysql_query("DELETE FROM relatives WHERE anketa_id=".$anketa_id);
			mysql_query("DELETE FROM work_activity WHERE anketa_id=".$anketa_id);
			mysql_query("DELETE FROM anketa WHERE id=".$anketa_id);

			$_SESSION['id']="";
			$_SESSION['hash']="";
			session_unset();
			session_destroy();
			*/
			
			$complete = true;
		}
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Анкета заполнена</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src='js/dhtmlxmessage.js'></script>
        <script src="js/site.js"></script>
		<link rel="stylesheet" type="text/css" href="stylesheets/dhtmlxmessage_dhx_skyblue.css">
		<script language="javascript">
			$(document).ready(function() {
			});
			
			function doLogout(){
				window.location.href = "logout.php";
			}

			function stepDone(){
				if (!$('#agree').is(":checked")){
					alert_error("Вы не согласились на проверку указанных сведений");
					return;
				}
				location.href='finish.php?done=1'
			}

		</script>
	</head>
	<body>
		<div id="top-border">&nbsp;</div>
		<div id="page">
			<div id="header-row"></div>
			<div id="page-container">
<?
				if ($chekResult != ""){
?>
					<div id="warning-message">
						Ошибки заполнения анкеты:<br>
					</div>
					<div class="margined-line-34">
						<? echo $chekResult ?>
					</div>
					<div class="margined-line-34">
							<div class="clearfix padded-top-25">
									<div class="pull-left blue-text">
										<button class="btn" type="button" tabindex="27" onClick="doLogout();return false;">Выход</button>
									</div>
									<div class="pull-right">
										<button class="btn btn-primary" type="button" tabindex="25" onClick="location.href='step1.php';return false;">Шаг 1</button>
										<button class="btn btn-primary" type="button" tabindex="25" onClick="location.href='step2.php';return false;">Шаг 2</button>
										<button class="btn btn-primary" type="button" tabindex="25" onClick="location.href='step3.php';return false;">Шаг 3</button>
										<button class="btn btn-primary" type="button" tabindex="25" onClick="location.href='step4.php';return false;">Шаг 4</button>
										<button class="btn btn-primary" type="button" tabindex="25" onClick="location.href='step5.php';return false;">Шаг 5</button>
									</div>
								</div>
					</div>
<?
				}
				elseif ($complete && !$mail_sent){
?>
					<div id="warning-message">
						Проблемы с отправкой анкеты!!
					</div>
					<div class="margined-line-34">
						<div class="clearfix padded-top-25">
								<div class="pull-left blue-text">
									<button class="btn" type="button" tabindex="27" onClick="location.href='login.php';return false;">Выход</button>
								</div>
							</div>
					</div>
<?
				}
				else if ($complete){
?>
					<div id="warning-message">
						Спасибо. Анкета отправлена!
					</div>
					<div class="margined-line-34">
						<div class="clearfix padded-top-25">
								<div class="pull-left blue-text">
									<button class="btn" type="button" tabindex="27" onClick="location.href='login.php';return false;">Выход</button>
								</div>
							</div>
					</div>
<?
				}
				else{
?>
					<div id="warning-message">
						Спасибо. Анкета заполнена!
					</div>
					<form>
						<h2>Как передать результаты:</h2>
						<div class="margined-line-34">
							<ol>
								<li>
									Для формирования анкеты нажмите на кнопку "Отправить"
								</li>
								<li>
									Ваша анкета будет отправлена нашим менеджерам
								</li>
								<li>
									Все личные данные будут удалены.
								</li>
							</ol>
							<label class="checkbox">
								<input type="checkbox" id="agree" name="agree" value="">
								<span class="blue-text font16">Настоящим выражаю согласие на передачу персональных данных, указанных в Анкете, 
								третьим лицам в целях проверки их достоверности. Я понимаю, что указание недостоверных данных может 
								быть основанием для отказа в приеме на работу.</span>
							</label>
							<div class="clearfix padded-top-25">
									<div class="pull-left blue-text">
										<button class="btn" type="button" tabindex="27" onClick="doLogout();return false;">Выход</button>
									</div>
									<div class="pull-right">
										<button class="btn btn-primary" type="button" tabindex="25" onClick="location.href='step5.php';return false;">&laquo; Назад</button>
										<button class="btn btn-primary" type="button" tabindex="26" onClick="stepDone();return false;">Отправить</button>
									</div>
								</div>
						</div>
					</form>
<?
				}
?>
			</div>

		</div>
	</body>
</html>