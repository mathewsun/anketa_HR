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

$education_types = mysql_query( "SELECT id, name FROM education_type ORDER BY sort_order ");
$training_types = mysql_query( "SELECT id, name FROM training_type ORDER BY sort_order ");
$languages = mysql_query( "SELECT id, name FROM language ORDER BY sort_order ");
$skill_levels = mysql_query( "SELECT id, name FROM skill_level ORDER BY sort_order ");

$educations = mysql_query( "
				SELECT e.*, et.name AS education_type_name, tt.name AS training_type_name
				FROM education e
				INNER JOIN education_type et ON et.id = e.education_type_id
				INNER JOIN training_type tt ON tt.id = e.training_type_id
				WHERE e.anketa_id = ".$anketa_id."
				ORDER BY e.id 
			");
$additional_educations = mysql_query( "
				SELECT e.*
				FROM additional_education e
				WHERE e.anketa_id = ".$anketa_id."
				ORDER BY e.id 
			");
$language_skills = mysql_query( "
				SELECT ls.*, l.name AS language_name, sl.name AS skill_level_name
				FROM language_skills ls
				INNER JOIN language l ON l.id = ls.language_id
				INNER JOIN skill_level sl ON sl.id = ls.skill_level_id
				WHERE ls.anketa_id = ".$anketa_id."
				ORDER BY ls.id 
			");


?>
<!DOCTYPE html>
<html>
	<head>
		<title>Образование</title>
		<meta charset="utf-8">
		<meta content="" name="keywords" />
		<link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <script src="js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src='js/dhtmlxmessage.js'></script>
        <script src="js/site.js"></script>
		<link rel="stylesheet" type="text/css" href="stylesheets/dhtmlxmessage_dhx_skyblue.css">
		<script language="javascript">
			var langArr = [];
			var educationSaved = false;
			var addEducationSaved = false;
			var languageSaved = false;
			
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
					location.href='step1.php?aid=<? echo $anketa_id ?>';
<?
				}
				else
				{
?>
					location.href='step1.php';
<?
				}
?>
			}
			
			function stepDone(){
				window.educationSaved = true;
				window.addEducationSaved = true;
				window.languageSaved = true;
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
					if (!$('#degree_no').is(":checked")){
						if ($('#degree').val() == ''){
							alert_error('Вы не заполнили поле "Учёная степень, звание"');
							return false;
						}
					}
					if ($('#education_type_id').val() != '' && $('#education_type_id').val() != null){
						window.educationSaved = false;
						if (!educationSave(false))
						{
							alert_error('Образование не сохранилось');
							return false;
						}
						else
						{
							window.educationSaved = true;
						}
					}
					if ($('#place').val() != ''){
						window.addEducationSaved = false;
						if (!addEducationSave(false))
						{
							alert_error('Доп. образование не сохранилось');
							return false;
						}
						else
						{
							window.addEducationSaved = true;
						}
					}				
					if ($('#language_id').val() != '' && $('#language_id').val() != null){
						window.languageSaved = false;
						if (!langSave(false))
						{
							alert_error('Знание иностранных языков не сохранилось');
							return false;
						}
						else
						{
							window.languageSaved = true;
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
					if ( window.educationSaved && window.addEducationSaved && window.languageSaved )
					{
						location.href='step3.php';
					}
					else
					{
						window.setTimeout(nextStep(),500);
					}
				}

				function doSave( isAjax, obj){
					nameVal = obj.name
					valueVal = obj.value;
					if (isAjax){
						nameVal = obj.prop("name");
						valueVal = obj.value;
					}
					$.post("process.php", { step: '2', name: nameVal, value: valueVal } );
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
				
				function degreeChanged(obj){
					if(obj.checked){
						$('#degree').val('');

						doSave( true, $('#degree'));

						$('#degree').prop('disabled', true);
					}
					else{
						$('#degree').prop('disabled', false);
					}
				}
				
				var education_id = 0;
				var addEducation_id = 0;
				var language_skill_id = 0;
				
				function educationDelete(proceed_id){
					if (confirm("Удалить строку?")){
						$.post("process.php", 
									{ 
										step: '2.1', 
										action: 'delete',
										id: proceed_id
									},
									function(data){
										location.href="step2.php";
									}
								);
					}
				}
				
				function educationEdit(proceed_id, education_type_id, institution_name, specialty, certificate_num, finish_year, qualification, training_type_id){
					education_id = proceed_id;
					$('#education_type_id').val(education_type_id);
					$('#institution_name').val(institution_name);
					$('#specialty').val(specialty);
					$('#certificate_num').val(certificate_num);
					$('#finish_year').val(finish_year);
					$('#qualification').val(qualification);
					$('#training_type_id').val(training_type_id);

					$('#education_save').val("Сохранить");
					$('#education_save_cancel').show();
				}

				function educationEditCancel(){
					$('#education_type_id').val("");
					$('#institution_name').val("");
					$('#specialty').val("");
					$('#certificate_num').val("");
					$('#finish_year').val("");
					$('#qualification').val("");
					$('#training_type_id').val("");

					$('#education_save').val("Добавить");
					$('#education_save_cancel').hide();
					education_id = 0;
				}

				function educationSave(keepStep){
						fields = "";
						if ($('#education_type_id').val() == '' || $('#education_type_id').val() == null){
							fields += ", Образование";
						}
						if ($('#institution_name').val() == ''){
							fields += ", Учебное заведение";
						}
						if ($('#specialty').val() == ''){
							fields += ", Специальность";
						}
						if ($('#certificate_num').val() == ''){
							fields += ", Диплом: серия, номер";
						}
						if ($('#finish_year').val() == ''){
							fields += ", Год окончания";
						}
						if ($('#qualification').val() == ''){
							fields += ", Квалификация";
						}
						if ($('#training_type_id').val() == '' || $('#training_type_id').val() == null){
							fields += ", Форма";
						}
						if (fields.length > 0){
							alert_error("Вы не заполнили следующие поля при заполнении данных об образовании: " + fields.substring(2));
							return false;
						}

						if (isNaN($('#finish_year').val())){
							alert_error("Год окончания должен быть числом");
							return false;
						}
						birthday_arr = '<?php echo( $data['birthday'] ) ?>'.split("-");
						birthdayDateYear = birthday_arr[0];
						finishYear = parseInt($('#finish_year').val());
						if (finishYear < 1900){
							alert_error("Год окончания должен быть не раньше 1900 года.");
							return false;
						}
						if (birthdayDateYear > finishYear){
							alert_error("Год окончания должен быть больше года рождения");
							return false;
						}
						if (keepStep)
						{
							$.post("process.php", 
										{ 
											step: '2.1', 
											action: 'save',
											id: education_id, 
											education_type_id: $('#education_type_id').val(), 
											institution_name: $('#institution_name').val(), 
											specialty: $('#specialty').val(), 
											certificate_num: $('#certificate_num').val(), 
											finish_year: $('#finish_year').val(), 
											qualification: $('#qualification').val(), 
											training_type_id: $('#training_type_id').val()
										},
										function(data){
											$("#education_rows").html(data);
											educationEditCancel();
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
												step: '2.1', 
												action: 'save',
												id: education_id, 
												education_type_id: $('#education_type_id').val(), 
												institution_name: $('#institution_name').val(), 
												specialty: $('#specialty').val(), 
												certificate_num: $('#certificate_num').val(), 
												finish_year: $('#finish_year').val(), 
												qualification: $('#qualification').val(), 
												training_type_id: $('#training_type_id').val()
											},
										dataType: "html",
										success: function(data){
											$("#education_rows").html(data);
											educationEditCancel();
										}
									});
							return true;
						}
				}
				
				function addEducationDelete(proceed_id){
					if (confirm("Удалить строку?")){
						$.post("process.php", 
									{ 
										step: '2.2', 
										action: 'delete',
										id: proceed_id
									},
									function(data){
										location.href="step2.php";
									}
								);
					}
				}
				
				function addEducationEdit(proceed_id, place, date, duration){
					addEducation_id = proceed_id;
					$('#place').val(place);
					$('#date').val(date);
					$('#duration').val(duration);

					$('#addEducation_save').val("Сохранить");
					$('#addEducation_save_cancel').show();
				}

				function addEducationEditCancel(){
					$('#place').val("");
					$('#date').val("");
					$('#duration').val("");

					$('#addEducation_save').val("Добавить");
					$('#addEducation_save_cancel').hide();
					addEducation_id = 0;
				}

				
				function addEducationSave(keepStep){
						fields = "";
						if ($('#place').val() == ''){
							fields += ", Где, предмет обучения";
						}
						if ($('#date').val() == ''){
							fields += ", Год окончания";
						}
						if ($('#duration').val() == ''){
							fields += ", Продолжительность";
						}
						if (fields.length > 0){
							alert_error("Вы не заполнили следующие поля при заполнении данных о дополнительном образовании: " + fields.substring(2));
							return false;
						}

						if ($('#place').val() == "" || $('#date').val() == "" || $('#duration').val() == ""){
							alert_error("Заполните все поля!");
							return false;
						}
						if (isNaN($('#date').val())){
							alert_error('Поле "Год окончания" должно быть числом');
							return false;
						}
						birthday_arr = '<?php echo( $data['birthday'] ) ?>'.split("-");
						birthdayDateYear = birthday_arr[0];
						finishYear = parseInt($('#date').val());
						if (finishYear < 1900){
							alert_error('Поле "Год окончания" должно быть не раньше 1900 года.');
							return false;
						}
						if (birthdayDateYear > finishYear){
							alert_error('Поле "Год окончания" должно быть больше года рождения');
							return false;
						}
						if (keepStep)
						{
							$.post("process.php", 
										{ 
											step: '2.2', 
											action: 'save',
											id: addEducation_id, 
											place: $('#place').val(), 
											date: $('#date').val(), 
											duration: $('#duration').val()
										},
										function(data){
											$("#addEducation_rows").html(data);
											addEducationEditCancel();
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
												step: '2.2', 
												action: 'save',
												id: addEducation_id, 
												place: $('#place').val(), 
												date: $('#date').val(), 
												duration: $('#duration').val()
											},
										dataType: "html",
										success: function(data){
											$("#addEducation_rows").html(data);
											addEducationEditCancel();
										}
									});
							return true;
						}
				}

				function langDelete(proceed_id){
					if (confirm("Удалить строку?")){
						$.post("process.php", 
									{ 
										step: '2.3', 
										action: 'delete',
										id: proceed_id
									},
									function(data){
										location.href="step2.php";
									}
								);
					}
				}
				
				function langEdit(proceed_id, language_id, skill_level_id){
					language_skill_id = proceed_id;
					$('#language_id').val(language_id);
					$('#skill_level_id').val(skill_level_id);

					$('#lang_save').val("Сохранить");
					$('#lang_save_cancel').show();
				}

				function langEditCancel(){
					$('#language_id').val("");
					$('#skill_level_id').val("");

					$('#lang_save').val("Добавить");
					$('#lang_save_cancel').hide();
					language_skill_id = 0;
				}
				
				function langSave(keepStep){
						fields = "";
						if ($('#language_id').val() == '' || $('#language_id').val() == null){
							fields += ", Язык";
						}
						if ($('#skill_level_id').val() == '' || $('#skill_level_id').val() == null){
							fields += ", Уровень владения";
						}
						if (fields.length > 0){
							alert_error("Вы не заполнили следующие поля при заполнении данных об образовании: " + fields.substring(2));
							return false;
						}

						if( langArr.indexOf($('#language_id').val()) >= 0 && langArr.indexOf($('#language_id').val()) != language_skill_id){
							alert_error("Выбранный язык уже есть в Вашем списке.");
							return false;
						}
						if (keepStep)
						{
							$.post("process.php", 
										{ 
											step: '2.3', 
											action: 'save',
											id: language_skill_id, 
											language_id: $('#language_id').val(), 
											skill_level_id: $('#skill_level_id').val()
										},
										function(data){
											$("#lang_rows").html(data);
											langEditCancel();
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
												step: '2.3',
												async: false,										
												action: 'save',
												id: language_skill_id, 
												language_id: $('#language_id').val(), 
												skill_level_id: $('#skill_level_id').val()
											},
										dataType: "html",
										success: function(data){
											$("#lang_rows").html(data);
											langEditCancel();
										}
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
				<h1>Образование <i>(шаг 2 из 5)</i><span class="to_right">Внимание! Все поля анкеты должны быть заполнены.</span></h1>
			</div>
			<div id="page-container">
				<div id="warning-message">
				</div>
				<form>
				    <h2>Образование <small class="blue-text">(Заполнять таблицу следует в обратном порядке (начиная с последнего места учебы), при отсутствии информации поставьте прочерк (минус))</small></h2>
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="width356">
						    	<label>Образование</label>
								<select tabindex="1" class="width356 no-margin-left" id="education_type_id" name="education_type_id">
									<option></option>
<?php
									while ($row = mysql_fetch_assoc($education_types)) {
?>
										<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
									}
									mysql_free_result($education_types);
?>
								</select>
						    	<label>Специальность</label>
						    	<input type="text" class="width342" tabindex="3" id="specialty" name="specialty" value="<? echo $data['specialty'] ?>">
								<label>Год окончания</label>
								<input type="text" class="width342" tabindex="5" id="finish_year" name="finish_year" value="<? echo $data['finish_year'] ?>"/>
								<label>Форма</label>
								<select class="width356 no-margin-left" tabindex="7" id="training_type_id" name="training_type_id">
									<option></option>
<?php
									while ($row = mysql_fetch_assoc($training_types)) {
?>
										<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
									}
									mysql_free_result($training_types);
?>
								</select>
					    	</div>
					    	<div class="width356">
						    	<label>Учебное заведение</label>
						    	<input type="text" class="width342" tabindex="2" id="institution_name" name="institution_name" value="<? echo $data['institution_name'] ?>">
						    	<label>Диплом: серия, номер</label>
						    	<input type="text" class="width342" tabindex="4" id="certificate_num" name="certificate_num" value="<? echo $data['certificate_num'] ?>">
								<label>Квалификация</label>
								<input type="text" class="width342" tabindex="6" id="qualification" name="qualification" value="<? echo $data['qualification'] ?>"/>
					    	</div>
				    	</div>
				    	<div class="clearfix">
					    	<div class="pull-right">
								<input type="button" tabindex="8" class="btn btn-primary ask padded-line margined-top-2" id="education_save" value="Добавить" onClick="educationSave(true)">
								<input type="button" tabindex="9" class="btn btn-primary ask padded-line margined-top-2" id="education_save_cancel" value="Отменить" onClick="educationEditCancel()" style="display:none">
					    	</div>
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
						    	<td class="sell-4">Образование</td>
						    	<td class="sell-4">Учебное заведение</td>
						    	<td class="sell-4">Специальность</td>
						    	<td class="sell-4">Диплом</td>
						    	<td class="sell-4">Окончание</td>
						    	<td class="sell-4">Квалификация</td>
						    	<td class="sell-4">Форма</td>
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
					    <tbody id="education_rows">
<?php
							while ($row = mysql_fetch_assoc($educations)) {
?>
								<tr>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-document" onClick="educationEdit(<?php echo( $row['id'] ) ?>, '<?php echo( $row['education_type_id'] ) ?>', '<?php echo( getSafeJSString($row['institution_name']) ) ?>', '<?php echo( getSafeJSString($row['specialty']) ) ?>', '<?php echo( getSafeJSString($row['certificate_num']) ) ?>', '<?php echo( getSafeJSString($row['finish_year']) ) ?>', '<?php echo( getSafeJSString($row['qualification']) ) ?>', '<?php echo( $row['training_type_id'] ) ?>'); return false;">&nbsp;</a></td>
<?
									}
?>
									<td class="sell-4"><?php echo( $row['education_type_name'] ) ?></td>
									<td class="sell-4"><?php echo( $row['institution_name'] ) ?></td>
									<td class="sell-4"><?php echo( $row['specialty'] ) ?></td>
									<td class="sell-4"><?php echo( $row['certificate_num'] ) ?></td>
									<td class="sell-4"><?php echo( $row['finish_year'] ) ?></td>
									<td class="sell-4"><?php echo( $row['qualification'] ) ?></td>
									<td class="sell-4"><?php echo( $row['training_type_name'] ) ?></td>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-delete" onClick="educationDelete(<?php echo( $row['id'] ) ?>); return false;">&nbsp;</a></td>
<?
									}
?>
								</tr>
<?php
							}
							mysql_free_result($educations);
?>
					    </tbody>
					</table>			    	


				    <h2>Дополнительное образование <small class="blue-text">(аспирантура, стажировки, тренинги и др.)</small></h2>
			    	<div class="margined-line-34">
					    <div class="row-fluid">
					    	<div class="width356">
						    	<label>Где, предмет обучения</label>
						    	<input type="text" class="width342" tabindex="11" id="place" name="place">
						    	<label>Продолжительность</label>
						    	<input type="text" class="width342" tabindex="13" id="duration" name="duration">
					    	</div>
					    	<div class="width356">
						    	<label>Год окончания</label>
								<input type="text" class="width342" tabindex="12" id="date" name="date">
						    	<div class="clearfix margined-top-mid">
							    	<div class="pull-right">
										<input type="button" tabindex="14" class="btn btn-primary ask padded-line margined-top-2" id="addEducation_save" value="Добавить" onClick="addEducationSave(true)">
										<input type="button" tabindex="15" class="btn btn-primary ask padded-line margined-top-2" id="addEducation_save_cancel" value="Отменить" onClick="addEducationEditCancel()" style="display:none">
							    	</div>
						    	</div>
					    	</div>
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
						    	<td class="sell-3">Где, Предмет обучения</td>
						    	<td class="sell-3">Год окончания</td>
						    	<td class="sell-3">Продолжительность</td>
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
					    <tbody id="addEducation_rows">
<?php
							while ($row = mysql_fetch_assoc($additional_educations)) {
?>
								<tr>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-document" onClick="addEducationEdit(<?php echo( $row['id'] ) ?>, '<?php echo( getSafeJSString($row['place']) ) ?>', '<?php echo( getSafeJSString($row['date']) ) ?>', '<?php echo( getSafeJSString($row['duration']) ) ?>'); return false;">&nbsp;</a></td>
<?
									}
?>
									<td class="sell-3"><?php echo( $row['place'] ) ?></td>
									<td class="sell-3"><?php echo( $row['date'] ) ?></td>
									<td class="sell-3"><?php echo( $row['duration'] ) ?></td>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-delete" onClick="addEducationDelete( <?php echo( $row['id'] ) ?>); return false;">&nbsp;</a></td>
<?
									}
?>
								</tr>
<?php
							}
							mysql_free_result($additional_educations);
?>
					    </tbody>
					</table>

				    <h2>Учёная степень, звание</h2>
			    	<div class="margined-line-34">
				    	<div class="row-fluid">
					    	<div class="clearfix">
					    		<div class="pull-left">
							    	<label>Учёная степень, звание</label>
						    		<input type="text" class="width638" tabindex="16" <? if ($data['degree'] == ''){ echo ' disabled';} ?> id="degree" name="degree" value="<? echo $data['degree'] ?>" onchange="doSave(false, this)">
					    		</div>
					    		<div class="pull-right padded-top-25">
					    			<label class="checkbox">
									    <input type="checkbox" <? if ($data['degree'] == ''){ echo ' checked';} ?> id="degree_no" name="degree_no" value="1" tabindex="17" onchange="degreeChanged(this);">
									    Не имею
									</label>
					    		</div>
					    	</div>
					    </div>
				    </div>

				    <h2>Знание иностранных языков</h2>
			    	<div class="margined-line-34">
					    <div class="row-fluid">
					    	<div class="width356">
						    	<label>Язык</label>
								<select class="width356 no-margin-left" tabindex="18" id="language_id" name="language_id">
									<option></option>
<?php
									while ($row = mysql_fetch_assoc($languages)) {
?>
										<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
									}
									mysql_free_result($languages);
?>
								</select>
					    	</div>
					    	<div class="width356">
						    	<label>Уровень владения</label>
								<select class="width356 no-margin-left" tabindex="19" id="skill_level_id" name="skill_level_id">
									<option></option>
<?php
									while ($row = mysql_fetch_assoc($skill_levels)) {
?>
										<option value="<?php echo( $row['id'] ) ?>"><?php echo( $row['name'] ) ?></option>
<?php
									}
									mysql_free_result($skill_levels);
?>
								</select>
						    	<div class="clearfix">
							    	<div class="pull-right">
										<input type="button" tabindex="20" class="btn btn-primary ask padded-line margined-top-2" id="lang_save" value="Добавить" onClick="langSave(true)">
										<input type="button" tabindex="21" class="btn btn-primary ask padded-line margined-top-2" id="lang_save_cancel" value="Отменить" onClick="langEditCancel()" style="display:none">
							    	</div>
						    	</div>
					    	</div>
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
						    	<td class="sell-2">Язык</td>
						    	<td class="sell-2">Уровень владения</td>
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
					    <tbody id="lang_rows">
<?php
							while ($row = mysql_fetch_assoc($language_skills)) {
?>
								<script language="javascript">
									langArr[<?php echo( $row['id'] ) ?>] = "<?php echo( $row['language_id'] ) ?>";
								</script>
								<tr>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-document" onClick="langEdit(<?php echo( $row['id'] ) ?>, '<?php echo( $row['language_id'] ) ?>', '<?php echo( $row['skill_level_id'] ) ?>'); return false;">&nbsp;</a></td>
<?
									}
?>
									<td class="sell-2"><?php echo( $row['language_name'] ) ?></td>
									<td class="sell-2"><?php echo( $row['skill_level_name'] ) ?></td>
<?
									if ($_SESSION['is_admin'] != "1")
									{
?>
										<td class="icon-sell"><a href="#" class="icon-delete" onClick="langDelete( <?php echo( $row['id'] ) ?>); return false">&nbsp;</a></td>
<?
									}
?>
								</tr>
<?php
							}
							mysql_free_result($language_skills);
?>
					    </tbody>
					</table>

			    	<div class="margined-line-34 padded-top-25">
				    	<div class="clearfix">
				    		<div class="pull-left blue-text">
				    			<button class="btn" name="exit" type="button" tabindex="27" onClick="doLogout();return false;">Выход</button>
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