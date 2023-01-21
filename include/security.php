<? include_once("common.php");
$anketa_id = 0;
if(!isset($is_agree)){
	$is_agree = false;
}
if(!isset($admin_section)){
	$admin_section = false;
}

if (isset($_SESSION['id']) and isset($_SESSION['hash']))
{
    //$query = mysql_query("SELECT id, hash, email, name ,INET_NTOA(IP) AS IP FROM user WHERE id = '".intval($_SESSION['id'])."' LIMIT 1");
    $query = mysql_query("SELECT id, hash, agreed, is_admin FROM user WHERE id = '".intval($_SESSION['id'])."' LIMIT 1");
    $userdata = mysql_fetch_assoc($query);

	if(($userdata['hash'] !== $_SESSION['hash']) or ($userdata['id'] !== $_SESSION['id']) or ($admin_section && $userdata['is_admin'] == '0'))
    {
		$_SESSION['id']="";
        $_SESSION['hash']="";
        $_SESSION['is_admin']="";
//отправляем логиниться/отображаем ссылку "вход" инклуд логинпхп
		//header("Location: login.php?autoout=1&err=1");
		//exit();
	    echo '<script language="JavaScript">
		window.location.href = "login.php";
	    </script>';
		exit;
    }
    else
    {
		if( $userdata['agreed'] == '0' && !$is_agree){
			echo '<script language="JavaScript">
			window.location.href = "agree.php";
			</script>';
			exit;
		}

		$query = mysql_query("SELECT id FROM anketa WHERE user_id='".$_SESSION['id']."' LIMIT 1");
		if(mysql_num_rows($query) > 0){
			$data = mysql_fetch_assoc($query);
			$anketa_id = $data['id'];
		}
		if ($_SESSION['is_admin'] == "1")
		{
			$anketa_id = $_GET['aid'];
		}
	}
}
else
{
//отправляем логиниться/отображаем ссылку "вход" инклуд логинпхп
	    echo '<script language="JavaScript">
		window.location.href = "login.php?autoout=1";
	    </script>';
    //header("Location: login.php?autoout=1");
	exit();
}

?>
