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

$marital_statuses = mysql_query( "SELECT id, name FROM marital_status ORDER BY sort_order ");
$relations = mysql_query( "SELECT id, name FROM relation ORDER BY sort_order ");

$relatives = mysql_query( "
				SELECT r.*, rl.name as relation_name,
					a.postcode, a.region, a.district, a.city, a.location, a.street, a.house, a.block, a.appt, a.phone_country, a.phone_city, a.phone_number, a.phone_add,
					CONCAT_WS( ', ', a.postcode, a.region, a.city, a.street, a.house) as address_full
				FROM relatives r
				INNER JOIN relation rl ON rl.id = r.relation_id
				LEFT JOIN address a ON a.id = r.address_id
				WHERE r.anketa_id = ".$anketa_id."
				ORDER BY r.id 
			");


?>
<!DOCTYPE html>
<html>
	<head>
		<title>Семейное положение</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src='js/dhtmlxmessage.js'></script>
        <script src="js/site.js"></script>
		<link rel="stylesheet" type="text/css" href="stylesheets/dhtmlxmessage_dhx_skyblue.css">
		<script language="javascript">
			var hasMother = false;
			var hasFather = false;
			var hasSpouse = false;
			var relativeSaved = false;
			
			$(document).ready(function() {
				for (i = new Date().getFullYear(); i >= 1900; i--)
				{
					$('#birthday_y').append($('<option />').val(i).html(i));
				}				
				for (i = 1; i <= 31; i++)
				{
					show = '' + i;
					if ( i<10 ){
						show = '0' + i;
					}
					$('#birthday_d').append($('<option />').val(show).html(show));
				}				
				$('#marital_status_id').val(<?php echo( $data['marital_status_id'] ) ?>);
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
					location.href='step3.php?aid=<? echo $anketa_id ?>';
<?
				}
				else
				{
?>
					location.href='step3.php';
<?
				}
?>
			}
			
			function stepDone(){
				window.relativeSaved = true;
<?
				if ($_SESSION['is_admin'] == "1")
				{
?>
					location.href='step5.php?aid=<? echo $anketa_id ?>';
<?
				}
				else
				{
?>
					if (!$('#courts_no').is(":checked")){
						if ($('#courts').val() == ''){
							alert_error('Вы не заполнили поле "Привлекались ли Вы или Ваши близкие родственники к судебной ответственности"');
							return false;
						}
					}
					if (!$('#police_relatives_no').is(":checked")){
						if ($('#police_relatives').val() == ''){
							alert_error('Вы не заполнили поле "Имеете ли Вы родственников, работающих в правоохранительных структурах"');
							return false;
						}
					}
					if ($('#marital_status_id').val() == ''){
						alert_error('Вы не заполнили поле "Семейное положение"');
						return false;
					}
					if ( !hasMother && $('#relation_id').val() != '5'){
						alert_error('Вы не добавили информацию о матери');
						return false;
					}
					if ( !hasFather && $('#relation_id').val() != '7'){
						alert_error('Вы не добавили информацию об отце');
						return false;
					}
					if ( ($('#marital_status_id').val() == '2' || $('#marital_status_id').val() == '3') && !hasSpouse){
						alert_error('Вы не добавили информацию о супруге');
						return false;
					}
					
					if ($('#last_name').val() != '' && $('#first_name').val() != ''){
						window.relativeSaved = true;
						if (!relativeSave(false))
						{
							alert_error('Информация о родственнике не сохранилась');
							return false;
						}
						else
						{
							window.relativeSaved = true;
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
					if ( window.relativeSaved )
					{
						location.href='step5.php';
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

				function setAddressFull(){
					show_string = $('#address_postcode').val() + " " + $('#address_region').val() + " " + $('#address_city').val() + " " + $('#address_location').val() + " " + $('#address_street').val() + " " + $('#address_house').val() + " " + $('#address_block').val() + " " + $('#address_appt').val();
					$('#address_full').val(show_string);
				}

				function doSave( isAjax, obj){
					nameVal = obj.name
					valueVal = obj.value;
					if (isAjax){
						nameVal = obj.prop("name");
						valueVal = obj.value;
					}
					$.post("process.php", { step: '4', name: nameVal, value: valueVal } );
				}

				var relative_id = 0;
				
				function relativeDelete(proceed_id){
					if (confirm("Удалить строку?")){
						$.post("process.php", 
									{ 
										step: '4.1', 
										action: 'delete',
										id: proceed_id
									},
									function(data){
										location.href="step4.php";
									}
								);
					}
				}
				
				function relativeEdit(proceed_id, relation_id, first_name, last_name, middle_name, birthday, birth_place, old_first_name, old_last_name, old_middle_name, company, position, address_full, postcode, region, district, city, location, street, house, block, appt, phone_country, phone_city, phone_number, phone_add){
					birthday_arr = birthday.split("-");
					relative_id = proceed_id;
					$('#relation_id').val(relation_id);
					$('#first_name').val(first_name);
					$('#last_name').val(last_name);
					$('#middle_name').val(middle_name);
					$('#birthday_y').val(birthday_arr[0]);
					$('#birthday_m').val(birthday_arr[1]);
					$('#birthday_d').val(birthday_arr[2]);
					$('#birth_place').val(birth_place);
					$('#old_first_name').val(old_first_name);
					$('#old_last_name').val(old_last_name);
					$('#old_middle_name').val(old_middle_name);
					$('#company').val(company);
					$('#position').val(position);
					$('#address_full').val(address_full);

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

					$('#relative_save').val("Сохранить");
					$('#relative_save_cancel').show();
				}

				function relativeEditCancel(){
					$('#relation_id').val("");
					$('#first_name').val("");
					$('#last_name').val("");
					$('#middle_name').val("");
					$('#birthday_y').val("");
					$('#birthday_m').val("");
					$('#birthday_d').val("");
					$('#birth_place').val("");
					$('#old_first_name').val("");
					$('#old_last_name').val("");
					$('#old_middle_name').val("");
					$('#company').val("");
					$('#position').val("");
					$('#address_full').val("");

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

					$('#relative_save').val("Добавить");
					$('#relative_save_cancel').hide();
					relative_id = 0;
				}

				function relativeSave(keepStep){
						if ($('#old_first_name').val() == "" 
							&& $('#old_last_name').val() == "" 
							&& $('#old_middle_name').val() == ""){
							$('#old_last_name').val('Не менял(а)');
						}

						if ($('#company').val() == ""){
							$('#company').val('Не работает');
						}

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
						if ($('#relation_id').val() == '' || $('#relation_id').val() == null){
							fields += ", Степень родства";
						}
						if ($('#birthday_d').prop('selectedIndex') < 1 || $('#birthday_m').prop('selectedIndex') < 1 || $('#birthday_y').prop('selectedIndex') < 1){
							fields += ", Дата рождения";
						}
						if ($('#birth_place').val() == ''){
							fields += ", Место рождения";
						}
						if ($('#company').val() == ''){
							fields += ", Место работы";
						}
						if (fields.length > 0){
							alert_error("Вы не заполнили следующие поля при заполнении данных об образовании: " + fields.substring(2));
							return false;
						}

						if ($('#address_city').val() == "" 
							|| $('#address_street').val() == "" 
							|| $('#address_house').val() == "" ){
							alert_error("Заполните не заполнили адрес проживания!");
							return false;
						}

						birthdayDateObj = new Date($('#birthday_y').val(), $('#birthday_m').val()-1, $('#birthday_d').val() );
						//Проверка закомментирована, так как в IE8 объекты всегда неравны и постоянно вылетает ошибка
                        //if(birthdayDateObj.getFullYear() != parseInt($('#birthday_y').val()) || birthdayDateObj.getMonth() != parseInt($('#birthday_m').val())-1 || birthdayDateObj.getDate() != parseInt($('#birthday_d').val())){
						//	alert_error("Некорректная дата рождения.");
						//	return false;
						//}
						birthday_val = '' + $('#birthday_y').val() + '-' + $('#birthday_m').val() + '-' + $('#birthday_d').val();
						if (keepStep)
						{
							$.post("process.php", 
										{ 
											step: '4.1', 
											action: 'save',
											id: relative_id, 
											relation_id: $('#relation_id').val(), 
											first_name: $('#first_name').val(), 
											last_name: $('#last_name').val(),
											middle_name: $('#middle_name').val(),
											birthday: birthday_val,
											birth_place: $('#birth_place').val(),
											old_first_name: $('#old_first_name').val(),
											old_last_name: $('#old_last_name').val(),
											old_middle_name: $('#old_middle_name').val(),
											company: $('#company').val(),
											position: $('#position').val(),
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
											location.href="step4.php";
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
												step: '4.1', 
												action: 'save',
												id: relative_id, 
												relation_id: $('#relation_id').val(), 
												first_name: $('#first_name').val(), 
												last_name: $('#last_name').val(),
												middle_name: $('#middle_name').val(),
												birthday: birthday_val,
												birth_place: $('#birth_place').val(),
												old_first_name: $('#old_first_name').val(),
												old_last_name: $('#old_last_name').val(),
												old_middle_name: $('#old_middle_name').val(),
												company: $('#company').val(),
												position: $('#position').val(),
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

				function courtsChanged(obj){
					if(obj.checked){
						$('#courts').val('');

						doSave( true, $('#courts'));

						$('#courts').prop('disabled', true);
					}
					else{
						$('#courts').prop('disabled', false);
					}
				}

				function policeRelativesChanged(obj){
					if(obj.checked){
						$('#police_relatives').val('');

						doSave( true, $('#police_relatives'));

						$('#police_relatives').prop('disabled', true);
					}
					else{
						$('#police_relatives').prop('disabled', false);
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
				<h1>Семейное положение <i>(шаг 4 из 5)</i><span class="to_right">Внимание! Все поля анкеты должны быть заполнены.</span></h1>
			</div>
			<div id="page-container">
				<div id="warning-message">
				</div>
				<form>
			    	<div class="clearfix margined-line-34">
				    	<label>Семейное положение</label>
						<select class="width356 no-margin-left" tabindex="1" id="marital_status_id" name="marital_status_id" onchange="doSave(false, this)">
							<option></option>
<?php
							while ($row = mysql_fetch_assoc($marital_statuses)) {
?>
								<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
							}
							mysql_free_result($marital_statuses);
?>
						</select>
			    	</div>

			    	<h2>Ваши ближайшие родственники <small class="blue-text">(жена/муж, отец, мать, братья, сёстры, дети)</small></h2>
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="width356">
						    	<label>Фамилия</label>
						    	<input type="text" class="width342" tabindex="2" id="last_name" name="last_name">
						    	<label>Отчество</label>
						    	<input type="text" class="width342" tabindex="4" id="middle_name" name="middle_name">
					    		<label>Дата рождения</label>
								<select class="day" id="birthday_d" name="birthday_d" tabindex="6">
									<option value=""></option>
								</select>
								<select class="month" tabindex="6" id="birthday_m" name="birthday_m">
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
								<select  class="year" tabindex="6" id="birthday_y" name="birthday_y">
									<option value=""></option>
								</select>
						    	<label>Старая фамилия</label>
						    	<input type="text" class="width342" tabindex="8" id="old_last_name" name="old_last_name">
						    	<label>Старое отчество</label>
						    	<input type="text" class="width342" tabindex="10" id="old_middle_name" name="old_middle_name">
						    	<label>Место работы</label>
						    	<input type="text" class="width342" tabindex="11" id="company" name="company">
					    	</div>
					    	<div class="width356">
						    	<label>Имя</label>
						    	<input type="text" class="width342" tabindex="3" id="first_name" name="first_name">
						    	<label>Степень родства</label>
								<select class="width356 no-margin-left" id="relation_id" tabindex="5" name="relation_id">
									<option></option>
<?php
									while ($row = mysql_fetch_assoc($relations)) {
?>
										<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
									}
									mysql_free_result($relations);
?>
								</select>
						    	<label>Место рождения</label>
						    	<input type="text" class="width342" tabindex="7" id="birth_place" name="birth_place">
						    	<label>Старое имя</label>
						    	<input type="text" class="width342" tabindex="9" id="old_first_name" name="old_first_name">
						    	<label>&nbsp;</label>
						    	<label style="height:30px">&nbsp;</label>
						    	<label>Должность</label>
						    	<input type="text" class="width342" tabindex="12" id="position" name="position">
					    	</div>
				    	</div>
				    	<label>Адрес проживания</label>
				    	<input type="text" class="width677" tabindex="13" id="address_full" name="address_full" disabled="disabled">
						<button class="btn btn-primary ask padded-line margined-top--8" tabindex="14" type="button" onClick="showAddressPopup(true); return false;">Задать</button>
				    	<div class="clearfix">
					    	<div class="pull-right">
								<input type="button" tabindex="15" class="btn btn-primary ask padded-line margined-top-2" id="relative_save" value="Добавить" onClick="relativeSave(true)">
								<input type="button" tabindex="16" class="btn btn-primary ask padded-line margined-top-2" id="relative_save_cancel" value="Отменить" onClick="relativeEditCancel()" style="display:none">
					    	</div>
				    	</div>
								<div class="popup__overlay" id="address_popup" style="display:none;">
								    <div class="popup two">
											<h2>Адрес проживания</h2>
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
													<label>Улица*</label>
													<input type="text" class="width159 no-margin-left" id="address_street" name="address_street">
												</div>
											</div>
											<div class="row-fluid">
										    	<div class="width74">
								    				<label class="font12">Дом*</label>
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
									    			<button class="btn btn-primary ask padded-line margined-top-2" type="button" onClick="showAddressPopup(false); return false;">ОК</button>
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
						    	<td class="sell-4">Степень родства</td>
						    	<td class="sell-4">ФИО</td>
						    	<td class="sell-4">Дата и место рождения</td>
						    	<td class="sell-4">Старое ФИО</td>
						    	<td class="sell-4">Место работы / должность</td>
						    	<td class="sell-4">Адрес проживания</td>
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
							while ($row = mysql_fetch_assoc($relatives)) {
								if ($row['relation_id'] == 5){ //mother
?>
									<script language="javascript">
										hasMother = true;
									</script>
<?
								}
								if ($row['relation_id'] == 7){ //father
?>
									<script language="javascript">
										hasFather = true;
									</script>
<?
								}
								if ($row['relation_id'] == 4 || $row['relation_id'] == 6){ //Spouse
?>
									<script language="javascript">
										hasSpouse = true;
									</script>
<?
								}
?>
								<tr>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-document" onClick="relativeEdit(<?php echo( $row['id'] ) ?>, '<?php echo( $row['relation_id'] ) ?>', '<?php echo( getSafeJSString($row['first_name']) ) ?>', '<?php echo( getSafeJSString($row['last_name']) ) ?>', '<?php echo( getSafeJSString($row['middle_name']) ) ?>', '<?php echo( getSafeJSString($row['birthday']) ) ?>', '<?php echo( getSafeJSString($row['birth_place']) ) ?>', '<?php echo( getSafeJSString($row['old_first_name']) ) ?>', '<?php echo( getSafeJSString($row['old_last_name']) ) ?>', '<?php echo( getSafeJSString($row['old_middle_name']) ) ?>', '<?php echo( getSafeJSString($row['company']) ) ?>', '<?php echo( getSafeJSString($row['position']) ) ?>', '<?php echo( getSafeJSString($row['address_full']) ) ?>', '<?php echo( getSafeJSString($row['postcode']) ) ?>', '<?php echo( getSafeJSString($row['region']) ) ?>', '<?php echo( getSafeJSString($row['district']) ) ?>', '<?php echo( getSafeJSString($row['city']) ) ?>', '<?php echo( getSafeJSString($row['location']) ) ?>', '<?php echo( getSafeJSString($row['street']) ) ?>', '<?php echo( getSafeJSString($row['house']) ) ?>', '<?php echo( getSafeJSString($row['block']) ) ?>', '<?php echo( getSafeJSString($row['appt']) ) ?>', '<?php echo( getSafeJSString($row['phone_country']) ) ?>', '<?php echo( getSafeJSString($row['phone_city']) ) ?>', '<?php echo( getSafeJSString($row['phone_number']) ) ?>', '<?php echo( getSafeJSString($row['phone_add']) ) ?>'); return false;">&nbsp;</a></td>
<?
									}
?>
									<td class="sell-4"><?php echo( $row['relation_name'] ) ?></td>
									<td class="sell-4"><?php echo( $row['last_name'].' '.$row['first_name'].' '.$row['middle_name'] ) ?></td>
									<td class="sell-4"><?php echo( $row['birthday'] ) ?><br><?php echo( $row['birth_place'] ) ?></td>
									<td class="sell-4"><?php echo( $row['old_last_name'].' '.$row['old_first_name'].' '.$row['old_middle_name'] ) ?></td>
									<td class="sell-4"><?php echo( $row['company'] ) ?> / <?php echo( $row['position'] ) ?></td>
									<td class="sell-4"><?php echo( $row['address_full'] ) ?></td>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-delete" onClick="relativeDelete(<?php echo( $row['id'] ) ?>); return false;">&nbsp;</a></td>
<?
									}
?>
								</tr>
<?php
							}
							mysql_free_result($relatives);
?>
					    </tbody>
					</table>			    	
<br><br>
						<div class="clearfix">
					    	<div class="pull-left">
						    	Привлекались ли Вы или Ваши близкие родственники к судебной ответственности, когда и где
					    	</div>
					    	<div class="pull-right">
				    			<label class="checkbox margined-top-2">
								    <input type="checkbox" tabindex="17" <? if ($data['courts'] == ''){ echo ' checked';} ?> id="courts_no" name="courts_no" value="1" onchange="courtsChanged(this);">
								    Не привлекался (ась)
								</label>
					    	</div>
				    	</div>
				    	<input type="text" class="width816" tabindex="18" <? if ($data['courts'] == ''){ echo ' disabled';} ?> id="courts" name="courts" value="<? echo $data['courts'] ?>" onchange="doSave(false, this)">

				    	<div class="clearfix margined-vertical">
					    	<div class="pull-left">
						    	Имеете ли Вы родственников, работающих в правоохранительных структурах 
								<br>(ГНП, ГНИ, ФСБ, ФАПСИ, Прокуратура, МВД и т.д.)
					    	</div>
					    	<div class="pull-right">
				    			<label class="checkbox margined-top-2">
								    <input type="checkbox" value="" tabindex="19" <? if ($data['police_relatives'] == ''){ echo ' checked';} ?> id="police_relatives_no" name="police_relatives_no" value="1" onchange="policeRelativesChanged(this);">
								    Не имею
								</label>
					    	</div>
				    	</div>
				    	<input type="text" class="width816" tabindex="20" <? if ($data['police_relatives'] == ''){ echo ' disabled';} ?> id="police_relatives" name="police_relatives" value="<? echo $data['police_relatives'] ?>" onchange="doSave(false, this)">

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