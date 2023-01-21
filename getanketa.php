<?php
$admin_section = true;
include_once("include/common.php");
include_once("include/security.php");

$id = $_GET['id'];

$query = mysql_query("SELECT anketa_file FROM user WHERE id='".$id."' LIMIT 1");
$data = mysql_fetch_assoc($query);

if($data['anketa_file'] != "")
{
	$file = $data['anketa_file'];
	$res = file_get_contents("anketa_files/".$file);

	header("Content-type: text/xml");
	header("Content-Disposition: attachment; filename=anketa.xml");
	echo $res;
}

	

?>