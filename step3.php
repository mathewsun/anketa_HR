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

$work_activities = mysql_query( "
				SELECT wa.*, 
					a.postcode, a.region, a.district, a.city, a.location, a.street, a.house, a.block, a.appt, a.phone_country, a.phone_city, a.phone_number, a.phone_add,
					CONCAT_WS( ', ', a.postcode, a.region, a.city, a.street, a.house) as company_place_full
				FROM work_activity wa
				LEFT JOIN address a ON a.id = wa.company_place_id
				WHERE wa.anketa_id = ".$anketa_id."
				ORDER BY wa.id 
			");
$work_activities_count = mysql_num_rows($work_activities);

function getShortDate($inputString){
	$dr = date_create_from_format('Y-m-d', $inputString);
	if (($timestamp = strtotime($inputString)) === false || $dr == null){
		$inputString = "";
	}
	else{
		$inputString = $dr->format('m.Y');
	}
	return $inputString;
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Трудовая деятельность</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src='js/dhtmlxmessage.js'></script>
        <script src="js/site.js"></script>
		<link rel="stylesheet" type="text/css" href="stylesheets/dhtmlxmessage_dhx_skyblue.css">
		<script language="javascript">
			var workArr = [];
			var currentWorkEdit = 0;
			var workSaved = false;
			$(document).ready(function() {
				for (i = new Date().getFullYear(); i >= 1900; i--)
				{
					$('#date_start_y').append($('<option />').val(i).html(i));
					$('#date_end_y').append($('<option />').val(i).html(i));
				}				
				showAddressPopup(false);
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
					if (form.elements[i].name != "exit" && form.elements[i].name != "next" && form.elements[i].name != "prev")
					{
						form.elements[i].disabled = true;
					}
				}
			}

			function stepBack()
			{
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
					location.href='step2.php';
<?
				}
?>
			}
			
			function stepDone(){
				window.workSaved = true;
<?
				if ($_SESSION['is_admin'] == "1")
				{
?>
					location.href='step4.php?aid=<? echo $anketa_id ?>';
<?
				}
				else
				{
?>
					if (!$('#additional_skills_no').is(":checked")){
						if ($('#additional_skills').val() == ''){
							alert_error('Вы не заполнили поле "Дополнительные знания"');
							return false;
						}
					}
					if (!$('#driver_license_no').is(":checked")){
						if ($('#driver_license_category').val() == ''){
							alert_error('Вы не заполнили поле "Категории" раздела "Водительские права"');
							return false;
						}
						if ($('#driver_license_num').val() == ''){
							alert_error('Вы не заполнили поле "Удостоверения" раздела "Водительские права"');
							return false;
						}
					}
					if ($('#company_name').val() != '' && $('#position').val() != ''){
						window.workSaved = false;
						if (!workSave(false))
						{
							alert_error('Информация о трудовой деятельности не сохранилась');
							return false;
						}
						else
						{
							window.workSaved = true;
						}
					}
					
					nextStep();
<?
				}
?>
			}

<?
			if ($_SESSION['is_admin'] == "1")
			{
?>
				function showAddressPopup(show){
					if (show) {
						$('#address_popup').show();
					}
					else{
						$('#address_popup').hide();
					}
				}

				function doLogout(){
					window.location.href = "admin.php";
				}

<?
			}
			else
			{
?>
				function nextStep()
				{
					if ( window.workSaved )
					{
						location.href='step4.php';
					}
					else
					{
						window.setTimeout(nextStep(),500);
					}
				}

				function showAddressPopup(show){
					if (show) {
						$('#address_popup').show();
					}
					else{
						$('#address_popup').hide();
					}
					setAddressFull();
				}

				function doneAddressPopup(){
					if ($('#address_city').val() == ''){
						alert_error("Укажите хотя бы город.");
						return;
					}
					showAddressPopup(false);
				}

				function setAddressFull(){
					show_string = $('#address_postcode').val() + " " + $('#address_region').val() + " " + $('#address_city').val() + " " + $('#address_location').val() + " " + $('#address_street').val() + " " + $('#address_house').val() + " " + $('#address_block').val() + " " + $('#address_appt').val();
					$('#company_place').val(show_string);
				}
				
				function doSave( isAjax, obj){
					nameVal = obj.name
					valueVal = obj.value;
					if (isAjax){
						nameVal = obj.prop("name");
						valueVal = obj.value;
					}
					$.post("process.php", { step: '3', name: nameVal, value: valueVal } );
				}

				function doSaveCheckbox(obj){
					if(obj.checked){
						$.post("process.php", { step: '2', name: obj.name, value: "1" } );
					}
					else{
						$.post("process.php", { step: '2', name: obj.name, value: "0" } );
					}
				}
				
				function doSaveDate(objName){
					objY = eval(objName + '_y');
					objM = eval(objName + '_m');
					objD = eval(objName + '_d');
					if (objY.value != '' && objM.value != '' && objD.value != ''){
						objValue = '' + objY.value + '-' + objM.value + '-' + objD.value;
						$.post("process.php", { step: '2', name: objName, value: objValue } );
					}
				}
				
				function addSkillsChanged(obj){
					if(obj.checked){
						$('#additional_skills').val('');

						doSave( true, $('#additional_skills'));

						$('#additional_skills').prop('disabled', true);
					}
					else{
						$('#additional_skills').prop('disabled', false);
					}
				}
				
				function driverLicenseChanged(obj){
					if(obj.checked){
						$('#driver_license_category').val('');
						$('#driver_license_num').val('');

						doSave( true, $('#driver_license_category'));
						doSave( true, $('#driver_license_num'));

						$('#driver_license_category').prop('disabled', true);
						$('#driver_license_num').prop('disabled', true);
					}
					else{
						$('#driver_license_category').prop('disabled', false);
						$('#driver_license_num').prop('disabled', false);
					}
				}
				
				var work_activity_id = 0;
				
				function workDelete(proceed_id){
					if (confirm("Удалить строку?")){
						$.post("process.php", 
									{ 
										step: '3.1', 
										action: 'delete',
										id: proceed_id
									},
									function(data){
										location.href="step3.php";
									}
								);
					}
				}
				
				function workEdit(counter, proceed_id, date_start, date_end, company_name, position, recommendations, dismiss_reason, company_place, postcode, region, district, city, location, street, house, block, appt, phone_country, phone_city, phone_number, phone_add){
					date_start_arr = date_start.split(".");
					date_end_arr = date_end.split(".");

					currentWorkEdit = counter;
					work_activity_id = proceed_id;
					$('#date_start_y').val(date_start_arr[1]);
					$('#date_start_m').val(date_start_arr[0]);
					$('#date_end_y').val(date_end_arr[1]);
					$('#date_end_m').val(date_end_arr[0]);
					$('#company_name').val(company_name);
					$('#position').val(position);
					$('#recommendations').val(recommendations);
					$('#dismiss_reason').val(dismiss_reason);
					$('#company_place').val(company_place);

					$('#address_postcode').val(postcode);
					$('#address_region').val(region);
					$('#address_district').val(district);
					$('#address_city').val(city);
					$('#address_location').val(location);
					$('#address_street').val(street);
					$('#address_house').val(house);
					$('#address_block').val(block);
					$('#address_appt').val(appt);
					$('#address_phone_country').val(phone_country);
					$('#address_phone_city').val(phone_city);
					$('#address_phone_number').val(phone_number);
					$('#address_phone_add').val(phone_add);

					$('#work_save').val("Сохранить");
					$('#work_save_cancel').show();
				}

				function workEditCancel(){
					currentWorkEdit = 0;
					$('#date_start_y').val("");
					$('#date_start_m').val("");
					$('#date_end_y').val("");
					$('#date_end_m').val("");
					$('#company_name').val("");
					$('#position').val("");
					$('#recommendations').val("");
					$('#dismiss_reason').val("");
					$('#company_place').val("");

					$('#address_postcode').val("");
					$('#address_region').val("");
					$('#address_district').val("");
					$('#address_city').val("");
					$('#address_location').val("");
					$('#address_street').val("");
					$('#address_house').val("");
					$('#address_block').val("");
					$('#address_appt').val("");
					$('#address_phone_country').val("");
					$('#address_phone_city').val("");
					$('#address_phone_number').val("");
					$('#address_phone_add').val("");
					
					$('#work_save').val("Добавить");
					$('#work_save_cancel').hide();
					work_activity_id = 0;
				}

				function workSave(keepStep){
						fields = "";
						if ($('#date_start_y').prop('selectedIndex') < 1 || $('#date_start_m').prop('selectedIndex') < 1){
							fields += ", Дата с";
						}
						if ($('#date_end_y').prop('selectedIndex') < 1 || $('#date_end_m').prop('selectedIndex') < 1){
							fields += ", Дата по";
						}
						if ($('#company_name').val() == ''){
							fields += ", Полное название организации";
						}
						if ($('#position').val() == ''){
							fields += ", Занимаемая должность";
						}
						if ($('#dismiss_reason').val() == ''){
							fields += ", Причина увольнения";
						}
						if ($('#address_city').val() == ''){
							fields += ", Местонахождение организации";
						}
						if (fields.length > 0){
							alert_error("Вы не заполнили следующие поля при заполнении данных об образовании: " + fields.substring(2));
							return false;
						}

						birthday_arr = '<?php echo( $data['birthday'] ) ?>'.split("-");
						birthdayDateYear = birthday_arr[0];
						if( birthdayDateYear > $('#date_start_y').val() ){
							alert_error("Год начала работы должен быть больше года рождения");
							return false;
						}

						/* Временно отключили проверку стажа работы.
						if (workArr.length > 0 && currentWorkEdit != 1){
							prevEndDate = workArr[workArr.length-1];
							if ( currentWorkEdit > 0){
								prevEndDate = workArr[currentWorkEdit-1];
							}
							startDate = new Date($('#date_start_y').val(), $('#date_start_m').val() - 1, 1);
							endDate = new Date($('#date_end_y').val(), $('#date_end_m').val() - 1, 1);
							if( (endDate-startDate)/(1000*60*60*24) < 0){
								alert_error("Дата окончания работы не может быть ранее даты начала работы.");
								return false;
							}
							if( (startDate-prevEndDate)/(1000*60*60*24) > 35){
								alert_error("Прерыв в работе более месяца недопустим");
								return false;
							}
							if( (startDate-prevEndDate)/(1000*60*60*24) < 0){
								alert_error("Дата начала работы не может быть ранее даты увольнения с предыдущего места работы.");
								return false;
							}
						}
						*/

						date_start_val = '' + $('#date_start_y').val() + '-' + $('#date_start_m').val() + '-01';
						date_end_val = '' + $('#date_end_y').val() + '-' + $('#date_end_m').val() + '-01';
						if (keepStep)
						{
							$.post("process.php", 
										{ 
											step: '3.1', 
											action: 'save',
											id: work_activity_id, 
											date_start: date_start_val, 
											date_end: date_end_val, 
											company_name: $('#company_name').val(), 
											position: $('#position').val(), 
											recommendations: $('#recommendations').val(), 
											dismiss_reason: $('#dismiss_reason').val(),
											postcode: $('#address_postcode').val(), 
											region: $('#address_region').val(), 
											district: $('#address_district').val(), 
											city: $('#address_city').val(), 
											location: $('#address_location').val(), 
											street: $('#address_street').val(), 
											house: $('#address_house').val(), 
											block: $('#address_block').val(), 
											appt: $('#address_appt').val(), 
											phone_country: $('#address_phone_country').val(), 
											phone_city: $('#address_phone_city').val(), 
											phone_number: $('#address_phone_number').val(), 
											phone_add: $('#address_phone_add').val()
										},
										function(data){
											location.href="step3.php";
										}
									);
						}
						else
						{
							$.ajax({ 
										url: "process.php",
										async: false,										
										type: "POST",
										data:
											{ 
												step: '3.1', 
												action: 'save',
												id: work_activity_id, 
												date_start: date_start_val, 
												date_end: date_end_val, 
												company_name: $('#company_name').val(), 
												position: $('#position').val(), 
												recommendations: $('#recommendations').val(), 
												dismiss_reason: $('#dismiss_reason').val(),
												postcode: $('#address_postcode').val(), 
												region: $('#address_region').val(), 
												district: $('#address_district').val(), 
												city: $('#address_city').val(), 
												location: $('#address_location').val(), 
												street: $('#address_street').val(), 
												house: $('#address_house').val(), 
												block: $('#address_block').val(), 
												appt: $('#address_appt').val(), 
												phone_country: $('#address_phone_country').val(), 
												phone_city: $('#address_phone_city').val(), 
												phone_number: $('#address_phone_number').val(), 
												phone_add: $('#address_phone_add').val()
											},
										dataType: "html"
									});
							return true;
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
				<h1>Трудовая деятельность <i>(шаг 3 из 5)</i><span class="to_right">Внимание! Все поля анкеты должны быть заполнены.</span></h1>
			</div>
			<div id="page-container">
				<div id="warning-message">
				</div>
				<form>
					<h2>Выполняемая работа с начала трудовой деятельности<br>
						<small  class="blue-text">
							Включая учебу в высших и средних учебных заведениях, военную службу, работу по совместительству, предпринимательскую деятельность.<br>
							Заполнять таблицу следует в обратном порядке (начиная с последнего (текущего) места работы.<br>
							Адреса по последним трем местам работы должны быть заполнены точно, по остальным хотя бы город.» <br>
							Вы можете навести мышь на заголовок столбца и получить подсказку о заполнении.<br>
							В случае перерыва в трудовой деятельности в поле «Полное название организации» указать «не работал(а)», в поле «Занимаемая должность» - причину перерыва. В случае работы по настоящее время в качестве даты оканчания ввести 31 декабря текущего года.
						</small>
					</h2>
			    	<div class="margined-line-34">
			    		<div class="row-fluid">
					    	<div class="width356">
						    	<label>Дата с</label>
								<select class="month" tabindex="1" id="date_start_m" name="start_m">
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
								<select  class="year" id="date_start_y" name="start_y" tabindex="2">
									<option value=""></option>
								</select>
					    	</div>
					    	<div class="width356">
						    	<label>Дата по</label>
								<select class="month" tabindex="3" id="date_end_m" name="end_m">
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
								<select  class="year" id="date_end_y" name="end_y" tabindex="4">
									<option value=""></option>
								</select>
					    	</div>
				    	</div>
				    	<div class="row-fluid">
					    	<div class="width356">
						    	<label>Полное название организации</label>
						    	<input type="text" class="width342" tabindex="5" id="company_name" name="company_name">
					    	</div>
					    	<div class="width356">
						    	<label>Занимаемая должность</label>
						    	<input type="text" class="width342" tabindex="6" id="position" name="position">
					    	</div>
				    	</div>
						<label>Причина увольнения</label>
						<input type="text" class="width816" tabindex="7" id="dismiss_reason" name="dismiss_reason">
						<label>Рекомендации <i>(ФИО, должность и тел. лиц, которые могут Вас рекомендовать как специалиста. ОБЯЗАТЕЛЬНО по последним трем местам работы.)</i></label>
						<input type="text" class="width816" tabindex="8" id="recommendations" name="recommendations">
				    	<label>Место нахождения организации <i>(Адреса по последним трем местам работы должны быть заполнены точно, по остальным хотя бы город.)</i></label>
				    	<input type="text" class="width677" tabindex="9" id="company_place" name="company_place" disabled="disabled">
						<button class="btn btn-primary ask padded-line margined-top--8" type="button" onClick="showAddressPopup(true); return false;">Задать</button>
				    	<div class="clearfix">
					    	<div class="pull-right">
								<input type="button" class="btn btn-primary ask padded-line margined-top-2" id="work_save" value="Добавить" onClick="workSave(true)">
								<input type="button" class="btn btn-primary ask padded-line margined-top-2" id="work_save_cancel" value="Отменить" onClick="workEditCancel()" style="display:none">
					    	</div>
				    	</div>
								<div class="popup__overlay" id="address_popup" style="display:none">
								    <div class="popup two">
											<h2>Место нахождения организации</h2>
											<div class="row-fluid">
										    	<div class="width74">
													<label>Индекс</label>
													<input type="text" class="width60" id="address_postcode" name="address_postcode">
												</div>
												<div class="width178">
													<label>Регион</label>
													<input type="text" class="width253" id="address_region" name="address_region">
												</div>
											</div>
									    	<label>Район</label>
											<input type="text" class="width342 no-margin-left" id="address_district" name="address_district">
									    	<label>Город*</label>
											<input type="text" class="width342 no-margin-left" id="address_city" name="address_city">
											<div class="row-fluid">
										    	<div class="width153 no-margin-left">
													<label>Населённый пункт</label>
													<input type="text" class="width159 no-margin-left" id="address_location" name="address_location">
												</div>
												<div class="width153">
													<label>Улица</label>
													<input type="text" class="width159 no-margin-left" id="address_street" name="address_street">
												</div>
											</div>
											<div class="row-fluid">
										    	<div class="width74">
								    				<label class="font12">Дом</label>
													<input type="text" class="width60" id="address_house" name="address_house">
												</div>
												<div class="width74 margined-left">
										    		<label class="font12">Корпус</label>
													<input type="text" class="width60" id="address_block" name="address_block">
												</div>
												<div class="width178 no-margin-right">
										    		<label class="font12">Квартира(офис)</label>
													<input type="text" class="width164" id="address_appt" name="address_appt">
												</div>
											</div>
											<h2>Номер телефона</h2>
											<div class="row-fluid">
										    	<div class="width74">
								    				<label class="font12">Код страны</label>
													<input type="text" class="width60" id="address_phone_country" name="address_phone_country">
												</div>
												<div class="width74 margined-left">
										    		<label class="font12">Код города</label>
													<input type="text" class="width60" id="address_phone_city" name="address_phone_city">
												</div>
												<div class="width178 no-margin-right">
										    		<label class="font12">Телефон</label>
													<input type="text" class="width164" id="address_phone_number" name="address_phone_number">
												</div>
										    	<div class="width178_2">
								    				<label class="font12">Добавочный</label>
													<input type="text" class="width164" id="address_phone_add" name="address_phone_add">
												</div>
											</div>
											<div class="clearfix">
									    		<div class="pull-right">
									    			<button class="btn btn-primary ask padded-line margined-top-2" type="button" onClick="doneAddressPopup(); return false;">ОК</button>
									    			<button class="btn btn-primary ask padded-line margined-top-2" type="button" onClick="showAddressPopup(false); return false;">Отмена</button>
									    		</div>
									    	</div>
					    			</div>
					    			<a href="#" class="close two" onClick="showAddressPopup(false); return false;">&nbsp;</a>
					    		</div>
			    	</div>

					<table>
					    <thead>
						    <tr>
<?
								if ($_SESSION['is_admin'] != "1")
								{
?>
									<td class="icon-sell"></td>
<?
								}
?>
						    	<td class="sell-5" title="Месяц и год поступления на место работы">Дата с</td>
						    	<td class="sell-5" title="Месяц и год увольнения">Дата по</td>
						    	<td class="sell-5" title="Полное название организации">Организация</td>
						    	<td class="sell-5" title="Занимаемая должность">Должность</td>
						    	<td class="sell-5" title="ФИО, должность и тел. лиц, которые могут Вас рекомендовать как специалиста (ОБЯЗАТЕЛЬНО по последним трем местам работы)">Рекомендации</td>
								<td class="sell-5" title="Причина увольнения">Причина увольнения</td>
								<td class="sell-5" title="Место нахождения организации">Место нахождения организации</td>
<?
								if ($_SESSION['is_admin'] != "1")
								{
?>
									<td class="icon-sell"></td>
<?
								}
?>
						    </tr>
					    </thead>
					    <tbody>
<?php
							$counter = 0;
							while ($row = mysql_fetch_assoc($work_activities)) {
								$counter++;
?>
								<script language="javascript">
									date_arr = '<?php echo( $row['date_end'] ) ?>'.split("-");
									workArr[<? echo $counter ?>] = new Date(date_arr[0], date_arr[1]-1, date_arr[2]);
								</script>
								<tr>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-document" onClick="workEdit(<?php echo( $counter ) ?>, <?php echo( $row['id'] ) ?>, '<?php echo( getShortDate($row['date_start']) ) ?>', '<?php echo( getShortDate($row['date_end']) ) ?>', '<?php echo( getSafeJSString($row['company_name']) ) ?>', '<?php echo( getSafeJSString($row['position']) ) ?>', '<?php echo( getSafeJSString($row['recommendations']) ) ?>', '<?php echo( getSafeJSString($row['dismiss_reason']) ) ?>', '<?php echo( getSafeJSString($row['company_place_full']) ) ?>', '<?php echo( getSafeJSString($row['postcode']) ) ?>', '<?php echo( getSafeJSString($row['region']) ) ?>', '<?php echo( getSafeJSString($row['district']) ) ?>', '<?php echo( getSafeJSString($row['city']) ) ?>', '<?php echo( getSafeJSString($row['location']) ) ?>', '<?php echo( getSafeJSString($row['street']) ) ?>', '<?php echo( getSafeJSString($row['house']) ) ?>', '<?php echo( getSafeJSString($row['block']) ) ?>', '<?php echo( getSafeJSString($row['appt']) ) ?>', '<?php echo( getSafeJSString($row['phone_country']) ) ?>', '<?php echo( getSafeJSString($row['phone_city']) ) ?>', '<?php echo( getSafeJSString($row['phone_number']) ) ?>', '<?php echo( getSafeJSString($row['phone_add']) ) ?>'); return false;">&nbsp;</a></td>
<?
									}
?>
									<td class="sell-5"><?php echo( getShortDate($row['date_start']) ) ?></td>
									<td class="sell-5"><?php echo( getShortDate($row['date_end']) ) ?></td>
									<td class="sell-5"><?php echo( $row['company_name'] ) ?></td>
									<td class="sell-5"><?php echo( $row['position'] ) ?></td>
									<td class="sell-5"><?php echo( $row['recommendations'] ) ?></td>
									<td class="sell-5"><?php echo( $row['dismiss_reason'] ) ?></td>
									<td class="sell-5"><?php echo( $row['company_place_full'] ) ?></td>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell">
											<? if ($counter == $work_activities_count){ ?><a href="#" class="icon-delete" onClick="workDelete(<?php echo( $row['id'] ) ?>); return false;">&nbsp;</a><? } ?>
										</td>
<?
									}
?>
								</tr>
<?php
							}
							mysql_free_result($work_activities);
?>
					    </tbody>
					</table>			    	


				    <h2>Дополнительные знания <small class="blue-text">(умения, навыки, степень владения)</small></h2>
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="clearfix">
					    		<div class="pull-left">
							    	<label>Владение компьютером, программы, уровень владения ПК</label>
						    		<input type="text" class="width638" tabindex="16" <? if ($data['additional_skills'] == ''){ echo ' disabled';} ?> id="additional_skills" name="additional_skills" value="<? echo $data['additional_skills'] ?>" onchange="doSave(false, this)">
					    		</div>
					    		<div class="pull-right padded-top-25">
					    			<label class="checkbox">
									    <input type="checkbox" <? if ($data['additional_skills'] == ''){ echo ' checked';} ?> id="additional_skills_no" name="additional_skills_no" value="1" tabindex="17" onchange="addSkillsChanged(this);">
									    Не имею (не владею)
									</label>
					    		</div>
					    	</div>
					    </div>
				    </div>

				    <h2>Водительские права
					</h2>
			    	<div class="margined-line-34">
					    <div class="row-fluid">
					    	<div class="width356">
						    	<label>Категории</label>
								<input type="text" class="width342" tabindex="21" <? if ($data['driver_license_category'] == ''){ echo ' disabled';} ?> id="driver_license_category" name="driver_license_category" value="<? echo $data['driver_license_category'] ?>" onchange="doSave(false, this)" />
								<label class="checkbox">
									<input type="checkbox" <? if ($data['driver_license_num'] == ''){ echo ' checked';} ?> id="driver_license_no" name="driver_license_no" value="1" tabindex="18" onchange="driverLicenseChanged(this);">
									Не имею
								</label>
					    	</div>
					    	<div class="width356">
						    	<label>Серия, номер водительского удостоверения</label>
						    	<input type="text" class="width342" tabindex="22" <? if ($data['driver_license_category'] == ''){ echo ' disabled';} ?> id="driver_license_num" name="driver_license_num" value="<? echo $data['driver_license_num'] ?>" onchange="doSave(false, this)">
					    	</div>
				    	</div>
				    </div>

			    	<div class="margined-line-34 padded-top-25">
				    	<div class="clearfix">
				    		<div class="pull-left blue-text">
				    			<button class="btn" type="button" name="exit" tabindex="27" onClick="doLogout();return false;">Выход</button>
				    		</div>
				    		<div class="pull-right">
					    			<button class="btn btn-primary" name="prev" type="button" tabindex="25" onClick="stepBack();return false;">&laquo; Назад</button>
					    			<button class="btn btn-primary" name="next" type="button" tabindex="26" onClick="stepDone();return false;">Далее &raquo;</button>
				    		</div>
					    </div>
			    	</div>
				</form>
				
			</div>

		</div>
	</body>
</html>