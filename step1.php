<?php
include_once("include/common.php");
include_once("include/security.php");

$query = mysql_query("
			SELECT a.* 
			FROM anketa a 
			WHERE a.id=".$anketa_id." LIMIT 1");
if(mysql_num_rows($query) == 0){
	exit;
}
$data = mysql_fetch_assoc($query);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Общая информация</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src='js/dhtmlxmessage.js'></script>
        <script src="js/site.js"></script>
		<link rel="stylesheet" type="text/css" href="stylesheets/dhtmlxmessage_dhx_skyblue.css">
		<script language="javascript">
			$(document).ready(function() {
				for (i = new Date().getFullYear(); i >= 1900; i--)
				{
					$('#name_changed_date_y').append($('<option />').val(i).html(i));
					$('#birthday_y').append($('<option />').val(i).html(i));
					$('#citizenship_changed_date_y').append($('<option />').val(i).html(i));
				}				
				for (i = 1; i <= 31; i++)
				{
					show = '' + i;
					if ( i<10 ){
						show = '0' + i;
					}
					$('#name_changed_date_d').append($('<option />').val(show).html(show));
					$('#birthday_d').append($('<option />').val(show).html(show));
					$('#citizenship_changed_date_d').append($('<option />').val(show).html(show));
				}				
				name_changed_date_arr = '<?php echo( $data['name_changed_date'] ) ?>'.split("-");
				$('#name_changed_date_y').val(name_changed_date_arr[0]);
				$('#name_changed_date_m').val(name_changed_date_arr[1]);
				$('#name_changed_date_d').val(name_changed_date_arr[2]);

				birthday_arr = '<?php echo( $data['birthday'] ) ?>'.split("-");
				$('#birthday_y').val(birthday_arr[0]);
				$('#birthday_m').val(birthday_arr[1]);
				$('#birthday_d').val(birthday_arr[2]);

				citizenship_changed_date_arr = '<?php echo( $data['citizenship_changed_date'] ) ?>'.split("-");
				$('#citizenship_changed_date_y').val(citizenship_changed_date_arr[0]);
				$('#citizenship_changed_date_m').val(citizenship_changed_date_arr[1]);
				$('#citizenship_changed_date_d').val(citizenship_changed_date_arr[2]);
				
<?
				if ($_SESSION['is_admin'] == "1")
				{
?>
					disableForm(document.forms[0]);
<?
				}
?>
			});
			
			function disableForm(form) {
				var length = form.elements.length;
				for (i=0; i < length; i++) {
					if (form.elements[i].name != "exit" && form.elements[i].name != "next")
					{
						form.elements[i].disabled = true;
					}
				}
			}

			function stepDone(){
<?
				if ($_SESSION['is_admin'] == "1")
				{
?>
					location.href='step2.php?aid=<? echo $anketa_id ?>';
<?
				}
				else
				{
?>
					fields = "";
					if ($('#last_name').val() == ''){
						fields += ", Фамилия";
					}
					if ($('#first_name').val() == ''){
						fields += ", Имя";
					}
					if ($('#middle_name').val() == ''){
						fields += ", Отчество";
					}
					if ($("input[name='gender']:checked").val() != 'M' && $("input[name='gender']:checked").val() != 'F'){
						fields += ", Пол";
					}
					if (!$('#name_not_changed').is(":checked")){
						if ($('#old_last_name').val() == ''){
							fields += ", Старая фамилия";
						}
						if ($('#old_first_name').val() == ''){
							fields += ", Старое имя";
						}
						if ($('#old_middle_name').val() == ''){
							fields += ", Старое отчество";
						}
						if ($('#name_changed_date_d').prop('selectedIndex') < 1 || $('#name_changed_date_m').prop('selectedIndex') < 1 || $('#name_changed_date_y').prop('selectedIndex') < 1){
							fields += ", Дата смены фамилии";
						}
						if ($('#name_changed_place').val() == ''){
							fields += ", Место смены фамилии";
						}
						if ($('#name_changed_reason').val() == ''){
							fields += ", Причина смены фамилии";
						}
					}
					if ($('#birthday_d').prop('selectedIndex') < 1 || $('#birthday_m').prop('selectedIndex') < 1 || $('#birthday_y').prop('selectedIndex') < 1){
						fields += ", Дата рождения";
					}
					if ($('#birth_place').val() == ''){
						fields += ", Место рождения";
					}
					if ($("input[name='citizenship_country_s']:checked").val() == 'other' && $('#citizenship_country').val() == ''){
						fields += ", Гражданство";
					}
					if (!$('#citizenship_not_changed').is(":checked")){
						if ($('#citizenship_changed_date_d').prop('selectedIndex') < 1 || $('#citizenship_changed_date_m').prop('selectedIndex') < 1 || $('#citizenship_changed_date_y').prop('selectedIndex') < 1){
							fields += ", Дата смены гражданства";
						}
						if ($('#citizenship_changed_reason').val() == ''){
							fields += ", Причина смены гражданства";
						}
					}
					if (fields.length > 0){
						alert_error("Вы не заполнили следующие поля: " + fields.substring(2));
						return;
					}

					nowDateObj = new Date();
					birthdayDateObj = new Date($('#birthday_y').val(), $('#birthday_m').val()-1, $('#birthday_d').val() );
					//if(birthdayDateObj.getFullYear() != parseInt($('#birthday_y').val()) || birthdayDateObj.getMonth() != parseInt($('#birthday_m').val())-1 || birthdayDateObj.getDate() != parseInt($('#birthday_d').val())){
					//	alert_error("Некорректная дата рождения");
					//	return;
					//}
					if((nowDateObj-birthdayDateObj) / (24*60*60*1000*365) < 18){
						alert_error("Вам еще нет 18 лет");
						return;
					}
					if (!$('#name_not_changed').is(":checked")){
						nameChangedDateObj = new Date($('#name_changed_date_y').val(), $('#name_changed_date_m').val()-1, $('#name_changed_date_d').val() );
						//if(nameChangedDateObj.getFullYear() != parseInt($('#name_changed_date_y').val()) || nameChangedDateObj.getMonth() != parseInt($('#name_changed_date_m').val())-1 || nameChangedDateObj.getDate() != parseInt($('#name_changed_date_d').val())){
						//	alert_error("Некорректная дата смены фамилии");
						//	return;
						//}
						if((nameChangedDateObj-birthdayDateObj) < 0){
							alert_error("Дата смены фамилии не может быть раньше даты рождения");
							return;
						}
					}
					if (!$('#citizenship_not_changed').is(":checked")){
						citizenshipChangedDateObj = new Date($('#citizenship_changed_date_y').val(), $('#citizenship_changed_date_m').val()-1, $('#citizenship_changed_date_d').val() );
						//if(citizenshipChangedDateObj.getFullYear() != parseInt($('#citizenship_changed_date_y').val()) || citizenshipChangedDateObj.getMonth() != parseInt($('#citizenship_changed_date_m').val())-1 || citizenshipChangedDateObj.getDate() != parseInt($('#citizenship_changed_date_d').val())){
						//	alert_error("Некорректная дата смены гражданства");
						//	return;
						//}
						if((citizenshipChangedDateObj-birthdayDateObj) < 0){
							alert_error("Дата смены гражданства не может быть раньше даты рождения");
							return;
						}
					}
					location.href='step2.php';
<?
				}
?>
				
			}
			
<?
			if ($_SESSION['is_admin'] == "1")
			{
?>
				function doLogout(){
					window.location.href = "admin.php";
				}

<?
			}
			else
			{
?>
				function doSave( isAjax, obj){
					nameVal = obj.name
					valueVal = obj.value;
					if (isAjax){
						nameVal = obj.prop("name");
						valueVal = obj.value;
					}
					$.post("process.php", { step: '1', name: nameVal, value: valueVal } );
				}

				function doSaveCheckbox(obj){
					if(obj.checked){
						$.post("process.php", { step: '1', name: obj.name, value: "1" } );
					}
					else{
						$.post("process.php", { step: '1', name: obj.name, value: "0" } );
					}
				}
				
				function nameChanged(obj){
					if(obj.checked){
						$('#old_first_name').val('');
						$('#old_last_name').val('');
						$('#old_middle_name').val('');
						$('#name_changed_place').val('');
						$('#name_changed_reason').val('');
						$('#name_changed_date_y').prop('selectedIndex', 0);
						$('#name_changed_date_m').prop('selectedIndex', 0);
						$('#name_changed_date_d').prop('selectedIndex', 0);

						doSave( true, $('#old_first_name'));
						doSave( true, $('#old_last_name'));
						doSave( true, $('#old_middle_name'));
						doSave( true, $('#name_changed_place'));
						doSave( true, $('#name_changed_reason'));
						$.post("process.php", { step: '1', name: 'name_changed_date', value: '0000-00-00' } );

						$('#old_first_name').prop('disabled', true);
						$('#old_last_name').prop('disabled', true);
						$('#old_middle_name').prop('disabled', true);
						$('#name_changed_place').prop('disabled', true);
						$('#name_changed_reason').prop('disabled', true);
						$('#name_changed_date_y').prop('disabled', true);
						$('#name_changed_date_m').prop('disabled', true);
						$('#name_changed_date_d').prop('disabled', true);
					}
					else{
						$('#old_first_name').prop('disabled', false);
						$('#old_last_name').prop('disabled', false);
						$('#old_middle_name').prop('disabled', false);
						$('#name_changed_place').prop('disabled', false);
						$('#name_changed_reason').prop('disabled', false);
						$('#name_changed_date_y').prop('disabled', false);
						$('#name_changed_date_m').prop('disabled', false);
						$('#name_changed_date_d').prop('disabled', false);
					}
				}
				
				function citizenshipChanged(obj){
					if(obj.checked){
						$('#citizenship_changed_reason').val('');
						$('#citizenship_changed_date_y').prop('selectedIndex', 0);
						$('#citizenship_changed_date_m').prop('selectedIndex', 0);
						$('#citizenship_changed_date_d').prop('selectedIndex', 0);

						doSave( true, $('#citizenship_changed_reason'));
						$.post("process.php", { step: '1', name: 'citizenship_changed_date', value: '0000-00-00' } );

						$('#citizenship_changed_reason').prop('disabled', true);
						$('#citizenship_changed_date_y').prop('disabled', true);
						$('#citizenship_changed_date_m').prop('disabled', true);
						$('#citizenship_changed_date_d').prop('disabled', true);
					}
					else{
						$('#citizenship_changed_reason').prop('disabled', false);
						$('#citizenship_changed_date_y').prop('disabled', false);
						$('#citizenship_changed_date_m').prop('disabled', false);
						$('#citizenship_changed_date_d').prop('disabled', false);
					}
				}
				
				function doSaveDate(objName){
					objYvalue = $('#' + objName + '_y').val();
					objMvalue = $('#' + objName + '_m').val();
					objDvalue = $('#' + objName + '_d').val();
					if (objYvalue != '' && objMvalue != '' && objDvalue != '' && objYvalue != null && objMvalue != null && objDvalue != null){
						birthdayDateObj = new Date(objYvalue, objMvalue-1, objDvalue );
						//if(birthdayDateObj.getFullYear() != parseInt(objYvalue) || birthdayDateObj.getMonth() != parseInt(objMvalue)-1 || birthdayDateObj.getDate() != parseInt(objDvalue)){
						//	alert_error("Некорректная дата.");
						//	return;
						//}

						objValue = '' + objYvalue + '-' + objMvalue + '-' + objDvalue;
						$.post("process.php", { step: '1', name: objName, value: objValue } );
					}
				}
				
				function citizenshipSelect(isRussia){
					if (isRussia){
						$('#citizenship_country').val('');
						$('#citizenship_country').prop('disabled', true);
						doSave( true, $('#citizenship_country'));
					}
					else{
						$('#citizenship_country').prop('disabled', false);
					}
				}

				function doLogout(){
					window.location.href = "logout.php";
				}
<?
			}
?>

		</script>
	</head>
	<body>
		<div id="top-border">&nbsp;</div>
		<div id="page">
			<div id="header-row">
				<h1>Общая информация <i>(шаг 1 из 5)</i> <span class="to_right">Внимание! Все поля анкеты должны быть заполнены.</span></h1>
			</div>
			<div id="page-container">
				<div id="warning-message">
					
				</div>
				<form>
					<h2>Персональная информация</h2>
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="width356">
						    	<label>Фамилия</label>
						    	<input type="text" class="width342" tabindex="1" id="last_name" name="last_name" value="<? echo $data['last_name'] ?>" onchange="doSave(false, this)">
						    	<label>Отчество</label>
						    	<input type="text" class="width342" tabindex="3" id="middle_name" name="middle_name" value="<? echo $data['middle_name'] ?>" onchange="doSave(false, this)">
					    	</div>
					    	<div class="width356">
						    	<label>Имя</label>
						    	<input type="text" class="width342" tabindex="2" id="first_name" name="first_name" value="<? echo $data['first_name'] ?>" onchange="doSave(false, this)">
						    	<label>Пол</label>
								<label class="radio position1">
								    <input type="radio" id="gender" name="gender" value="M" tabindex="4" <? if ($data['gender'] == 'M'){ echo ' checked';} ?> onClick="doSave(false, this)">Мужской
								</label>
								<label class="radio position1">
								    <input type="radio" id="gender" name="gender" value="F" tabindex="4" <? if ($data['gender'] == 'F'){ echo ' checked';} ?> onClick="doSave(false, this)">Женский
								</label>
					    	</div>
				    	</div>
			    	</div>

				    <h2>Смена фамилии</h2>
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="clearfix">
					    		<div class="pull-left blue-text">
					    			Если изменяли фамилию, имя или отчество, то укажите их, а также когда, где и по какой причине.
					    		</div>
					    		<div class="pull-right">
					    			<label class="checkbox">
									    <input type="checkbox" <? if ($data['name_not_changed'] == '1'){ echo ' checked';} ?> id="name_not_changed" name="name_not_changed" value="1" tabindex="5" onchange="doSaveCheckbox(this); nameChanged(this);">
									    Не менял(а)
									</label>
					    		</div>
					    	</div>
					    </div>
					    <div class="row-fluid">
					    	<div class="width356">
						    	<label>Старая фамилия</label>
						    	<input type="text" class="width342" tabindex="6" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> id="old_last_name" name="old_last_name" value="<? echo $data['old_last_name'] ?>" onchange="doSave(false, this)">
						    	<label>Старое отчество</label>
						    	<input type="text" class="width342" tabindex="8" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> id="old_middle_name" name="old_middle_name" value="<? echo $data['old_middle_name'] ?>" onchange="doSave(false, this)">
					    	</div>
					    	<div class="width356">
						    	<label>Старое имя</label>
						    	<input type="text" class="width342" tabindex="7" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> id="old_first_name" name="old_first_name" value="<? echo $data['old_first_name'] ?>" onchange="doSave(false, this)">
						    	<label>Когда меняли</label>
								<select class="day" tabindex="9" id="name_changed_date_d" name="name_changed_date_d" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> onChange="doSaveDate('name_changed_date')">
									<option value=""></option>
								</select>
								<select class="month" tabindex="10" id="name_changed_date_m" name="name_changed_date_m" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> onChange="doSaveDate('name_changed_date')">
									<option value=""></option>
									<option value="01">Январь
									</option>
									<option value="02">Февраль
									</option>
									<option value="03">Март
									</option>
									<option value="04">Апрель
									</option>
									<option value="05">Май
									</option>
									<option value="06">Июнь
									</option>
									<option value="07">Июль
									</option>
									<option value="08">Август
									</option>
									<option value="09">Сентябрь
									</option>
									<option value="10">Октябрь
									</option>
									<option value="11">Ноябрь
									</option>
									<option value="12">Декабрь
									</option>
								</select>
								<select class="year" tabindex="11" id="name_changed_date_y" name="name_changed_date_y" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> onChange="doSaveDate('name_changed_date')">
									<option value=""></option>
								</select>
					    	</div>
				    	</div>
				    	<label>Место смены</label>
				    	<input type="text" class="width816" tabindex="12" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> id="name_changed_place" name="name_changed_place" value="<? echo $data['name_changed_place'] ?>" onchange="doSave(false, this)">
				    	<label>Причина</label>
				    	<input type="text" class="width816" tabindex="13" <? if ($data['name_not_changed'] == '1'){ echo ' disabled';} ?> id="name_changed_reason" name="name_changed_reason" value="<? echo $data['name_changed_reason'] ?>" onchange="doSave(false, this)">
			    	</div>

				    <h2>Дата и место рождения</h2>
			    	<div class="margined-line-34">
				    	<label>Дата рождения</label>
						<select class="day" id="birthday_d" name="birthday_d" tabindex="14" onChange="doSaveDate('birthday')">
							<option value=""></option>
						</select>
						<select class="month" tabindex="15" id="birthday_m" name="birthday_m" onChange="doSaveDate('birthday')">
							<option value=""></option>
							<option value="01">Январь
							</option>
							<option value="02">Февраль
							</option>
							<option value="03">Март
							</option>
							<option value="04">Апрель
							</option>
							<option value="05">Май
							</option>
							<option value="06">Июнь
							</option>
							<option value="07">Июль
							</option>
							<option value="08">Август
							</option>
							<option value="09">Сентябрь
							</option>
							<option value="10">Октябрь
							</option>
							<option value="11">Ноябрь
							</option>
							<option value="12">Декабрь
							</option>
						</select>
						<select  class="year" tabindex="16" id="birthday_y" name="birthday_y" onChange="doSaveDate('birthday')">
							<option value=""></option>
						</select>
				    	<label>Место рождения</label>
				    	<input type="text" class="width816" tabindex="17" id="birth_place" name="birth_place" value="<? echo $data['birth_place'] ?>" onchange="doSave(false, this)">
			    	</div>

				    <h2>Гражданство</h2>
			    	<div class="margined-line-34">
						<div class="row-fluid">
					    	<div class="width356">
								<label class="radio position1">
								    <input type="radio" tabindex="18" id="citizenship_country_s" name="citizenship_country_s" value="russia" <? if ( $data['citizenship_country'] == '' ){echo " checked";} ?> onClick="citizenshipSelect(true);">Россия
								</label>
					    	</div>
					    	<div class="width356">
								<label class="radio position1">
								    <input type="radio" tabindex="18" id="citizenship_country_s" name="citizenship_country_s" value="other" <? if ( $data['citizenship_country'] != '' ){echo " checked";} ?> onClick="citizenshipSelect(false);">Другое
								</label>
				    			<input type="text" class="width342" tabindex="19" id="citizenship_country" name="citizenship_country" <? if ( $data['citizenship_country'] == '' ){echo " disabled";} ?> value="<? echo $data['citizenship_country'] ?>" onchange="doSave(false, this)">
					    	</div>
			    		</div>
			    	</div>

				    <h2>Смена гражданства</h2>
			    	<div class="margined-line-34">
			    		<div class="row-fluid">
					    	<div class="clearfix">
					    		<div class="pull-left blue-text">
					    			Если изменяли гражданство, то укажите дату, а также по какой причине.
					    		</div>
					    		<div class="pull-right">
					    			<label class="checkbox">
									    <input type="checkbox" <? if ($data['citizenship_not_changed'] == '1'){ echo ' checked';} ?> id="citizenship_not_changed" name="citizenship_not_changed" value="1" tabindex="20" onchange="doSaveCheckbox(this); citizenshipChanged(this);">
									    Не менял(а)
									</label>
					    		</div>
					    	</div>
					    </div>
				    	<label>Дата смены</label>
						<select class="day" tabindex="21" <? if ($data['citizenship_not_changed'] == '1'){ echo ' disabled';} ?> id="citizenship_changed_date_d" name="citizenship_changed_date_d" onChange="doSaveDate('citizenship_changed_date')">
							<option value=""></option>
						</select>
						<select class="month" tabindex="22" <? if ($data['citizenship_not_changed'] == '1'){ echo ' disabled';} ?> id="citizenship_changed_date_m" name="citizenship_changed_date_m" onChange="doSaveDate('citizenship_changed_date')">
							<option value=""></option>
							<option value="01">Январь
							</option>
							<option value="02">Февраль
							</option>
							<option value="03">Март
							</option>
							<option value="04">Апрель
							</option>
							<option value="05">Май
							</option>
							<option value="06">Июнь
							</option>
							<option value="07">Июль
							</option>
							<option value="08">Август
							</option>
							<option value="09">Сентябрь
							</option>
							<option value="10">Октябрь
							</option>
							<option value="11">Ноябрь
							</option>
							<option value="12">Декабрь
							</option>
						</select>
						<select  class="year" tabindex="23" <? if ($data['citizenship_not_changed'] == '1'){ echo ' disabled';} ?> id="citizenship_changed_date_y" name="citizenship_changed_date_y" onChange="doSaveDate('citizenship_changed_date')">
							<option value=""></option>
						</select>
				    	<label>Причина смены</label>
				    	<input type="text" class="width816" tabindex="24" <? if ($data['citizenship_not_changed'] == '1'){ echo ' disabled';} ?> id="citizenship_changed_reason" name="citizenship_changed_reason" value="<? echo $data['citizenship_changed_reason'] ?>" onchange="doSave(false, this)">

				    	<div class="clearfix padded-top-25">
					    		<div class="pull-left blue-text">
					    			<button class="btn" name="exit" type="button" tabindex="27" onClick="doLogout();return false;">Выход</button>
					    		</div>
					    		<div class="pull-right">
					    			<button class="btn btn-primary" name="next" type="button" tabindex="26" onClick="stepDone();return false;">Далее &raquo;</button>
					    		</div>
					    	</div>
			    	</div>
				</form>
				
			</div>

		</div>
	</body>
</html>