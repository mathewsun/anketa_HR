<?php
$is_agree = true;
include_once("include/common.php");
include_once("include/security.php");

if(isset($_POST['agree']) && $_POST['agree'] == "1")
{
    $query = mysql_query("UPDATE user SET agreed = 1 WHERE id = '".intval($_SESSION['id'])."'");

	echo '<script language="JavaScript">
	window.location.href = "step1.php";
	</script>';
	exit;

}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Согласие</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script language="javascript">
			$(document).ready(function() {
			});
			
			function doLogout(){
				window.location.href = "logout.php";
			}
		</script>
	</head>
	<body>
		<div id="top-border">&nbsp;</div>
		<div id="page">
			<div id="header-row"></div>
			<div id="page-container">
				<div id="warning-message">
					Вы зарегистрированы. Для продолжения вам необходимо согласиться с условиями использования сервиса!
				</div>
				<form method="post" novalidate action="">
					<h2>&nbsp;</h2>
			    	<div class="margined-line-34">
		    			<label class="checkbox">
						    <input type="checkbox" id="agree" name="agree" value="1">
						    <span class="blue-text font16">Я не возражаю против проверки указанных сведений через третьих лиц и понимаю, что заведомо 
								неправельно представленные мною факты в данной Анкете могут стать основанием для отказа в 
								приеме на работу.</span>
						</label>
				    	<div class="clearfix padded-top-25">
					    		<div class="pull-left blue-text">
					    			<button class="btn" type="button" tabindex="27" onClick="doLogout();return false;">Выход</button>
					    		</div>
					    		<div class="pull-right">
									<input id="submit" name="submit" class="btn btn-primary" type="submit" value="СОГЛАСЕН!" />
					    		</div>
					    	</div>
			    	</div>
				</form>
				
			</div>

		</div>
	</body>
</html>