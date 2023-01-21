<?php
include_once("include/common.php");
include_once("include/security.php");

$query = mysql_query("
			SELECT a.*, 
					CONCAT_WS( ', ', adr.postcode, adr.region, adr.city, adr.street, adr.house) as address_full,
					CONCAT_WS( ', ', adr2.postcode, adr2.region, adr2.city, adr2.street, adr2.house) as address2_full,
					adr.postcode AS address_postcode,
					adr.region AS address_region,
					adr.district AS address_district,
					adr.city AS address_city,
					adr.location AS address_location,
					adr.street AS address_street,
					adr.house AS address_house,
					adr.block AS address_block,
					adr.appt AS address_appt,
					adr.phone_country AS address_phone_country,
					adr.phone_city AS address_phone_city,
					adr.phone_number AS address_phone_number,
					adr.phone_add AS address_phone_add,
					
					adr2.postcode AS address2_postcode,
					adr2.region AS address2_region,
					adr2.district AS address2_district,
					adr2.city AS address2_city,
					adr2.location AS address2_location,
					adr2.street AS address2_street,
					adr2.house AS address2_house,
					adr2.block AS address2_block,
					adr2.appt AS address2_appt,
					adr2.phone_country AS address2_phone_country,
					adr2.phone_city AS address2_phone_city,
					adr2.phone_number AS address2_phone_number,
					adr2.phone_add AS address2_phone_add,
					
					d.document_type_id AS doc_document_type_id, 
					d.series AS doc_series, 
					d.number AS doc_number, 
					d.issued_date AS doc_issued_date, 
					d.issued_by AS doc_issued_by, 
					d.division_code AS doc_division_code,

					d2.document_type_id AS doc2_document_type_id, 
					d2.series AS doc2_series, 
					d2.number AS doc2_number, 
					d2.issued_date AS doc2_issued_date, 
					d2.issued_by AS doc2_issued_by, 
					d2.division_code AS doc2_division_code
			FROM anketa a 
			LEFT JOIN address adr ON adr.id = a.registration_address_id
			LEFT JOIN address adr2 ON adr2.id = a.residence_address_id
			LEFT JOIN document d ON d.id = a.internal_document_id
			LEFT JOIN document d2 ON d2.id = a.foreign_document_id
			WHERE a.id=".$anketa_id." LIMIT 1");
if(mysql_num_rows($query) == 0){
	exit;
}
$data = mysql_fetch_assoc($query);

$military_ranks = mysql_query( "SELECT id, name FROM military_rank ORDER BY name");
$document_types = mysql_query( "SELECT id, name FROM document_type ORDER BY name");

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Дополнительная информация</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
        <script src="js/jquery.maskedinput.min.js"></script>
		<script type="text/javascript" src='js/dhtmlxmessage.js'></script>
        <script src="js/site.js"></script>
		<link rel="stylesheet" type="text/css" href="stylesheets/dhtmlxmessage_dhx_skyblue.css">
		<script language="javascript">
			$(document).ready(function() {
				$("#INN").mask("999999999999");
				$("#insurance_number").mask("999-999-999 99");
				for (i = new Date().getFullYear(); i >= 1900; i--)
				{
					$('#doc_issued_date_y').append($('<option />').val(i).html(i));
					$('#doc2_issued_date_y').append($('<option />').val(i).html(i));
				}				
				for (i = 1; i <= 31; i++)
				{
					show = '' + i;
					if ( i<10 ){
						show = '0' + i;
					}
					$('#doc_issued_date_d').append($('<option />').val(show).html(show));
					$('#doc2_issued_date_d').append($('<option />').val(show).html(show));
				}				

				$('#military_service_obligation').val(<?php echo( $data['military_service_obligation'] ) ?>);
				$('#military_rank_id').val(<?php echo( $data['military_rank_id'] ) ?>);
				showPassportPopup(false);
				showAddressPopup(false);
				showAddress2Popup(false);
				showPassport2Popup(false);
				
				$('#registration_address_id').val(<?php echo( $data['registration_address_id'] ) ?>);
				$('#residence_address_id').val(<?php echo( $data['residence_address_id'] ) ?>);
				$('#internal_document_id').val(<?php echo( $data['internal_document_id'] ) ?>);
				$('#foreign_document_id').val(<?php echo( $data['foreign_document_id'] ) ?>);

				$('#doc_document_type_id').val(<?php echo( $data['doc_document_type_id'] ) ?>);
				$('#doc_series').val('<?php echo( $data['doc_series'] ) ?>');
				$('#doc_number').val('<?php echo( $data['doc_number'] ) ?>');
				$('#doc_issued_by').val('<?php echo( $data['doc_issued_by'] ) ?>');
				$('#doc_division_code').val('<?php echo( $data['doc_division_code'] ) ?>');
				doc_issued_date_arr = '<?php echo( $data['doc_issued_date'] ) ?>'.split("-");
				$('#doc_issued_date_y').val(doc_issued_date_arr[0]);
				$('#doc_issued_date_m').val(doc_issued_date_arr[1]);
				$('#doc_issued_date_d').val(doc_issued_date_arr[2]);

				$('#doc2_document_type_id').val(<?php echo( $data['doc2_document_type_id'] ) ?>);
				$('#doc2_series').val('<?php echo( $data['doc2_series'] ) ?>');
				$('#doc2_number').val('<?php echo( $data['doc2_number'] ) ?>');
				$('#doc2_issued_by').val('<?php echo( $data['doc2_issued_by'] ) ?>');
				$('#doc2_division_code').val('<?php echo( $data['doc2_division_code'] ) ?>');
				doc2_issued_date_arr = '<?php echo( $data['doc2_issued_date'] ) ?>'.split("-");
				$('#doc2_issued_date_y').val(doc2_issued_date_arr[0]);
				$('#doc2_issued_date_m').val(doc2_issued_date_arr[1]);
				$('#doc2_issued_date_d').val(doc2_issued_date_arr[2]);
				
				$('#address_postcode').val('<?php echo( $data['address_postcode'] ) ?>');
				$('#address_region').val('<?php echo( $data['address_region'] ) ?>');
				$('#address_district').val('<?php echo( $data['address_district'] ) ?>');
				$('#address_city').val('<?php echo( $data['address_city'] ) ?>');
				$('#address_location').val('<?php echo( $data['address_location'] ) ?>');
				$('#address_street').val('<?php echo( $data['address_street'] ) ?>');
				$('#address_house').val('<?php echo( $data['address_house'] ) ?>');
				$('#address_block').val('<?php echo( $data['address_block'] ) ?>');
				$('#address_appt').val('<?php echo( $data['address_appt'] ) ?>');
				$('#address_phone_country').val('<?php echo( $data['address_phone_country'] ) ?>');
				$('#address_phone_city').val('<?php echo( $data['address_phone_city'] ) ?>');
				$('#address_phone_number').val('<?php echo( $data['address_phone_number'] ) ?>');
				$('#address_phone_add').val('<?php echo( $data['address_phone_add'] ) ?>');

				$('#address2_postcode').val('<?php echo( $data['address2_postcode'] ) ?>');
				$('#address2_region').val('<?php echo( $data['address2_region'] ) ?>');
				$('#address2_district').val('<?php echo( $data['address2_district'] ) ?>');
				$('#address2_city').val('<?php echo( $data['address2_city'] ) ?>');
				$('#address2_location').val('<?php echo( $data['address2_location'] ) ?>');
				$('#address2_street').val('<?php echo( $data['address2_street'] ) ?>');
				$('#address2_house').val('<?php echo( $data['address2_house'] ) ?>');
				$('#address2_block').val('<?php echo( $data['address2_block'] ) ?>');
				$('#address2_appt').val('<?php echo( $data['address2_appt'] ) ?>');
				$('#address2_phone_country').val('<?php echo( $data['address2_phone_country'] ) ?>');
				$('#address2_phone_city').val('<?php echo( $data['address2_phone_city'] ) ?>');
				$('#address2_phone_number').val('<?php echo( $data['address2_phone_number'] ) ?>');
				$('#address2_phone_add').val('<?php echo( $data['address2_phone_add'] ) ?>');


				setPassportFull();
				setPassport2Full();
				setAddressFull();
				setAddress2Full();
				if($('#residence_address_full').val() == ""){
					$('#sett_address_2').prop('disabled', true);
					$('#residence_address_full').prop('disabled', true);
					$('#address_same').attr('checked', true);
				}
				if($('#foreign_document_full').val() == ""){
					$('#set_no_passport').prop('disabled', true);
					$('#foreign_document_full').prop('disabled', true);
					$('#no_passport').attr('checked', true);
				}
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
					location.href='step4.php?aid=<? echo $anketa_id ?>';
<?
				}
				else
				{
?>
					location.href='step4.php';
<?
				}
?>
			}
			
			function showPassportPopup(show){
				if (show) {
					$('#passport_popup').show();
				}
				else{
					$('#passport_popup').hide();
				}
			}
			
			function showAddressPopup(show){
				if (show) {
					$('#address_popup').show();
				}
				else{
					$('#address_popup').hide();
				}
			}
			
			function showAddress2Popup(show){
				if (show) {
					$('#address2_popup').show();
				}
				else{
					$('#address2_popup').hide();
				}
			}
			
			function showPassport2Popup(show){
				if (show) {
					$('#passport2_popup').show();
				}
				else{
					$('#passport2_popup').hide();
				}
			}

			function setPassportFull(){
				show_string = "";
				if ($('#doc_series').val() != "") {
					show_string = $('#doc_series').val() + " " + $('#doc_number').val() + " выдан " + $('#doc_issued_date_d').val() + "." + $('#doc_issued_date_m').val() + "." + $('#doc_issued_date_y').val() + " " + $('#doc_issued_by').val();
				}
				$('#internal_document_full').val(show_string);
			}
			
			function setPassport2Full(){
				show_string = "";
				if ($('#doc2_series').val() != "") {
					show_string = $('#doc2_series').val() + " " + $('#doc2_number').val() + " выдан " + $('#doc2_issued_date_d').val() + "." + $('#doc2_issued_date_m').val() + "." + $('#doc2_issued_date_y').val() + " " + $('#doc2_issued_by').val();
				}
				$('#foreign_document_full').val(show_string);
			}
			
			function setAddressFull(){
				show_string = "";
				if ($('#address_city').val() != "") {
					show_string = $('#address_postcode').val() + " " + $('#address_region').val() + " " + $('#address_city').val() + " " + $('#address_location').val() + " " + $('#address_street').val() + " " + $('#address_house').val() + " " + $('#address_block').val() + " " + $('#address_appt').val();
				}
				$('#registration_address_full').val(show_string);
			}
			
			function setAddress2Full(){
				show_string = "";
				if ($('#address2_city').val() != "") {
					show_string = $('#address2_postcode').val() + " " + $('#address2_region').val() + " " + $('#address2_city').val() + " " + $('#address2_location').val() + " " + $('#address2_street').val() + " " + $('#address2_house').val() + " " + $('#address2_block').val() + " " + $('#address2_appt').val();
				}
				$('#residence_address_full').val(show_string);
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
				function stepDone(){
					if (!checkPassport()){
						return false;
					}
					if (!checkPassport2()){
						return false;
					}
					if (!checkAddress()){
						return false;
					}
					if (!checkAddress2()){
						return false;
					}

					if ($('#military_service_obligation').val() == '' || $('#military_service_obligation').val() == null){
						alert_error('Вы не заполнили поле "Отношение к воинской обязанности"');
						return false;
					}
					else if ( $('#military_service_obligation').val() == "1" && ($('#military_rank_id').val() == '' || $('#military_rank_id').val() == null)){
						alert_error('Вы не заполнили поле "Воинское звание"');
						return false;
					}
					if (!$('#work_restrictions_no').is(":checked")){
						if ($('#work_restrictions').val() == ''){
							alert_error('Вы не заполнили поле "Наличие ограничений по условиям труда"');
							return false;
						}
					}
					if (!$('#additionals_no').is(":checked")){
						if ($('#additionals').val() == ''){
							alert_error('Вы не заполнили поле "Дополнительные сведения"');
							return false;
						}
					}
					if (!$('#INN_no').is(":checked")){
						if ($('#INN').val() == ''){
							alert_error('Вы не заполнили поле "ИНН"');
							return false;
						}
					}
					if (!$('#insurance_number_no').is(":checked")){
						if ($('#insurance_number').val() == ''){
							alert_error('Вы не заполнили поле "Страховой № в ПФР"');
							return false;
						}
					}
					
					location.href='finish.php';
				}

				function checkPassport(){
					fields = "";
					if ($('#doc_document_type_id').val() == '' || $('#doc_document_type_id').val() == null){
						fields += ", Вид документа";
					}
					if ($('#doc_series').val() == ''){
						fields += ", Серия";
					}
					if ($('#doc_number').val() == ''){
						fields += ", Номер";
					}
					if ($('#doc_issued_date_d').prop('selectedIndex') < 1 || $('#doc_issued_date_m').prop('selectedIndex') < 1 || $('#doc_issued_date_y').prop('selectedIndex') < 1){
						fields += ", Дата выдачи";
					}
					if ($('#doc_issued_by').val() == ''){
						fields += ", Кем выдан";
					}
					if (fields.length > 0){
						alert_error("Вы не заполнили следующие поля при заполнении данных паспорта: " + fields.substring(2));
						return false;
					}
					return true;
				}

				function checkPassport2(){
					if (!$('#no_passport').is(":checked")){
						fields = "";
						if ($('#doc2_document_type_id').val() == '' || $('#doc2_document_type_id').val() == null){
							fields += ", Вид документа";
						}
						if ($('#doc2_series').val() == ''){
							fields += ", Серия";
						}
						if ($('#doc2_number').val() == ''){
							fields += ", Номер";
						}
						if ($('#doc2_issued_date_d').prop('selectedIndex') < 1 || $('#doc2_issued_date_m').prop('selectedIndex') < 1 || $('#doc2_issued_date_y').prop('selectedIndex') < 1){
							fields += ", Дата выдачи";
						}
						if ($('#doc2_issued_by').val() == ''){
							fields += ", Кем выдан";
						}
						if (fields.length > 0){
							alert_error("Вы не заполнили следующие поля при заполнении данныз загранпаспорта: " + fields.substring(2));
							return false;
						}

						birthdayDateObj = new Date($('#doc2_issued_date_y').val(), $('#doc2_issued_date_m').val()-1, $('#doc2_issued_date_d').val() );
						//if(birthdayDateObj.getFullYear() != parseInt($('#doc2_issued_date_y').val()) || birthdayDateObj.getMonth() != parseInt($('#doc2_issued_date_m').val())-1 || birthdayDateObj.getDate() != parseInt($('#doc2_issued_date_d').val())){
						//	alert_error("Некорректная дата выдачи");
						//	return false;
						//}
					}
					return true;
				}
				
				function checkAddress(){
					fields = "";
					if ($('#address_city').val() == ''){
						fields += ", Город";
					}
					if ($('#address_street').val() == ''){
						fields += ", Улица";
					}
					if ($('#address_house').val() == ''){
						fields += ", Дом";
					}
					if (fields.length > 0){
						alert_error("Вы не заполнили следующие поля при заполнении адреса регистрации: " + fields.substring(2));
						return false;
					}
					return true;
				}
				
				function checkAddress2(){
					if (!$('#address_same').is(":checked")){
						fields = "";
						if ($('#address2_city').val() == ''){
							fields += ", Город";
						}
						if ($('#address2_street').val() == ''){
							fields += ", Улица";
						}
						if ($('#address2_house').val() == ''){
							fields += ", Дом";
						}
						if (fields.length > 0){
							alert_error("Вы не заполнили следующие поля при заполнении адреса фактического проживания: " + fields.substring(2));
							return false;
						}
					}
					return true;
				}

				function savePassportPopup(){
					if (!checkPassport()){
						return;
					}

					birthdayDateObj = new Date($('#doc_issued_date_y').val(), $('#doc_issued_date_m').val()-1, $('#doc_issued_date_d').val() );
					//if(birthdayDateObj.getFullYear() != parseInt($('#doc_issued_date_y').val()) || birthdayDateObj.getMonth() != parseInt($('#doc_issued_date_m').val())-1 || birthdayDateObj.getDate() != parseInt($('#doc_issued_date_d').val())){
					//	alert_error("Некорректная дата выдачи");
					//	return;
					//}

					doc_issued_date_value = "0000-00-00";
					objYvalue = $('#doc_issued_date_y').val();
					objMvalue = $('#doc_issued_date_m').val();
					objDvalue = $('#doc_issued_date_d').val();
					if (objYvalue != '' && objMvalue != '' && objDvalue != ''){
						doc_issued_date_value = '' + objYvalue + '-' + objMvalue + '-' + objDvalue;
					}

					$.post("process.php", { 
											step: '5.1', 
											internal_document_id: $('#internal_document_id').val(), 
											document_type_id: $('#doc_document_type_id').val(), 
											series: $('#doc_series').val(), 
											number: $('#doc_number').val(), 
											issued_date: doc_issued_date_value, 
											issued_by: $('#doc_issued_by').val(), 
											division_code: $('#doc_division_code').val()
										} );
					showPassportPopup(false);
					setPassportFull();
				}
				
				function saveAddressPopup(){
					if (!checkAddress()){
						return;
					}

					$.post("process.php", { 
											step: '5.3', 
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
										} );
					showAddressPopup(false);
					setAddressFull();
				}
				
				function saveAddress2Popup(){
					if (!checkAddress2()){
						return;
					}

					$.post("process.php", { 
											step: '5.4', 
											postcode: $('#address2_postcode').val(), 
											region: $('#address2_region').val(), 
											district: $('#address2_district').val(), 
											city: $('#address2_city').val(), 
											location: $('#address2_location').val(), 
											street: $('#address2_street').val(), 
											house: $('#address2_house').val(), 
											block: $('#address2_block').val(), 
											appt: $('#address2_appt').val(), 
											phone_country: $('#address2_phone_country').val(), 
											phone_city: $('#address2_phone_city').val(), 
											phone_number: $('#address2_phone_number').val(), 
											phone_add: $('#address2_phone_add').val()
										} );
					showAddress2Popup(false);
					setAddress2Full();
				}
				
				function savePassport2Popup(){
					if (!checkPassport2()){
						return;
					}

					doc2_issued_date_value = "0000-00-00";
					objYvalue = $('#doc2_issued_date_y').val();
					objMvalue = $('#doc2_issued_date_m').val();
					objDvalue = $('#doc2_issued_date_d').val();
					if (objYvalue != '' && objMvalue != '' && objDvalue != ''){
						doc2_issued_date_value = '' + objYvalue + '-' + objMvalue + '-' + objDvalue;
					}

					$.post("process.php", { 
											step: '5.2', 
											foreign_document_id: $('#foreign_document_id').val(), 
											document_type_id: $('#doc2_document_type_id').val(), 
											series: $('#doc2_series').val(), 
											number: $('#doc2_number').val(), 
											issued_date: doc2_issued_date_value, 
											issued_by: $('#doc2_issued_by').val(), 
											division_code: $('#doc2_division_code').val()
										} );
					showPassport2Popup(false);
					setPassport2Full();
				}
				
				function doSave( isAjax, obj){
					nameVal = obj.name
					valueVal = obj.value;
					if (isAjax){
						nameVal = obj.prop("name");
						valueVal = obj.value;
					}
					$.post("process.php", { step: '5', name: nameVal, value: valueVal } );
				}

				function doSameAddress(obj){
					if(obj.checked){
						$('#address2_postcode').val('');
						$('#address2_region').val('');
						$('#address2_district').val('');
						$('#address2_city').val('');
						$('#address2_location').val('');
						$('#address2_street').val('');
						$('#address2_house').val('');
						$('#address2_block').val('');
						$('#address2_appt').val('');
						$('#address2_phone_country').val('');
						$('#address2_phone_city').val('');
						$('#address2_phone_number').val('');
						$('#address2_phone_add').val('');
						setAddress2Full();
						$('#sett_address_2').prop('disabled', true);
						$('#residence_address_full').prop('disabled', true);
						$.post("process.php", { 
												step: '5.4.1'
											} );
					}
					else{
						$('#sett_address_2').prop('disabled', false);
						$('#residence_address_full').prop('disabled', false);
					}
				}

				function doNoPassport(obj){
					if(obj.checked){
						$('#foreign_document_id').val('0'); 
						$('#doc2_document_type_id').val('');
						$('#doc2_series').val('');
						$('#doc2_number').val('');
						$('#doc2_issued_date_y').prop('selectedIndex', 0);
						$('#doc2_issued_date_m').prop('selectedIndex', 0);
						$('#doc2_issued_date_d').prop('selectedIndex', 0);
						$('#doc2_issued_by').val('');
						$('#doc2_division_code').val('');
						setPassport2Full();
						$('#set_no_passport').prop('disabled', true);
						$('#foreign_document_full').prop('disabled', true);
						$.post("process.php", { 
												step: '5.2.1'
											} );
					}
					else{
						$('#set_no_passport').prop('disabled', false);
						$('#foreign_document_full').prop('disabled', false);
					}
				}

				function doSaveCheckbox(obj){
					if(obj.checked){
						$.post("process.php", { step: '5', name: obj.name, value: "1" } );
					}
					else{
						$.post("process.php", { step: '5', name: obj.name, value: "0" } );
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
				
				function workRestrictionsChanged(obj){
					if(obj.checked){
						$('#work_restrictions').val('');

						doSave( true, $('#work_restrictions'));

						$('#work_restrictions').prop('disabled', true);
					}
					else{
						$('#work_restrictions').prop('disabled', false);
					}
				}

				function additionalsChanged(obj){
					if(obj.checked){
						$('#additionals').val('');

						doSave( true, $('#additionals'));

						$('#additionals').prop('disabled', true);
					}
					else{
						$('#additionals').prop('disabled', false);
					}
				}

				function innChanged(obj){
					if(obj.checked){
						$('#INN').val('');

						doSave( true, $('#INN'));

						$('#INN').prop('disabled', true);
					}
					else{
						$('#INN').prop('disabled', false);
					}
				}

				function insuranceNumberChanged(obj){
					if(obj.checked){
						$('#insurance_number').val('');

						doSave( true, $('#insurance_number'));

						$('#insurance_number').prop('disabled', true);
					}
					else{
						$('#insurance_number').prop('disabled', false);
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
				<h1>Дополнительная информация <i>(шаг 5 из 5)</i><span class="to_right">Внимание! Все поля анкеты должны быть заполнены.</span></h1>
			</div>
			<div id="page-container">
				<div id="warning-message">
				</div>
				<form>
					<input type="hidden" id="internal_document_id" name="internal_document_id">
					<input type="hidden" id="foreign_document_id" name="foreign_document_id">
					<input type="hidden" id="registration_address_id" name="registration_address_id">
					<input type="hidden" id="residence_address_id" name="residence_address_id">
					<div class="margined-line-34">


				    	<div class="clearfix important-data">
					    	<div class="pull-left relative">
						    	<textarea type="text" class="important-data-input" id="internal_document_full" onfocus="showPassportPopup(true); this.blur(); return false;"></textarea>
					    		<div class="input-label">Паспорт</div>
					    	</div>
					    	<div class="pull-right">
								<button class="btn btn-primary ask" type="button" onClick="showPassportPopup(true); return false;">Задать</button>
					    	</div>

								<div class="popup__overlay" id="passport_popup" style="display:none;">
								    <div class="popup one">
									    	<label>Вид документа</label>
											<select class="width356 no-margin-left" id="doc_document_type_id" name="doc_document_type_id">
												<option></option>
<?php
												while ($row = mysql_fetch_assoc($document_types)) {
?>
													<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
												}
												//mysql_free_result($military_ranks);
?>
											</select>
											<div class="row-fluid">
										    	<div class="width163">
											    	<label>Серия</label>
											    	<input type="text" class="width149" id="doc_series" name="doc_series">
												</div>
										    	<div class="width163">
											    	<label>Номер</label>
											    	<input type="text" class="width149" id="doc_number" name="doc_number">
												</div>
									    	</div>
								    		<label>Дата выдачи</label>
											<select class="day" id="doc_issued_date_d" name="doc_issued_date_d">
												<option value=""></option>
											</select>
											<select class="month" id="doc_issued_date_m" name="doc_issued_date_m">
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
											<select  class="year" id="doc_issued_date_y" name="doc_issued_date_y">
												<option value=""></option>
											</select>
									    	<label>Кем выдан</label>
											<input type="text" class="width342" id="doc_issued_by" name="doc_issued_by">
											<label>Код подразделения</label>
											<input type="text" class="width149" id="doc_division_code" name="doc_division_code">
											<div class="clearfix">
									    		<div class="pull-right">
													<button class="btn btn-primary ask margined-top-23 padded-line" type="button" onClick="savePassportPopup(); return false;">ОК</button>
									    			<button class="btn btn-primary ask margined-top-23 padded-line" type="button" onClick="showPassportPopup(false); return false;">Отмена</button>
									    		</div>
									    	</div>
					    			</div>
					    			<a href="#" class="close one" onClick="showPassportPopup(false); return false;">&nbsp;</a>
					    		</div>
						</div>
				    	<div class="clearfix important-data">
					    	<div class="pull-left relative">
						    	<textarea type="text" class="important-data-input" id="registration_address_full" onfocus="showAddressPopup(true); this.blur(); return false;"></textarea>
					    		<div class="input-label">Адрес регистрации</div>
					    	</div>
					    	<div class="pull-right">
								<button class="btn btn-primary ask" type="button" onClick="showAddressPopup(true); return false;">Задать</button>
					    	</div>
								<div class="popup__overlay" id="address_popup" style="display:none;">
								    <div class="popup two">
											<h2>Адрес регистрации</h2>
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
									    			<button class="btn btn-primary ask padded-line margined-top-2" type="button" onClick="saveAddressPopup(); return false;">ОК</button>
									    			<button class="btn btn-primary ask padded-line margined-top-2" type="button" onClick="showAddressPopup(false); return false;">Отмена</button>
									    		</div>
									    	</div>
					    			</div>
					    			<a href="#" class="close two" onClick="showAddressPopup(false); return false;">&nbsp;</a>
					    		</div>

						</div>
				    	<div class="clearfix important-data">
					    	<div class="pull-left relative">
						    	<textarea type="text" class="important-data-input" id="residence_address_full" onfocus="showAddress2Popup(true); this.blur(); return false;"></textarea>
					    		<div class="input-label width479">Адрес фактического проживания</div>
				    			<label class="checkbox input-label-checkbox width479">
								    <input type="checkbox" value="" id="address_same" name="address_same" onClick="doSameAddress(this)">
								    Совпадает с адресом регистрации
								</label>
					    	</div>
					    	<div class="pull-right">
								<button class="btn btn-primary ask" type="button" id="sett_address_2" name="sett_address_2" onClick="showAddress2Popup(true); return false;">Задать</button>
					    	</div>
								<div class="popup__overlay" id="address2_popup" style="display:none;">
								    <div class="popup two">
											<h2>Адрес проживания</h2>
											<div class="row-fluid">
										    	<div class="width74">
													<label>Индекс</label>
													<input type="text" class="width60" id="address2_postcode" name="address2_postcode">
												</div>
												<div class="width178">
													<label>Регион</label>
													<input type="text" class="width253" id="address2_region" name="address2_region">
												</div>
											</div>
									    	<label>Район</label>
											<input type="text" class="width342 no-margin-left" id="address2_district" name="address2_district">
									    	<label>Город*</label>
											<input type="text" class="width342 no-margin-left" id="address2_city" name="address2_city">
											<div class="row-fluid">
										    	<div class="width153 no-margin-left">
													<label>Населённый пункт</label>
													<input type="text" class="width159 no-margin-left" id="address2_location" name="address2_location">
												</div>
												<div class="width153">
													<label>Улица*</label>
													<input type="text" class="width159 no-margin-left" id="address2_street" name="address2_street">
												</div>
											</div>
											<div class="row-fluid">
										    	<div class="width74">
								    				<label class="font12">Дом*</label>
													<input type="text" class="width60" id="address2_house" name="address2_house">
												</div>
												<div class="width74 margined-left">
										    		<label class="font12">Корпус</label>
													<input type="text" class="width60" id="address2_block" name="address2_block">
												</div>
												<div class="width178 no-margin-right">
										    		<label class="font12">Квартира(офис)</label>
													<input type="text" class="width164" id="address2_appt" name="address2_appt">
												</div>
											</div>
											<h2>Номер телефона</h2>
											<div class="row-fluid">
										    	<div class="width74">
								    				<label class="font12">Код страны</label>
													<input type="text" class="width60" id="address2_phone_country" name="address2_phone_country">
												</div>
												<div class="width74 margined-left">
										    		<label class="font12">Код города</label>
													<input type="text" class="width60" id="address2_phone_city" name="address2_phone_city">
												</div>
												<div class="width178 no-margin-right">
										    		<label class="font12">Телефон</label>
													<input type="text" class="width164" id="address2_phone_number" name="address2_phone_number">
												</div>
										    	<div class="width178_2">
								    				<label class="font12">Добавочный</label>
													<input type="text" class="width164" id="address2_phone_add" name="address2_phone_add">
												</div>
											</div>
											<div class="clearfix">
									    		<div class="pull-right">
									    			<button class="btn btn-primary ask padded-line margined-top-2" type="button" onClick="saveAddress2Popup(); return false;">ОК</button>
									    			<button class="btn btn-primary ask padded-line margined-top-2" type="button" onClick="showAddress2Popup(false); return false;">Отмена</button>
									    		</div>
									    	</div>
					    			</div>
					    			<a href="#" class="close two" onClick="showAddress2Popup(false); return false;">&nbsp;</a>
					    		</div>
				    	</div>
				    	<div class="clearfix important-data">
					    	<div class="pull-left relative">
						    	<textarea type="text" class="important-data-input" id="foreign_document_full" onfocus="showPassport2Popup(true); this.blur(); return false;"></textarea>
					    		<div class="input-label width297">Загранпаспорт</div>
				    			<label class="checkbox input-label-checkbox width297">
								    <input type="checkbox" value="" id="no_passport" name="no_passport" onClick="doNoPassport(this)">
								    Не имею загранпаспорта
								</label>
					    	</div>
					    	<div class="pull-right">
								<button class="btn btn-primary ask" type="button" id="set_no_passport" name="set_no_passport" onClick="showPassport2Popup(true); return false;">Задать</button>
					    	</div>
								<div class="popup__overlay" id="passport2_popup" style="display:none;">
								    <div class="popup one">
									    	<label>Вид документа</label>
											<select class="width356 no-margin-left" id="doc2_document_type_id" name="doc2_document_type_id">
												<option></option>
<?php
												mysql_data_seek($document_types, 0);
												while ($row = mysql_fetch_assoc($document_types)) {
?>
													<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
												}
												//mysql_free_result($military_ranks);
?>
											</select>
											<div class="row-fluid">
										    	<div class="width163">
											    	<label>Серия</label>
											    	<input type="text" class="width149" id="doc2_series" name="doc2_series">
												</div>
										    	<div class="width163">
											    	<label>Номер</label>
											    	<input type="text" class="width149" id="doc2_number" name="doc2_number">
												</div>
									    	</div>
								    		<label>Дата выдачи</label>
											<select class="day" id="doc2_issued_date_d" name="doc2_issued_date_d">
												<option value=""></option>
											</select>
											<select class="month" id="doc2_issued_date_m" name="doc2_issued_date_m">
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
											<select  class="year" id="doc2_issued_date_y" name="doc2_issued_date_y">
												<option value=""></option>
											</select>
									    	<label>Кем выдан</label>
											<input type="text" class="width342" id="doc2_issued_by" name="doc2_issued_by">
											<label>Код подразделения</label>
											<input type="text" class="width149" id="doc2_division_code" name="doc2_division_code">

											<div class="clearfix">
									    		<div class="pull-right">
										    			<button class="btn btn-primary ask margined-top-23 padded-line" type="button" onClick="savePassport2Popup(); return false;">ОК</button>
														<button class="btn btn-primary ask margined-top-23 padded-line" type="button" onClick="showPassport2Popup(false); return false;">Отмена</button>
									    		</div>
									    	</div>
					    			</div>
					    			<a href="#" class="close one" onClick="showPassport2Popup(false); return false;">&nbsp;</a>
					    		</div>
				    	</div>
					</div>

				    <h2>Отношение к воинской обязанности</h2>
			    	<div class="margined-line-34">
					    <div class="row-fluid">
					    	<div class="width356">
						    	<label>Отношение к воинской обязанности</label>
								<select class="width356 no-margin-left" id="military_service_obligation" name="military_service_obligation" onchange="doSave(false, this)">
									<option></option>
									<option value="0">Не военнообязан</option>
									<option value="1">Военнообязан</option>
								</select>
					    	</div>
					    	<div class="width356">
						    	<label>Воинское звание</label>
								<select class="width356 no-margin-left" id="military_rank_id" name="military_rank_id" onchange="doSave(false, this)">
									<option></option>
<?php
									while ($row = mysql_fetch_assoc($military_ranks)) {
?>
										<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
									}
									mysql_free_result($military_ranks);
?>
								</select>
					    	</div>
				    	</div>
				    </div>


					<h2 class="no-padding-bottom">Наличие ограничений по условиям труда</h2>
			    	<div class="margined-line-34">
			    		<small class="blue-text">(по состоянию здоровья, по инвалидности, по уходу за членами семьи, другие)</small>
				    	<div class="clearfix margined-vertical">
				    		<div class="pull-left ">
					    		<input type="text" class="width638" tabindex="20" <? if ($data['work_restrictions'] == ''){ echo ' disabled';} ?> id="work_restrictions" name="work_restrictions" value="<? echo $data['work_restrictions'] ?>" onchange="doSave(false, this)">
				    		</div>
				    		<div class="pull-right margined-top-2">
				    			<label class="checkbox">
								    <input type="checkbox" tabindex="21" <? if ($data['work_restrictions'] == ''){ echo ' checked';} ?> id="work_restrictions_no" name="work_restrictions_no" value="1" onchange="workRestrictionsChanged(this);">
								    Не имею
								</label>
				    		</div>
				    	</div>
				    </div>

					<h2 class="no-padding-bottom">Дополнительные сведения важные для решения вопроса о приеме Вас на работу</h2>
			    	<div class="margined-line-34">
				    	<div class="clearfix margined-vertical">
				    		<div class="pull-left ">
					    		<input type="text" class="width638" tabindex="22" <? if ($data['additionals'] == ''){ echo ' disabled';} ?> id="additionals" name="additionals" value="<? echo $data['additionals'] ?>" onchange="doSave(false, this)">
				    		</div>
				    		<div class="pull-right margined-top-2">
				    			<label class="checkbox">
								    <input type="checkbox" tabindex="23" <? if ($data['additionals'] == ''){ echo ' checked';} ?> id="additionals_no" name="additionals_no" value="1" onchange="additionalsChanged(this);">
								    Нет доп. сведений
								</label>
				    		</div>
				    	</div>
				    </div>

				    <h2>ИНН</h2>
			    	<div class="margined-line-34">
					    <div class="row-fluid">
					    	<div class="width356">
						    	<label>ИНН</label>
						    	<div class="clearfix">
									<input type="text" class="width164 pull-left" tabindex="24" <? if ($data['INN'] == ''){ echo ' disabled';} ?> id="INN" name="INN" value="<? echo $data['INN'] ?>" onchange="doSave(false, this)">
						    		<div class="pull-right margined-top-2">
						    			<label class="checkbox">
										    <input type="checkbox" tabindex="25" <? if ($data['INN'] == ''){ echo ' checked';} ?> id="INN_no" name="INN_no" value="1" onchange="innChanged(this);">
										    Нет ИНН
										</label>
						    		</div>									
					    		</div>
					    	</div>
					    	<div class="width356">
						    	<label>Страховой № в ПФР</label>
						    	<div class="clearfix">
									<input type="text" class="width164 pull-left" tabindex="26" <? if ($data['insurance_number'] == ''){ echo ' disabled';} ?> id="insurance_number" name="insurance_number" value="<? echo $data['insurance_number'] ?>" onchange="doSave(false, this)">
						    		<div class="pull-right margined-top-2">
						    			<label class="checkbox">
										    <input type="checkbox" tabindex="27" <? if ($data['insurance_number'] == ''){ echo ' checked';} ?> id="insurance_number_no" name="insurance_number_no" value="1" onchange="insuranceNumberChanged(this);">
										    Нет стр. №
										</label>
						    		</div>									
					    		</div>
					    	</div>
				    	</div>
				    </div>

			    	<div class="margined-line-34 padded-top-25">
				    	<div class="clearfix">
				    		<div class="pull-left blue-text">
				    			<button class="btn" name="exit" type="button" tabindex="27" onClick="doLogout();return false;">Выход</button>
				    		</div>
				    		<div class="pull-right">
								<button class="btn btn-primary" name="prev" type="button" tabindex="25" onClick="stepBack();return false;">&laquo; Назад</button>
<?
								if ($_SESSION['is_admin'] != "1")
								{
?>
									<button class="btn btn-primary" name="next" type="button" tabindex="26" onClick="stepDone();return false;">Далее &raquo;</button>
<?
								}
?>
				    		</div>
					    </div>
			    	</div>
				</form>
				
			</div>

		</div>
	</body>
</html>