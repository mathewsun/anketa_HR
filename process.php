<?php
include_once("include/common.php");
include_once("include/security.php");



if(isset($_POST['step']) && $_POST['step'] == "1") {
	if(isset($_POST['name'])) {
		if($_POST['name'] == 'first_name') {
			mysql_query("UPDATE anketa SET first_name='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		} else
		if($_POST['name'] == 'last_name') {
			mysql_query("UPDATE anketa SET last_name='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		} else
		if($_POST['name'] == 'middle_name') {
			mysql_query("UPDATE anketa SET middle_name='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'gender') {
			mysql_query("UPDATE anketa SET gender='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'name_not_changed') {
			mysql_query("UPDATE anketa SET name_not_changed='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'old_first_name') {
			mysql_query("UPDATE anketa SET old_first_name='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'old_last_name') {
			mysql_query("UPDATE anketa SET old_last_name='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'old_middle_name') {
			mysql_query("UPDATE anketa SET old_middle_name='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'name_changed_place') {
			mysql_query("UPDATE anketa SET name_changed_place='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'name_changed_reason') {
			mysql_query("UPDATE anketa SET name_changed_reason='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'name_changed_date') {
			mysql_query("UPDATE anketa SET name_changed_date='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'birthday') {
			mysql_query("UPDATE anketa SET birthday='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'birth_place') {
			mysql_query("UPDATE anketa SET birth_place='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'citizenship_country') {
			mysql_query("UPDATE anketa SET citizenship_country='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'citizenship_not_changed') {
			mysql_query("UPDATE anketa SET citizenship_not_changed='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'citizenship_changed_date') {
			mysql_query("UPDATE anketa SET citizenship_changed_date='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		if($_POST['name'] == 'citizenship_changed_reason') {
			mysql_query("UPDATE anketa SET citizenship_changed_reason='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "2") {
	if(isset($_POST['name'])) {
		if($_POST['name'] == 'degree') {
			mysql_query("UPDATE anketa SET degree='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "2.1") {
	if(isset($_POST['action']) && isset($_POST['id'])) {
		if($_POST['action'] == 'delete') {
			mysql_query("DELETE FROM education WHERE id = '".$_POST['id']."' AND anketa_id = '".$anketa_id."'");
		}
		else if ($_POST['action'] == 'save' && $_POST['id'] == '0') {
			mysql_query("INSERT INTO education (anketa_id, education_type_id, institution_name, specialty, certificate_num, finish_year, qualification, training_type_id) VALUES ('".$anketa_id."', '".mysql_real_escape_string($_POST['education_type_id'])."', '".mysql_real_escape_string($_POST['institution_name'])."', '".mysql_real_escape_string($_POST['specialty'])."', '".mysql_real_escape_string($_POST['certificate_num'])."', '".mysql_real_escape_string($_POST['finish_year'])."', '".mysql_real_escape_string($_POST['qualification'])."', '".mysql_real_escape_string($_POST['training_type_id'])."')");
		}
		else if ($_POST['action'] == 'save' && $_POST['id'] != '0') {
			mysql_query("UPDATE education SET education_type_id = '".mysql_real_escape_string($_POST['education_type_id'])."', institution_name = '".mysql_real_escape_string($_POST['institution_name'])."', specialty = '".mysql_real_escape_string($_POST['specialty'])."', certificate_num = '".mysql_real_escape_string($_POST['certificate_num'])."', finish_year = '".mysql_real_escape_string($_POST['finish_year'])."', qualification = '".mysql_real_escape_string($_POST['qualification'])."', training_type_id = '".mysql_real_escape_string($_POST['training_type_id'])."' WHERE id = '".$_POST['id']."' AND anketa_id = '".$anketa_id."'");
		}
		$educations = mysql_query( "
									SELECT e.*, et.name AS education_type_name, tt.name AS training_type_name
									FROM education e
									INNER JOIN education_type et ON et.id = e.education_type_id
									INNER JOIN training_type tt ON tt.id = e.training_type_id
									WHERE e.anketa_id = ".$anketa_id."
									ORDER BY e.id 
								");

		$res = "";
		while ($row = mysql_fetch_assoc($educations)) {
			$res .= '<tr>
					<td class="icon-sell"><a href="#" class="icon-document" onClick="educationEdit('.$row['id'].', \''.$row['education_type_id'].'\', \''.getSafeJSString($row['institution_name']).'\', \''.getSafeJSString($row['specialty']).'\', \''.getSafeJSString($row['certificate_num']).'\', \''.getSafeJSString($row['finish_year']).'\', \''.getSafeJSString($row['qualification']).'\', \''.$row['training_type_id'].'\'); return false;">&nbsp;</a></td>
					<td class="sell-4">'.$row['education_type_name'].'</td>
					<td class="sell-4">'.$row['institution_name'].'</td>
					<td class="sell-4">'.$row['specialty'].'</td>
					<td class="sell-4">'.$row['certificate_num'].'</td>
					<td class="sell-4">'.$row['finish_year'].'</td>
					<td class="sell-4">'.$row['qualification'].'</td>
					<td class="sell-4">'.$row['training_type_name'].'</td>
					<td class="icon-sell"><a href="#" class="icon-delete" onClick="educationDelete('.$row['id'].'); return false;">&nbsp;</a></td>
				</tr>';
		}
		mysql_free_result($educations);
		echo $res;
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "2.2") {
	if(isset($_POST['action']) && isset($_POST['id'])) {
		if($_POST['action'] == 'delete') {
			mysql_query("DELETE FROM additional_education WHERE id = '".$_POST['id']."' AND anketa_id = '".$anketa_id."'");
		}
		else if ($_POST['action'] == 'save' && $_POST['id'] == '0') {
			mysql_query("INSERT INTO additional_education (anketa_id, place, date, duration) VALUES ('".$anketa_id."', '".mysql_real_escape_string($_POST['place'])."', '".mysql_real_escape_string($_POST['date'])."', '".mysql_real_escape_string($_POST['duration'])."')");
		}
		else if ($_POST['action'] == 'save' && $_POST['id'] != '0') {
			mysql_query("UPDATE additional_education SET place = '".mysql_real_escape_string($_POST['place'])."', date = '".mysql_real_escape_string($_POST['date'])."', duration = '".mysql_real_escape_string($_POST['duration'])."' WHERE id = '".$_POST['id']."' AND anketa_id = '".$anketa_id."'");
		}
		$additional_educations = mysql_query( "
						SELECT e.*
						FROM additional_education e
						WHERE e.anketa_id = ".$anketa_id."
						ORDER BY e.id 
					");
		$res = "";
		while ($row = mysql_fetch_assoc($additional_educations)) {
			$res .= '<tr>
				<td class="icon-sell"><a href="#" class="icon-document" onClick="addEducationEdit('.$row['id'].', \''.getSafeJSString($row['place']).'\', \''.getSafeJSString($row['date']).'\', \''.getSafeJSString($row['duration']).'\'); return false;">&nbsp;</a></td>
				<td class="sell-3">'.$row['place'].'</td>
				<td class="sell-3">'.$row['date'].'</td>
				<td class="sell-3">'.$row['duration'].'</td>
				<td class="icon-sell"><a href="#" class="icon-delete" onClick="addEducationDelete('.$row['id'].'); return false;">&nbsp;</a></td>
			</tr>';
		}
		mysql_free_result($additional_educations);
		echo $res;
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "2.3") {
	if(isset($_POST['action']) && isset($_POST['id'])) {
		if($_POST['action'] == 'delete') {
			mysql_query("DELETE FROM language_skills WHERE id = '".$_POST['id']."' AND anketa_id = '".$anketa_id."'");
		}
		else if ($_POST['action'] == 'save' && $_POST['id'] == '0') {
			mysql_query("INSERT INTO language_skills (anketa_id, language_id, skill_level_id) VALUES ('".$anketa_id."', '".mysql_real_escape_string($_POST['language_id'])."', '".mysql_real_escape_string($_POST['skill_level_id'])."')");
		}
		else if ($_POST['action'] == 'save' && $_POST['id'] != '0') {
			mysql_query("UPDATE language_skills SET language_id = '".mysql_real_escape_string($_POST['language_id'])."', skill_level_id = '".mysql_real_escape_string($_POST['skill_level_id'])."' WHERE id = '".$_POST['id']."' AND anketa_id = '".$anketa_id."'");
		}
		$language_skills = mysql_query( "
						SELECT ls.*, l.name AS language_name, sl.name AS skill_level_name
						FROM language_skills ls
						INNER JOIN language l ON l.id = ls.language_id
						INNER JOIN skill_level sl ON sl.id = ls.skill_level_id
						WHERE ls.anketa_id = ".$anketa_id."
						ORDER BY ls.id 
					");
		$res = '<script language="javascript">langArr = [];</script>';
		while ($row = mysql_fetch_assoc($language_skills)) {
			$res .= '
					<script language="javascript">
						langArr['.$row['id'].'] = "'.$row['language_id'].'";
					</script>
					<tr>
						<td class="icon-sell"><a href="#" class="icon-document" onClick="langEdit('.$row['id'].', \''.$row['language_id'].'\', \''.$row['skill_level_id'].'\'); return false;">&nbsp;</a></td>
						<td class="sell-2">'.$row['language_name'].'</td>
						<td class="sell-2">'.$row['skill_level_name'].'</td>
						<td class="icon-sell"><a href="#" class="icon-delete" onClick="langDelete( '.$row['id'].'); return false">&nbsp;</a></td>
					</tr>';
		}
		mysql_free_result($language_skills);
		echo $res;
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "3") {
	if(isset($_POST['name'])) {
		if($_POST['name'] == 'additional_skills') {
			mysql_query("UPDATE anketa SET additional_skills='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'driver_license_category') {
			mysql_query("UPDATE anketa SET driver_license_category='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'driver_license_num') {
			mysql_query("UPDATE anketa SET driver_license_num='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "3.1") {
	if(isset($_POST['action']) && isset($_POST['id'])) {
		$work_activity_id = $_POST['id'];
		if($_POST['action'] == 'delete') {
			mysql_query("DELETE FROM work_activity WHERE id = '".$_POST['id']."' AND anketa_id = '".$anketa_id."'");
		}
		else if ($_POST['action'] == 'save' && $work_activity_id == '0') {
			mysql_query("INSERT INTO work_activity (anketa_id, date_start, date_end, company_name, position, recommendations, dismiss_reason) VALUES ('".$anketa_id."', '".mysql_real_escape_string($_POST['date_start'])."', '".mysql_real_escape_string($_POST['date_end'])."', '".mysql_real_escape_string($_POST['company_name'])."', '".mysql_real_escape_string($_POST['position'])."', '".mysql_real_escape_string($_POST['recommendations'])."', '".mysql_real_escape_string($_POST['dismiss_reason'])."')");

			$work_activity_id = mysql_insert_id();
		}
		else if ($_POST['action'] == 'save' && $work_activity_id != '0') {
			mysql_query("UPDATE work_activity SET date_start = '".mysql_real_escape_string($_POST['date_start'])."', date_end = '".mysql_real_escape_string($_POST['date_end'])."', company_name = '".mysql_real_escape_string($_POST['company_name'])."', position = '".mysql_real_escape_string($_POST['position'])."', recommendations = '".mysql_real_escape_string($_POST['recommendations'])."', dismiss_reason = '".mysql_real_escape_string($_POST['dismiss_reason'])."' WHERE id = '".$work_activity_id."' AND anketa_id = '".$anketa_id."'");
		}

		$company_place_id = 0;
		$query = mysql_query("SELECT company_place_id FROM work_activity where id='".$work_activity_id."' AND anketa_id = '".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['company_place_id']) && $data['company_place_id'] > 0 ){
				$company_place_id = $data['company_place_id'];
			}
		}

		if($company_place_id > 0) {
			mysql_query("UPDATE address SET postcode='".mysql_real_escape_string($_POST['postcode'])."', region='".mysql_real_escape_string($_POST['region'])."', district='".mysql_real_escape_string($_POST['district'])."', city='".mysql_real_escape_string($_POST['city'])."', location='".mysql_real_escape_string($_POST['location'])."', street='".mysql_real_escape_string($_POST['street'])."', house='".mysql_real_escape_string($_POST['house'])."', block='".mysql_real_escape_string($_POST['block'])."', appt='".mysql_real_escape_string($_POST['appt'])."', phone_country='".mysql_real_escape_string($_POST['phone_country'])."', phone_city='".mysql_real_escape_string($_POST['phone_city'])."', phone_number='".mysql_real_escape_string($_POST['phone_number'])."', phone_add='".mysql_real_escape_string($_POST['phone_add'])."' WHERE id='".$company_place_id."'");
			
		}
		else {
			mysql_query("INSERT INTO address (postcode, region, district, city, location, street, house, block, appt, phone_country, phone_city, phone_number, phone_add) VALUES('".mysql_real_escape_string($_POST['postcode'])."', '".mysql_real_escape_string($_POST['region'])."', '".mysql_real_escape_string($_POST['district'])."', '".mysql_real_escape_string($_POST['city'])."', '".mysql_real_escape_string($_POST['location'])."', '".mysql_real_escape_string($_POST['street'])."', '".mysql_real_escape_string($_POST['house'])."', '".mysql_real_escape_string($_POST['block'])."', '".mysql_real_escape_string($_POST['appt'])."', '".mysql_real_escape_string($_POST['phone_country'])."', '".mysql_real_escape_string($_POST['phone_city'])."', '".mysql_real_escape_string($_POST['phone_number'])."', '".mysql_real_escape_string($_POST['phone_add'])."')");
			
			$company_place_id = mysql_insert_id();
			mysql_query("UPDATE work_activity SET company_place_id='".$company_place_id."' WHERE id='".$work_activity_id."' AND anketa_id = '".$anketa_id."'");
		}
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "4") {
	if(isset($_POST['name'])) {
		if($_POST['name'] == 'marital_status_id') {
			mysql_query("UPDATE anketa SET marital_status_id='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'courts') {
			mysql_query("UPDATE anketa SET courts='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'police_relatives') {
			mysql_query("UPDATE anketa SET police_relatives='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "4.1") {
	if(isset($_POST['action']) && isset($_POST['id'])) {
		$relatives_id = $_POST['id'];
		if($_POST['action'] == 'delete') {
			mysql_query("DELETE FROM relatives WHERE id = '".$relatives_id."' AND anketa_id = '".$anketa_id."'");
		}
		else if ($_POST['action'] == 'save' && $relatives_id == '0') {
			mysql_query("INSERT INTO relatives (anketa_id, relation_id, first_name, last_name, middle_name, birthday, birth_place, old_first_name, old_last_name, old_middle_name, company, position) VALUES ('".$anketa_id."', '".mysql_real_escape_string($_POST['relation_id'])."', '".mysql_real_escape_string($_POST['first_name'])."', '".mysql_real_escape_string($_POST['last_name'])."', '".mysql_real_escape_string($_POST['middle_name'])."', '".mysql_real_escape_string($_POST['birthday'])."', '".mysql_real_escape_string($_POST['birth_place'])."', '".mysql_real_escape_string($_POST['old_first_name'])."', '".mysql_real_escape_string($_POST['old_last_name'])."', '".mysql_real_escape_string($_POST['old_middle_name'])."', '".mysql_real_escape_string($_POST['company'])."', '".mysql_real_escape_string($_POST['position'])."')");

			$relatives_id = mysql_insert_id();
		}
		else if ($_POST['action'] == 'save' && $relatives_id != '0') {
			mysql_query("UPDATE relatives SET relation_id = '".mysql_real_escape_string($_POST['relation_id'])."', first_name = '".mysql_real_escape_string($_POST['first_name'])."', last_name = '".mysql_real_escape_string($_POST['last_name'])."', middle_name = '".mysql_real_escape_string($_POST['middle_name'])."', birthday = '".mysql_real_escape_string($_POST['birthday'])."', birth_place = '".mysql_real_escape_string($_POST['birth_place'])."', old_first_name = '".mysql_real_escape_string($_POST['old_first_name'])."', old_last_name = '".mysql_real_escape_string($_POST['old_last_name'])."', old_middle_name = '".mysql_real_escape_string($_POST['old_middle_name'])."', company = '".mysql_real_escape_string($_POST['company'])."', position = '".mysql_real_escape_string($_POST['position'])."' WHERE id = '".$relatives_id."' AND anketa_id = '".$anketa_id."'");
		}

		$address_id = 0;
		$query = mysql_query("SELECT address_id FROM relatives where id='".$relatives_id."' AND anketa_id = '".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['address_id']) && $data['address_id'] > 0 ){
				$address_id = $data['address_id'];
			}
		}

		if($address_id > 0) {
			mysql_query("UPDATE address SET postcode='".mysql_real_escape_string($_POST['postcode'])."', region='".mysql_real_escape_string($_POST['region'])."', district='".mysql_real_escape_string($_POST['district'])."', city='".mysql_real_escape_string($_POST['city'])."', location='".mysql_real_escape_string($_POST['location'])."', street='".mysql_real_escape_string($_POST['street'])."', house='".mysql_real_escape_string($_POST['house'])."', block='".mysql_real_escape_string($_POST['block'])."', appt='".mysql_real_escape_string($_POST['appt'])."', phone_country='".mysql_real_escape_string($_POST['phone_country'])."', phone_city='".mysql_real_escape_string($_POST['phone_city'])."', phone_number='".mysql_real_escape_string($_POST['phone_number'])."', phone_add='".mysql_real_escape_string($_POST['phone_add'])."' WHERE id='".$address_id."'");
			
		}
		else {
			mysql_query("INSERT INTO address (postcode, region, district, city, location, street, house, block, appt, phone_country, phone_city, phone_number, phone_add) VALUES('".mysql_real_escape_string($_POST['postcode'])."', '".mysql_real_escape_string($_POST['region'])."', '".mysql_real_escape_string($_POST['district'])."', '".mysql_real_escape_string($_POST['city'])."', '".mysql_real_escape_string($_POST['location'])."', '".mysql_real_escape_string($_POST['street'])."', '".mysql_real_escape_string($_POST['house'])."', '".mysql_real_escape_string($_POST['block'])."', '".mysql_real_escape_string($_POST['appt'])."', '".mysql_real_escape_string($_POST['phone_country'])."', '".mysql_real_escape_string($_POST['phone_city'])."', '".mysql_real_escape_string($_POST['phone_number'])."', '".mysql_real_escape_string($_POST['phone_add'])."')");
			
			$address_id = mysql_insert_id();
			mysql_query("UPDATE relatives SET address_id='".$address_id."' WHERE id='".$relatives_id."' AND anketa_id = '".$anketa_id."'");
			echo "UPDATE relatives SET address_id='".$address_id."' WHERE id='".$relatives_id."' AND anketa_id = '".$anketa_id."'";
		}
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "5") {
	if(isset($_POST['name'])) {
		if($_POST['name'] == 'work_restrictions') {
			mysql_query("UPDATE anketa SET work_restrictions='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'additionals') {
			mysql_query("UPDATE anketa SET additionals='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'military_service_obligation') {
			mysql_query("UPDATE anketa SET military_service_obligation='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'military_rank_id') {
			mysql_query("UPDATE anketa SET military_rank_id='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'INN') {
			mysql_query("UPDATE anketa SET INN='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
		else if($_POST['name'] == 'insurance_number') {
			mysql_query("UPDATE anketa SET insurance_number='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$anketa_id."'");
		}
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "5.1") {
		$internal_document_id = 0;
		$query = mysql_query("SELECT internal_document_id FROM anketa where id='".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['internal_document_id']) && $data['internal_document_id'] > 0 ){
				$internal_document_id = $data['internal_document_id'];
			}
		}

		if($internal_document_id > 0) {
			mysql_query("UPDATE document SET document_type_id='".mysql_real_escape_string($_POST['document_type_id'])."', series='".mysql_real_escape_string($_POST['series'])."', number='".mysql_real_escape_string($_POST['number'])."', issued_date='".mysql_real_escape_string($_POST['issued_date'])."', issued_by='".mysql_real_escape_string($_POST['issued_by'])."', division_code='".mysql_real_escape_string($_POST['division_code'])."' WHERE id='".$internal_document_id."'");
		}
		else {
			mysql_query("INSERT INTO document (document_type_id, series, number, issued_date, issued_by, division_code) VALUES('".mysql_real_escape_string($_POST['document_type_id'])."', '".mysql_real_escape_string($_POST['series'])."', '".mysql_real_escape_string($_POST['number'])."', '".mysql_real_escape_string($_POST['issued_date'])."', '".mysql_real_escape_string($_POST['issued_by'])."', '".mysql_real_escape_string($_POST['division_code'])."')");

			$internal_document_id = mysql_insert_id();
			mysql_query("UPDATE anketa SET internal_document_id='".$internal_document_id."' WHERE id='".$anketa_id."'");
		}
}else
if(isset($_POST['step']) && $_POST['step'] == "5.2") {
		$foreign_document_id = 0;
		$query = mysql_query("SELECT foreign_document_id FROM anketa where id='".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['foreign_document_id']) && $data['foreign_document_id'] > 0 ){
				$foreign_document_id = $data['foreign_document_id'];
			}
		}

		if($foreign_document_id > 0) {
			mysql_query("UPDATE document SET document_type_id='".mysql_real_escape_string($_POST['document_type_id'])."', series='".mysql_real_escape_string($_POST['series'])."', number='".mysql_real_escape_string($_POST['number'])."', issued_date='".mysql_real_escape_string($_POST['issued_date'])."', issued_by='".mysql_real_escape_string($_POST['issued_by'])."', division_code='".mysql_real_escape_string($_POST['division_code'])."' WHERE id='".$foreign_document_id."'");
		}
		else {
			mysql_query("INSERT INTO document (document_type_id, series, number, issued_date, issued_by, division_code) VALUES('".mysql_real_escape_string($_POST['document_type_id'])."', '".mysql_real_escape_string($_POST['series'])."', '".mysql_real_escape_string($_POST['number'])."', '".mysql_real_escape_string($_POST['issued_date'])."', '".mysql_real_escape_string($_POST['issued_by'])."', '".mysql_real_escape_string($_POST['division_code'])."')");

			$foreign_document_id = mysql_insert_id();
			mysql_query("UPDATE anketa SET foreign_document_id='".$foreign_document_id."' WHERE id='".$anketa_id."'");
		}
}else
if(isset($_POST['step']) && $_POST['step'] == "5.2.1") {
		$foreign_document_id = 0;
		$query = mysql_query("SELECT foreign_document_id FROM anketa where id='".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['foreign_document_id']) && $data['foreign_document_id'] > 0 ){
				$foreign_document_id = $data['foreign_document_id'];
			}
		}

		if($foreign_document_id > 0) {
			mysql_query("DELETE FROM document WHERE id='".$foreign_document_id."'");
			mysql_query("UPDATE anketa SET foreign_document_id=0 WHERE id='".$anketa_id."'");
		}
}else
if(isset($_POST['step']) && $_POST['step'] == "5.3") {
		$registration_address_id = 0;
		$query = mysql_query("SELECT registration_address_id FROM anketa where id='".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['registration_address_id']) && $data['registration_address_id'] > 0 ){
				$registration_address_id = $data['registration_address_id'];
			}
		}

		if($registration_address_id > 0) {
			mysql_query("UPDATE address SET postcode='".mysql_real_escape_string($_POST['postcode'])."', region='".mysql_real_escape_string($_POST['region'])."', district='".mysql_real_escape_string($_POST['district'])."', city='".mysql_real_escape_string($_POST['city'])."', location='".mysql_real_escape_string($_POST['location'])."', street='".mysql_real_escape_string($_POST['street'])."', house='".mysql_real_escape_string($_POST['house'])."', block='".mysql_real_escape_string($_POST['block'])."', appt='".mysql_real_escape_string($_POST['appt'])."', phone_country='".mysql_real_escape_string($_POST['phone_country'])."', phone_city='".mysql_real_escape_string($_POST['phone_city'])."', phone_number='".mysql_real_escape_string($_POST['phone_number'])."', phone_add='".mysql_real_escape_string($_POST['phone_add'])."' WHERE id='".$registration_address_id."'");
		}
		else {
			mysql_query("INSERT INTO address (postcode, region, district, city, location, street, house, block, appt, phone_country, phone_city, phone_number, phone_add) VALUES('".mysql_real_escape_string($_POST['postcode'])."', '".mysql_real_escape_string($_POST['region'])."', '".mysql_real_escape_string($_POST['district'])."', '".mysql_real_escape_string($_POST['city'])."', '".mysql_real_escape_string($_POST['location'])."', '".mysql_real_escape_string($_POST['street'])."', '".mysql_real_escape_string($_POST['house'])."', '".mysql_real_escape_string($_POST['block'])."', '".mysql_real_escape_string($_POST['appt'])."', '".mysql_real_escape_string($_POST['phone_country'])."', '".mysql_real_escape_string($_POST['phone_city'])."', '".mysql_real_escape_string($_POST['phone_number'])."', '".mysql_real_escape_string($_POST['phone_add'])."')");

			$registration_address_id = mysql_insert_id();
			mysql_query("UPDATE anketa SET registration_address_id='".$registration_address_id."' WHERE id='".$anketa_id."'");
		}
}else
if(isset($_POST['step']) && $_POST['step'] == "5.4") {
		$residence_address_id = 0;
		$query = mysql_query("SELECT residence_address_id FROM anketa where id='".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['residence_address_id']) && $data['residence_address_id'] > 0 ){
				$residence_address_id = $data['residence_address_id'];
			}
		}

		if($residence_address_id > 0) {
			mysql_query("UPDATE address SET postcode='".mysql_real_escape_string($_POST['postcode'])."', region='".mysql_real_escape_string($_POST['region'])."', district='".mysql_real_escape_string($_POST['district'])."', city='".mysql_real_escape_string($_POST['city'])."', location='".mysql_real_escape_string($_POST['location'])."', street='".mysql_real_escape_string($_POST['street'])."', house='".mysql_real_escape_string($_POST['house'])."', block='".mysql_real_escape_string($_POST['block'])."', appt='".mysql_real_escape_string($_POST['appt'])."', phone_country='".mysql_real_escape_string($_POST['phone_country'])."', phone_city='".mysql_real_escape_string($_POST['phone_city'])."', phone_number='".mysql_real_escape_string($_POST['phone_number'])."', phone_add='".mysql_real_escape_string($_POST['phone_add'])."' WHERE id='".$residence_address_id."'");
		}
		else {
			mysql_query("INSERT INTO address (postcode, region, district, city, location, street, house, block, appt, phone_country, phone_city, phone_number, phone_add) VALUES('".mysql_real_escape_string($_POST['postcode'])."', '".mysql_real_escape_string($_POST['region'])."', '".mysql_real_escape_string($_POST['district'])."', '".mysql_real_escape_string($_POST['city'])."', '".mysql_real_escape_string($_POST['location'])."', '".mysql_real_escape_string($_POST['street'])."', '".mysql_real_escape_string($_POST['house'])."', '".mysql_real_escape_string($_POST['block'])."', '".mysql_real_escape_string($_POST['appt'])."', '".mysql_real_escape_string($_POST['phone_country'])."', '".mysql_real_escape_string($_POST['phone_city'])."', '".mysql_real_escape_string($_POST['phone_number'])."', '".mysql_real_escape_string($_POST['phone_add'])."')");
			
			$residence_address_id = mysql_insert_id();
			mysql_query("UPDATE anketa SET residence_address_id='".$residence_address_id."' WHERE id='".$anketa_id."'");
		}
}else
if(isset($_POST['step']) && $_POST['step'] == "5.4.1") {
		$residence_address_id = 0;
		$query = mysql_query("SELECT residence_address_id FROM anketa where id='".$anketa_id."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			if ( is_numeric($data['residence_address_id']) && $data['residence_address_id'] > 0 ){
				$residence_address_id = $data['residence_address_id'];
			}
		}

		if($residence_address_id > 0) {
			mysql_query("DELETE FROM address WHERE id='".$residence_address_id."'");
			mysql_query("UPDATE anketa SET residence_address_id=0 WHERE id='".$anketa_id."'");
		}
}else
if(isset($_POST['step']) && $_POST['step'] == "11") {
	if(isset($_POST['user_id'])) {
		mysql_query("UPDATE user SET comments='".mysql_real_escape_string($_POST['value'])."' WHERE id='".$_POST['user_id']."'");
	}
}else
if(isset($_POST['step']) && $_POST['step'] == "12") {
	if(isset($_POST['user_id'])) {
		$comments = "";
		$anketa_file = "";
		$query = mysql_query("SELECT anketa_file, comments FROM user where id='".$_POST['user_id']."'");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			$comments = nl2br2($data['comments']);
			$anketa_file = $data['anketa_file'];
		}

		$my_file = $anketa_file;
		$my_path = $_SERVER['DOCUMENT_ROOT']."/anketa_files/";
		$my_name = "ABS Group";
		$my_mail = "noreply@absgroup.ru";
		$my_replyto = "noreply@absgroup.ru";
		$my_subject = "Анкета";

		if (!mail_attachment($my_file, $my_path, $sendAnketaTo, $my_mail, $my_name, $my_replyto, $my_subject, $comments)) {
			echo "error";
		}
	}
}

?>
