<?php
$admin_section = true;
include_once("include/common.php");
include_once("include/security.php");
include_once("include/pagination/manager.php");
include_once("include/pagination/helper.php");

error_reporting(E_ALL);
set_time_limit(0);

$paginationManager = new Krugozor_Pagination_Manager(25, 40, $_REQUEST);

$errorMessage = "";
$successMessage = "";

$query = mysql_query("
			SELECT 
				SQL_CALC_FOUND_ROWS
				CASE 
					WHEN a.last_name <> '' THEN CONCAT_WS( ' ', a.last_name, a.first_name, a.middle_name)
					ELSE u.full_name
				END AS full_name,
				CASE 
					WHEN a.last_name <> '' THEN a.birthday
					ELSE u.birthday
				END AS birthday,
				CASE 
					WHEN u.anketa_file <> '' THEN 100
					WHEN a.internal_document_id > 0 THEN 99
					WHEN a.marital_status_id > 0 THEN 80
					WHEN a.driver_license_num <> '' THEN 60
					WHEN (SELECT COUNT(id) FROM education WHERE anketa_id = a.id) > 0 THEN 40
					WHEN a.last_name <> '' THEN 20
					ELSE 0
				END AS filled_prc,
				a.id as anketa_id,
				u.comments,
				u.id,
				u.entry_date,
				u.active,
				u.login,
				u.last_logged,
				u.anketa_file,
				u.ankeda_date
			FROM user u
			LEFT JOIN anketa a ON a.user_id = u.id
			WHERE u.is_admin = 0
			ORDER BY u.id DESC
			LIMIT " .
           $paginationManager->getStartLimit() . "," .
           $paginationManager->getStopLimit());

$result_found_rows = mysql_query("SELECT FOUND_ROWS() as `count`");
$count = mysql_fetch_assoc($result_found_rows);

$paginationManager->setCount($count["count"]);
include("template_admin.phtml");

?>