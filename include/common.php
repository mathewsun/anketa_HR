<?php
session_name('abs_job');

//Start the Session if session_id() returns nothing.
session_start();
header('Content-Type: text/html; charset=utf-8');

mysql_connect( 'mysql.mathewsun.myjino.ru', '039391001_absjob', 'absjob1' );
mysql_select_db( 'mathewsun_absjob' );

$sendAnketaTo = "mathewsun@rambler.ru";

$result1 = mysql_query( "SET NAMES UTF8" );

function DEFINE_date_create_from_format()
{

  function date_create_from_format( $dformat, $dvalue )
  {

    $schedule = $dvalue;
    //$schedule_format = str_replace(array('Y','m','d', 'H', 'i','a'),array('%Y','%m','%d', '%I', '%M', '%p' ) ,$dformat);
    $schedule_format = str_replace(array('y','Y','m','d', 'H', 'i','s'),array('%y','%Y','%m','%d', '%H', '%M', '%S' ) ,$dformat);
    // %Y, %m and %d correspond to date()'s Y m and d.
    // %I corresponds to H, %M to i and %p to a
    $ugly = strptime($schedule, $schedule_format);
    $ymd = sprintf(
        // This is a format string that takes six total decimal
        // arguments, then left-pads them with zeros to either
        // 4 or 2 characters, as needed
        '%04d-%02d-%02d %02d:%02d:%02d',
        $ugly['tm_year'] + 1900,  // This will be "111", so we need to add 1900.
        $ugly['tm_mon'] + 1,      // This will be the month minus one, so we add one.
        $ugly['tm_mday'], 
        $ugly['tm_hour'], 
        $ugly['tm_min'], 
        $ugly['tm_sec']
    );
    $new_schedule = new DateTime($ymd);

   return $new_schedule;

  }
}

if( !function_exists("date_create_from_format") )
  DEFINE_date_create_from_format();

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path.$filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/html; charset=utf-8\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";
    if (mail($mailto, $subject, "", $header)) {
        return true;
    } else {
        return false;
    }
}

function baseurl(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'];
}
  
function nl2br2($string) { 
$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string); 
$string = str_replace(array('"'), "'", $string); 
return $string; 
}
  
function getNumberOrZero($inputString){
		$inputString = str_replace(array(' ',','),array('','.') ,$inputString);
		if (is_numeric($inputString))
                return $inputString;
        else
                return 0;
}

function getNumberOrEmpty($inputString){
        if (is_numeric($inputString) && $inputString != 0)
                return $inputString;
        else
                return "";
}

function getBool($inputString){
        if ($inputString)
                return 1;
        else
                return 0;
}

function getSafeString($inputString){
        $inputString = str_replace(array('"'),array('&#34;') ,$inputString);
		return $inputString;
}

function getSafeJSString($inputString){
        $inputString = str_replace(array('"'),array('&#34;') ,$inputString);
        $inputString = str_replace(array("'"),array("\'") ,$inputString);
		return $inputString;
}

function getSafeDate($inputString){
	$inputString = str_replace(array('-'),array('.') ,$inputString);
	$dr = date_create_from_format('d.m.Y', $inputString);
	if (($timestamp = strtotime($inputString)) === false || $dr == null){
		$inputString = "";
	}
	else{
		$inputString = $dr->format('Y-m-d');
	}
	return $inputString;
}

function getSafeDateWithTime($inputString){
	$inputString = str_replace(array('-'),array('.') ,$inputString);
	$inputString = str_replace(array(' в '),array(' ') ,$inputString);
	$inputString = str_replace(array('в'),array(' ') ,$inputString);
	$dr = date_create_from_format('d.m.Y H:i', $inputString);
	if (($timestamp = strtotime($inputString)) === false || $dr == null){
		$inputString .= " 00:00:00";
		$dr = date_create_from_format('d.m.Y H:i:s', $inputString);
	}
	if (($timestamp = strtotime($inputString)) === false || $dr == null){
		$dr = date_create_from_format('d.m.Y H:i:s', $inputString);
	}

	if (($timestamp = strtotime($inputString)) === false || $dr == null){
		$inputString = "";
	}
	else{
		$inputString = $dr->format('Y-m-d H:i:s');
	}
	return $inputString;
}

function getPercent($num1, $num2){
        if ($num2 == 0)
                return 0;
        else
                return round(($num1*100)/$num2, 1);
}

function getDivide($num1, $num2){
        if ($num2 == 0)
                return 0;
        else
                return round($num1/$num2, 2);
}

function showStringFromDB($inputString){
        return getSafeString(str_replace(array('\"'),array('"') ,$inputString));
}

function showStringFromDBForExcel($inputString){
        return str_replace(array('\"'),array('"') ,$inputString);
}

//-------------------------------------------------------------------------------------------
//---------------GET ID BY VALUE FUNCTIONS -------------------------------------
//-------------------------------------------------------------------------------------------

function getUserByName($name){
        $id = 0;
        $query = mysql_query("SELECT id FROM user WHERE name = '".mysql_real_escape_string($name)."'");
        if(mysql_num_rows($query) > 0){
                $data = mysql_fetch_assoc($query);
                $id = $data['id'];
        }
        return $id;
}

function getRegionByName($name){
        $id = 0;
        $query = mysql_query("SELECT id FROM region WHERE name = '".mysql_real_escape_string($name)."'");
        if(mysql_num_rows($query) > 0){
                $data = mysql_fetch_assoc($query);
                $id = $data['id'];
        }
        return $id;
}

function getWorkCategoryByName($name){
        $id = 0;
        $query = mysql_query("SELECT id FROM work_category WHERE name = '".mysql_real_escape_string($name)."'");
        if(mysql_num_rows($query) > 0){
                $data = mysql_fetch_assoc($query);
                $id = $data['id'];
        }
        return $id;
}

function getSROByName($name){
        $id = 0;
        $query = mysql_query("SELECT id FROM sro WHERE name = '".mysql_real_escape_string($name)."'");
        if(mysql_num_rows($query) > 0){
                $data = mysql_fetch_assoc($query);
                $id = $data['id'];
        }
        return $id;
}

function getOrganizationByName($name){
        $id = 0;
        $query = mysql_query("SELECT id FROM organization WHERE name = '".mysql_real_escape_string($name)."'");
        if(mysql_num_rows($query) > 0){
                $data = mysql_fetch_assoc($query);
                $id = $data['id'];
        }
        return $id;
}
function getProcurementMethodByName($name){
        $id = 0;
        $query = mysql_query("SELECT id FROM procurement_method WHERE name = '".mysql_real_escape_string($name)."'");
        if(mysql_num_rows($query) > 0){
                $data = mysql_fetch_assoc($query);
                $id = $data['id'];
        }
        return $id;
}

//-------------------------------------------------------------------------------------------
//---------------INSERTS AND UPDATES ---------------------------------------------
//-------------------------------------------------------------------------------------------

function insertOrganization($id, $user_name, $name, $region_name, $city, $INN, $SRO){
        $user_id = getUserByName($user_name);
        if ($user_id == 0){
                return "Менеджер не существует";
        }
        $region_id = getRegionByName($region_name);
        if ($region_id == 0){
                return "Регион (".$region_name.") не существует";
        }
        mysql_query("INSERT INTO organization (id, user_id, name, region_id, city, INN) VALUES ( ".$id.", ".$user_id.", '".mysql_real_escape_string($name)."', ".$region_id.", '".mysql_real_escape_string($city)."', '".mysql_real_escape_string($INN)."')");
        $organization_id = mysql_insert_id();
        foreach ( $SRO as $lineData ) {
                $sro_id = getSROByName($lineData[0]);
                if ($sro_id == 0){
                        return "СРО (".$lineData[0].") не существует";
                }
                mysql_query("INSERT INTO organization_2_sro (organization_id, sro_id, payment_date) VALUES (".$organization_id.", ".$sro_id.", ".$lineData[1].")");
        }
}

function updateOrganization($id, $user_name, $name, $region_name, $city, $INN, $SRO){
        $user_id = getUserByName($user_name);
        if ($user_id == 0){
                return "Менеджер (".$user_name.") не существует";
        }
        $region_id = getRegionByName($region_name);
        if ($region_id == 0){
                return "Регион (".$region_name.") не существует";
        }
        mysql_query("UPDATE organization SET user_id = ".$user_id.", name = '".mysql_real_escape_string($name)."', region_id = ".$region_id.", city = '".mysql_real_escape_string($city)."', INN = '".mysql_real_escape_string($INN)."' WHERE id = ".$id);

        mysql_query("DELETE FROM organization_2_sro WHERE organization_id = ".$id);
        foreach ( $SRO as $lineData ) {
                $sro_id = getSROByName($lineData[0]);
                if ($sro_id == 0){
                        return "СРО (".$lineData[0].") не существует";
                }
                mysql_query("INSERT INTO organization_2_sro (organization_id, sro_id, payment_date) VALUES (".$id.", ".$sro_id.", ".$lineData[1].")");
        }
}

function insertContact($id, $organization_name, $position, $first_name, $last_name, $middle_name, $birthday, $sex, $email, $email_encoding, $auction_amount, $facility_type, $work_category, $region){
        $organization_id = getOrganizationByName($organization_name);
        if ($organization_id == 0){
                return "Организация (".$organization_name.") не существует";
        }
        $birthdayArr = explode("-", $birthday);
        if (count($birthdayArr) == 3){
                $birthday = "19".$birthdayArr[2]."-".$birthdayArr[0]."-".$birthdayArr[1];
        }

        mysql_query("INSERT INTO contact (`id`, `user_id`, `organization_id`, `position`, `first_name`, `last_name`, `middle_name`, `birthday`, `sex`, `email`, `email_encoding`, `auction_amount`, `facility_type`) VALUES ( ".$id.", NULL, ".$organization_id.", '".mysql_real_escape_string($position)."', '".mysql_real_escape_string($first_name)."', '".mysql_real_escape_string($last_name)."', '".mysql_real_escape_string($middle_name)."', '".$birthday."', '".mysql_real_escape_string($sex)."', '".mysql_real_escape_string($email)."', '".mysql_real_escape_string($email_encoding)."', ".$auction_amount.", '".mysql_real_escape_string($facility_type)."' )");
		
        $contact_id = mysql_insert_id();
        foreach ( $work_category as $lineData ) {
                $work_category_id = getWorkCategoryByName($lineData);
                if ($work_category_id == 0){
                        return "Классификатор (".$lineData.") не существует";
                }

                mysql_query("INSERT INTO contact_2_work_category (contact_id, work_category_id) VALUES (".$contact_id.", ".$work_category_id.")");
        }
        foreach ( $region as $lineData ) {
                $region_id = getRegionByName($lineData);
                if ($region_id == 0){
                        return "Регион (".$lineData.") не существует";
                }

                mysql_query("INSERT INTO contact_2_tender_region (contact_id, region_id) VALUES (".$contact_id.", ".$region_id.")");
        }

}

function updateContact($id, $organization_name, $position, $first_name, $last_name, $middle_name, $birthday, $sex, $email, $email_encoding, $auction_amount, $facility_type, $work_category, $region){
        $organization_id = getOrganizationByName($organization_name);
        if ($organization_id == 0){
                return "Организация (".$organization_name.") не существует";
        }
        $birthdayArr = explode("-", $birthday);
        if (count($birthdayArr) == 3){
                $birthday = "19".$birthdayArr[2]."-".$birthdayArr[0]."-".$birthdayArr[1];
        }

        mysql_query("UPDATE contact SET `user_id` = NULL, `organization_id` = ".$organization_id.", `position` = '".mysql_real_escape_string($position)."', `first_name` = '".mysql_real_escape_string($first_name)."', `last_name` = '".mysql_real_escape_string($last_name)."', `middle_name` = '".mysql_real_escape_string($middle_name)."', `birthday` = '".$birthday."', `sex` = '".mysql_real_escape_string($sex)."', `email` = '".mysql_real_escape_string($email)."', `email_encoding` = '".mysql_real_escape_string($email_encoding)."', `auction_amount` = ".$auction_amount.", `facility_type` = '".mysql_real_escape_string($facility_type)."' WHERE id = ".$id);

        mysql_query("DELETE FROM contact_2_work_category WHERE contact_id = ".$id);
        mysql_query("DELETE FROM contact_2_tender_region WHERE contact_id = ".$id);
        foreach ( $work_category as $lineData ) {
                $work_category_id = getWorkCategoryByName($lineData);
                if ($work_category_id == 0){
                        return "Классификатор (".$lineData.") не существует";
                }

                mysql_query("INSERT INTO contact_2_work_category (contact_id, work_category_id) VALUES (".$id.", ".$work_category_id.")");
        }
        foreach ( $region as $lineData ) {
                $region_id = getRegionByName($lineData);
                if ($region_id == 0){
                        return "Регион (".$lineData.") не существует";
                }

                mysql_query("INSERT INTO contact_2_tender_region (contact_id, region_id) VALUES (".$id.", ".$region_id.")");
        }

}

function insertTender($id, $user_name, $entry_date, $name, $customer, $description, $work_place, $procurement_method_name, $start_price, $region_name, $source_URL, $special_requirements, $bid_assurance, $contract_assurance, $publication_date, $bid_start_date, $bid_end_date, $bid_review_end_date, $auction_start_date, $work_category){

        $dr = date_create_from_format('d.m.Y H:i:s', $entry_date);
        if ($dr != null){
                $entry_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $publication_date);
        if ($dr != null){
                $publication_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $bid_start_date);
        if ($dr != null){
                $bid_start_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $bid_end_date);
        if ($dr != null){
                $bid_end_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $bid_review_end_date);
        if ($dr != null){
                $bid_review_end_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $auction_start_date);
        if ($dr != null){
                $auction_start_date = $dr->format('Y-m-d H:i:s');
        }


        $user_id = getUserByName($user_name);
        if ($user_id == 0){
                return "Менеджер (".$user_name.") не существует";
        }
        $region_id = getRegionByName($region_name);
        if ($region_id == 0){
                return "Регион (".$region_name.") не существует";
        }
        $procurement_method_id = getProcurementMethodByName($procurement_method_name);
        if ($procurement_method_id == 0){
                return "Способ закупки (".$procurement_method_name.") не существует";
        }

        mysql_query("INSERT INTO tender (id, user_id, entry_date, name, customer, description, work_place, procurement_method_id, start_price, region_id, source_URL, special_requirements, bid_assurance, contract_assurance, publication_date, bid_start_date, bid_end_date, bid_review_end_date, auction_start_date) VALUES (".$id.", ".$user_id.", '".$entry_date."', '".mysql_real_escape_string($name)."', '".mysql_real_escape_string($customer)."', '".mysql_real_escape_string($description)."', '".mysql_real_escape_string($work_place)."', ".$procurement_method_id.", ".$start_price.", ".$region_id.", '".mysql_real_escape_string($source_URL)."', '".mysql_real_escape_string($special_requirements)."', ".$bid_assurance.", ".$contract_assurance.", '".$publication_date."', '".$bid_start_date."', '".$bid_end_date."', '".$bid_review_end_date."', '".$auction_start_date."')");
        $tender_id = mysql_insert_id();

        foreach ( $work_category as $lineData ) {
                $work_category_id = getWorkCategoryByName($lineData);
                if ($work_category_id == 0){
                        return "Классификатор (".$lineData.") не существует";
                }

                mysql_query("INSERT INTO tender_2_work_category (tender_id, work_category_id) VALUES (".$tender_id.", ".$work_category_id.")");
        }

}

function updateTender($id, $user_name, $entry_date, $name, $customer, $description, $work_place, $procurement_method_name, $start_price, $region_name, $source_URL, $special_requirements, $bid_assurance, $contract_assurance, $publication_date, $bid_start_date, $bid_end_date, $bid_review_end_date, $auction_start_date, $work_category){

        $dr = date_create_from_format('d.m.Y H:i:s', $entry_date);
        if ($dr != null){
                $entry_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $publication_date);
        if ($dr != null){
                $publication_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $bid_start_date);
        if ($dr != null){
                $bid_start_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $bid_end_date);
        if ($dr != null){
                $bid_end_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $bid_review_end_date);
        if ($dr != null){
                $bid_review_end_date = $dr->format('Y-m-d H:i:s');
        }
        $dr = date_create_from_format('d/m/y H:i', $auction_start_date);
        if ($dr != null){
                $auction_start_date = $dr->format('Y-m-d H:i:s');
        }

        $user_id = getUserByName($user_name);
        if ($user_id == 0){
                return "Менеджер (".$user_name.") не существует";
        }
        $region_id = getRegionByName($region_name);
        if ($region_id == 0){
                return "Регион (".$region_name.") не существует";
        }
        $procurement_method_id = getProcurementMethodByName($procurement_method_name);
        if ($procurement_method_id == 0){
                return "Способ закупки (".$procurement_method_name.") не существует";
        }

        mysql_query("UPDATE tender SET user_id = ".$user_id.", entry_date = '".$entry_date."', name = '".mysql_real_escape_string($name)."', customer = '".mysql_real_escape_string($customer)."', description = '".mysql_real_escape_string($description)."', work_place = '".mysql_real_escape_string($work_place)."', procurement_method_id = ".$procurement_method_id.", start_price = ".$start_price.", region_id = ".$region_id.", source_URL = '".mysql_real_escape_string($source_URL)."', special_requirements = '".mysql_real_escape_string($special_requirements)."', bid_assurance = ".$bid_assurance.", contract_assurance = ".$contract_assurance.", publication_date = '".$publication_date."', bid_start_date = '".$bid_start_date."', bid_end_date = '".$bid_end_date."', bid_review_end_date = '".$bid_review_end_date."', auction_start_date = '".$auction_start_date."' WHERE id = ".$id);

        mysql_query("DELETE FROM tender_2_work_category WHERE tender_id = ".$id);
        foreach ( $work_category as $lineData ) {
                $work_category_id = getWorkCategoryByName($lineData);
                if ($work_category_id == 0){
                        return "Классификатор (".$lineData.") не существует";
                }

                mysql_query("INSERT INTO tender_2_work_category (tender_id, work_category_id) VALUES (".$id.", ".$work_category_id.")");
        }

}

function insertSRONews($id, $sro_name, $entry_date, $title, $body, $source_url){
        $sro_id = getSROByName($sro_name);
        if ($sro_id == 0){
                return "СРО (".$sro_name.") не существует";
        }

        $dr = date_create_from_format('m-d-y', $entry_date);
        if ($dr != null){
                $entry_date = $dr->format('Y-m-d');
        }

        mysql_query("INSERT INTO sro_news (id, user_id, sro_id, entry_date, title, body, source_URL) VALUES ( ".$id.", NULL, ".$sro_id.", '".$entry_date."', '".mysql_real_escape_string($title)."', '".mysql_real_escape_string($body)."', '".mysql_real_escape_string($source_url)."')");
        $SRO_news_id = mysql_insert_id();
}

function updateSRONews($id, $sro_name, $entry_date, $title, $body, $source_url){
        $sro_id = getSROByName($sro_name);
        if ($sro_id == 0){
                return "СРО (".$sro_name.") не существует";
        }
        $dr = date_create_from_format('m-d-y', $entry_date);
        if ($dr != null){
                $entry_date = $dr->format('Y-m-d');
        }

        mysql_query("UPDATE sro_news SET user_id = NULL, sro_id = ".$sro_id.", entry_date = '".$entry_date."', title = '".mysql_real_escape_string($title)."', body = '".mysql_real_escape_string($body)."', source_URL = '".mysql_real_escape_string($source_url)."' WHERE id = ".$id);

}

//---------------------------------------------------------------------------------------
//------------------------     SEND NOTIFICATIONS   ---------------------------
//---------------------------------------------------------------------------------------

function getRequestText($email, $email_subject, $email_body, $session){
        $request = '{
        "session":"'.$session.'",
        "action" : "issue.send",
    "gid" : "personal",
    "lang": "ru",
    "from.name": "Допуск в SRO",
    "from.email": "msherko@dopuskvsro.ru",
    "subject": "'.$email_subject.'",
    "message":{
                                "html" : "'.$email_body.'"
               },
    "mute": "1",
    "sendwhen": "now",
    "relink" : "0",
    "relink.param" : {
                      "link" : 0,
                      "image": 1,
                      "test": 0
                     },
    "link.qsid": "qwe" ,
    "email" : "'.$email.'"
        }';

        return $request;

}

function sendRequest($request){
/*
        $response="";

        if ($fp = fsockopen ("ssl://pro.subscribe.ru", 443, $errno, $errstr, 30))
        {
            $request ="POST /api HTTP/1.0\r\n";
            $request.="Host: pro.subscribe.ru\r\n";
            $request.="Content-Type: application/x-www-form-urlencoded\r\n";
            $request.="Content-Length: 7\r\n";
            $request.="\r\n\r\n";
            $request.="foo=bar";

            fwrite($fp,$request,strlen($request));

            while (!feof($fp))
                $response.=fread($fp,8192);

            fclose($fp);
        }
        else
            die('Could not open socket');

        echo "<pre>\n";
        echo htmlentities($response);
        echo "</pre>\n";
*/

        $postvars = array(
          "apiversion" => "100",
          "json" => "1",
          "request.id" => "112254874",
          "request" => $request
        );
        $postdata = "";
        foreach ( $postvars as $key => $value )
            $postdata .= "&".rawurlencode($key)."=".rawurlencode($value);
        $postdata = substr( $postdata, 1 );

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, "https://pro.subscribe.ru/api");
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec ($ch);
        curl_close($ch);

        return $result;
}

function getSubscribeSession(){
        $session = "";
        $request = '{"action":"login","login":"dopuskvsro","sublogin":"dopuskvsro","passwd":"shi5Uto"}';
        // Create map with request parameters
        $result = sendRequest($request);
        $resultArr = json_decode($result, TRUE);
        if (array_key_exists('session', $resultArr)) {
                $session = $resultArr["session"];
        }
        return $session;
}

function sendNotification($email, $email_subject, $email_body){
        if (!isset($_SESSION["SubscribeSession"]) || $_SESSION["SubscribeSession"] == ""){
                $_SESSION["SubscribeSession"] = getSubscribeSession();
                if ($_SESSION["SubscribeSession"] == ""){
                        return "Subscribe session not initialized";
                }
        }

        $email_body = str_replace(array("\r\n", "\r", "\n", "\t"), " ", $email_body); 
		$request = getRequestText($email, $email_subject, $email_body, $_SESSION["SubscribeSession"]);
        // Create map with request parameters
        $result = sendRequest($request);
        $resultArr = json_decode($result, TRUE);
        if (array_key_exists('errors', $resultArr)){  // && $resultArr['errors'] == "error/auth/failed") {
                $_SESSION["SubscribeSession"] = getSubscribeSession();
                if ($_SESSION["SubscribeSession"] == ""){
                        return "Subscribe session not initialized";
                }
                $request = getRequestText($email, $email_subject, $email_body, $_SESSION["SubscribeSession"]);
                $result = sendRequest($request);
        }

        $resultArr = json_decode($result, TRUE);
        if (array_key_exists('error', $resultArr)){  // && $resultArr['errors'] == "error/auth/failed") {
                $explain = "";
				if (array_key_exists('error', $resultArr)){
					$explain = $resultArr['explain'];
				}
				
				return "Не отправлено!! - " . $explain;
        }

        return "";
        //echo $result;

}

function setTenderNotificationSent($client_id, $sent_tenders){
        mysql_query("INSERT INTO sent_tenders (contact_id, tender_id) SELECT ".$client_id.", id FROM tender WHERE id IN (".$sent_tenders.")");
}
function setNewsNotificationSent($client_id, $sent_news){
        mysql_query("INSERT INTO sent_sro_news (contact_id, sro_news_id) SELECT ".$client_id.", id FROM sro_news WHERE id IN (".$sent_news.")");
}

function getStringForXML($inputString){
	return htmlspecialchars($inputString);
	/*
	$inputString = str_replace( '"', '&quot;', $inputString);
	$inputString = str_replace( '<', '&lt;', $inputString);
	$inputString = str_replace( '>', '&gt;', $inputString);
	return $inputString;
	*/
}

function getDateForXML($inputString){
	if (strpos($inputString, '0000') !== false) {
		return "";
	}
	$dr = date_create_from_format('Y-m-d H:i:s', $inputString);
	if ($dr == null){
		$inputString .= " 00:00:00";
		$dr = date_create_from_format('Y-m-d H:i:s', $inputString);
	}
	if ($dr == null){
		$inputString = "";
	}
	else{
		$inputString = $dr->format('Y-m-d')."T".$dr->format('H:i:s');
	}

	return $inputString;
}

function getDateShortForXML($inputString){
	if (strpos($inputString, '0000') !== false) {
		return "";
	}
	$dr = date_create_from_format('Y-m-d', $inputString);
	if (($timestamp = strtotime($inputString)) === false || $dr == null){
		$inputString = "";
	}
	else{
		$inputString = $dr->format('Y-m')."-01T00:00:00";
	}
	return $inputString;
}

function generateXML($anketa_id){
	$query = mysql_query("
				SELECT a.*,
					u.login as email,
					ms.code as marital_status,
					mr.code as military_rank,

					dt1.code as passport_document_type, 
					d1.series as passport_series, 
					d1.number as passport_number, 
					d1.issued_date as passport_issued_date, 
					d1.issued_by as passport_issued_by, 
					d1.division_code as passport_division_code,

					dt2.code as passport2_document_type, 
					d2.series as passport2_series, 
					d2.number as passport2_number, 
					d2.issued_date as passport2_issued_date, 
					d2.issued_by as passport2_issued_by, 
					d2.division_code as passport2_division_code,

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
					adr2.phone_add AS address2_phone_add
					
				FROM anketa a 
				INNER JOIN user u on u.id = a.user_id
				LEFT JOIN marital_status ms ON ms.id = a.marital_status_id
				LEFT JOIN document d1 ON d1.id = a.internal_document_id
				LEFT JOIN document_type dt1 ON dt1.id = d1.document_type_id
				LEFT JOIN document d2 ON d2.id = a.foreign_document_id
				LEFT JOIN document_type dt2 ON dt2.id = d2.document_type_id
				LEFT JOIN military_rank mr ON mr.id = a.military_rank_id
				LEFT JOIN address adr ON adr.id = a.registration_address_id
				LEFT JOIN address adr2 ON adr2.id = a.residence_address_id
				WHERE a.id=".$anketa_id." LIMIT 1");
	if(mysql_num_rows($query) == 0){
		return "";
	}
	$data = mysql_fetch_assoc($query);
	
	$file = file_get_contents('./xml/main.xml', false);

	$file = str_replace( '#entry_date#', getDateForXML($data['entrydate']) ,$file);
	$file = str_replace( '#last_name#', getStringForXML($data['last_name']) ,$file);
	$file = str_replace( '#first_name#', getStringForXML($data['first_name']) ,$file);
	$file = str_replace( '#middle_name#', getStringForXML($data['middle_name']) ,$file);
	$file = str_replace( '#birthday#', getDateForXML($data['birthday']) ,$file);
	if ($data['gender'] == 'M'){
		$file = str_replace( '#gender#', getStringForXML('{"#",ad87ca42-048d-4c72-9aea-ee138d9c0e9d,244:863a7c890bc7db6849bb5c5d636552aa}') ,$file);
	}
	else{
		$file = str_replace( '#gender#', getStringForXML('{"#",ad87ca42-048d-4c72-9aea-ee138d9c0e9d,244:a708eae2479f594e4595c317d33f5bb8}') ,$file);
	}
	if ($data['name_not_changed'] == '1'){
		$file = str_replace( '#name_not_changed#', '' ,$file);
	}
	else{
		$file = str_replace( '#name_not_changed#', 'false' ,$file);
	}
	$file = str_replace( '#old_last_name#', getStringForXML($data['old_last_name']) ,$file);
	$file = str_replace( '#old_first_name#', getStringForXML($data['old_first_name']) ,$file);
	$file = str_replace( '#old_middle_name#', getStringForXML($data['old_middle_name']) ,$file);
	$file = str_replace( '#name_changed_place#', getStringForXML($data['name_changed_place']) ,$file);
	$file = str_replace( '#name_changed_date#', getDateForXML($data['name_changed_date']) ,$file);
	$file = str_replace( '#name_changed_reason#', getStringForXML($data['name_changed_reason']) ,$file);
	$file = str_replace( '#birth_place#', getStringForXML($data['birth_place']) ,$file);
	if ($data['citizenship_country'] == ''){
		$file = str_replace( '#citizenship_country#', '3fe5d05c-4a18-11db-90b5-0012798f03ac' ,$file);
	}
	else{
		$file = str_replace( '#citizenship_country#', '3fe5d05d-4a18-11db-90b5-0012798f03ac' ,$file);
	}
	$file = str_replace( '#citizenship_country_other#', getStringForXML($data['citizenship_country']) ,$file);
	$file = str_replace( '#citizenship_changed_date#', getDateForXML($data['citizenship_changed_date']) ,$file);
	$file = str_replace( '#citizenship_changed_reason#', getStringForXML($data['citizenship_changed_reason']) ,$file);
	if ($data['degree'] == ''){
		$file = str_replace( '#degree_not_have#', '' ,$file);
	}
	else{
		$file = str_replace( '#degree_not_have#', 'false' ,$file);
	}
	$file = str_replace( '#degree#', getStringForXML($data['degree']) ,$file);
	$file = str_replace( '#additional_skills#', getStringForXML($data['additional_skills']) ,$file);
	$file = str_replace( '#driver_license#', getStringForXML($data['driver_license_category'].'¤'.$data['driver_license_num']) ,$file);
	$file = str_replace( '#marital_status#', getStringForXML($data['marital_status']) ,$file);
	$file = str_replace( '#courts#', getStringForXML($data['courts']) ,$file);
	$file = str_replace( '#police_relatives#', getStringForXML($data['police_relatives']) ,$file);
	$file = str_replace( '#passport_document_type#', getStringForXML($data['passport_document_type']) ,$file);
	$file = str_replace( '#passport_series#', getStringForXML($data['passport_series']) ,$file);
	$file = str_replace( '#passport_number#', getStringForXML($data['passport_number']) ,$file);
	$file = str_replace( '#passport_issued_date#', getDateForXML($data['passport_issued_date']) ,$file);
	$file = str_replace( '#passport_issued_by#', getStringForXML($data['passport_issued_by']) ,$file);
	$file = str_replace( '#passport_division_code#', getStringForXML($data['passport_division_code']) ,$file);
	$file = str_replace( '#registration_address#', getStringForXML($data['address_postcode'].'¤'.$data['address_region'].'¤'.$data['address_district'].'¤'.$data['address_city'].'¤'.$data['address_location'].'¤'.$data['address_street'].'¤'.$data['address_house'].'¤'.$data['address_block'].'¤'.$data['address_appt']) ,$file);
	$file = str_replace( '#registration_address_phone#', getStringForXML($data['address_phone_country'].'¤'.$data['address_phone_city'].'¤'.$data['address_phone_number'].'¤'.$data['address_phone_add']) ,$file);
	$file = str_replace( '#residence_address#', getStringForXML($data['address2_postcode'].'¤'.$data['address2_region'].'¤'.$data['address2_district'].'¤'.$data['address2_city'].'¤'.$data['address2_location'].'¤'.$data['address2_street'].'¤'.$data['address2_house'].'¤'.$data['address2_block'].'¤'.$data['address2_appt']) ,$file);
	$file = str_replace( '#residence_addres_phones#', getStringForXML($data['address2_phone_country'].'¤'.$data['address2_phone_city'].'¤'.$data['address2_phone_number'].'¤'.$data['address2_phone_add']) ,$file);
	$file = str_replace( '#email#', getStringForXML($data['email']) ,$file);
	$file = str_replace( '#passport2_document_type#', getStringForXML($data['passport2_document_type']) ,$file);
	$file = str_replace( '#passport2_series#', getStringForXML($data['passport2_series']) ,$file);
	$file = str_replace( '#passport2_number#', getStringForXML($data['passport2_number']) ,$file);
	$file = str_replace( '#passport2_issued_date#', getDateForXML($data['passport2_issued_date']) ,$file);
	$file = str_replace( '#passport2_issued_by#', getStringForXML($data['passport2_issued_by']) ,$file);
	if ($data['military_service_obligation'] == '1'){
		$file = str_replace( '#military_service_obligation#', getStringForXML('{"#",5fc59268-2517-42b5-9d81-9bdc269a2b39,241:8013536fa99864b74612d2cc970a04bb}') ,$file);
	}
	else{
		$file = str_replace( '#military_service_obligation#', getStringForXML('{"#",5fc59268-2517-42b5-9d81-9bdc269a2b39,241:82ade9e79dd20ae04c47376b24b70d8d}') ,$file);
	}
	$file = str_replace( '#military_rank#', getStringForXML($data['military_rank']) ,$file);
	$file = str_replace( '#work_restrictions#', getStringForXML($data['work_restrictions']) ,$file);
	$file = str_replace( '#additionals#', getStringForXML($data['additionals']) ,$file);
	$file = str_replace( '#INN#', getStringForXML($data['INN']) ,$file);
	$file = str_replace( '#insurance_number#', getStringForXML($data['insurance_number']) ,$file);

	//  #SECTION_education#
	$query = mysql_query( "
					SELECT e.*, et.code AS education_type, tt.code AS training_type
					FROM education e
					INNER JOIN education_type et ON et.id = e.education_type_id
					INNER JOIN training_type tt ON tt.id = e.training_type_id
					WHERE e.anketa_id = ".$anketa_id."
					ORDER BY e.id 
				");
	$SECTION_education = "";
	$counter = 1;
	$file_list_origin = file_get_contents('./xml/education.xml', false);
	while ($row = mysql_fetch_assoc($query)) {
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($counter) ,$file_list);
		$file_list = str_replace( '#education_type#', getStringForXML($row['education_type']) ,$file_list);
		$file_list = str_replace( '#institution_name#', getStringForXML($row['institution_name']) ,$file_list);
		$file_list = str_replace( '#specialty#', getStringForXML($row['specialty']) ,$file_list);
		$file_list = str_replace( '#certificate_num#', getStringForXML($row['certificate_num']) ,$file_list);
		$file_list = str_replace( '#finish_year#', getStringForXML($row['finish_year']) ,$file_list);
		$file_list = str_replace( '#qualification#', getStringForXML($row['qualification']) ,$file_list);
		$file_list = str_replace( '#training_type#', getStringForXML($row['training_type']) ,$file_list);
		$SECTION_education = $SECTION_education.$file_list;
		$counter++;
	}
	mysql_free_result($query);
	for( $i = $counter; $i <= 4; $i++){
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($i) ,$file_list);
		$file_list = str_replace( '#education_type#', '' ,$file_list);
		$file_list = str_replace( '#institution_name#', '' ,$file_list);
		$file_list = str_replace( '#specialty#', '' ,$file_list);
		$file_list = str_replace( '#certificate_num#', '' ,$file_list);
		$file_list = str_replace( '#finish_year#', '' ,$file_list);
		$file_list = str_replace( '#qualification#', '' ,$file_list);
		$file_list = str_replace( '#training_type#', '' ,$file_list);
		$SECTION_education = $SECTION_education.$file_list;
	}
	$file = str_replace( '#SECTION_education#', $SECTION_education ,$file);
	//  #SECTION_additional_education#
	$query = mysql_query( "
					SELECT e.*
					FROM additional_education e
					WHERE e.anketa_id = ".$anketa_id."
					ORDER BY e.id 
				");
	$SECTION_additional_education = "";
	$counter = 1;
	$file_list_origin = file_get_contents('./xml/aditional_education.xml', false);
	while ($row = mysql_fetch_assoc($query)) {
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($counter) ,$file_list);
		$file_list = str_replace( '#place#', getStringForXML($row['place']) ,$file_list);
		$file_list = str_replace( '#date#', getStringForXML($row['date']) ,$file_list);
		$file_list = str_replace( '#duration#', getStringForXML($row['duration']) ,$file_list);
		$SECTION_additional_education = $SECTION_additional_education.$file_list;
		$counter++;
	}
	mysql_free_result($query);
	for( $i = $counter; $i <= 4; $i++){
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($i) ,$file_list);
		$file_list = str_replace( '#place#', '' ,$file_list);
		$file_list = str_replace( '#date#', '' ,$file_list);
		$file_list = str_replace( '#duration#', '' ,$file_list);
		$SECTION_additional_education = $SECTION_additional_education.$file_list;
	}
	$file = str_replace( '#SECTION_additional_education#', $SECTION_additional_education ,$file);
	//  #SECTION_language_skills#
	$query = mysql_query( "
					SELECT ls.*, l.code AS language, sl.code AS skill_level
					FROM language_skills ls
					INNER JOIN language l ON l.id = ls.language_id
					INNER JOIN skill_level sl ON sl.id = ls.skill_level_id
					WHERE ls.anketa_id = ".$anketa_id."
					ORDER BY ls.id 
				");
	$SECTION_language_skills = "";
	$counter = 1;
	$file_list_origin = file_get_contents('./xml/language_skills.xml', false);
	while ($row = mysql_fetch_assoc($query)) {
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($counter) ,$file_list);
		$file_list = str_replace( '#language#', getStringForXML($row['language']) ,$file_list);
		$file_list = str_replace( '#skill_level#', getStringForXML($row['skill_level']) ,$file_list);
		$SECTION_language_skills = $SECTION_language_skills.$file_list;
		$counter++;
	}
	mysql_free_result($query);
	for( $i = $counter; $i <= 4; $i++){
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($i) ,$file_list);
		$file_list = str_replace( '#language#', '' ,$file_list);
		$file_list = str_replace( '#skill_level#', '' ,$file_list);
		$SECTION_language_skills = $SECTION_language_skills.$file_list;
	}
	$file = str_replace( '#SECTION_language_skills#', $SECTION_language_skills ,$file);
	//  #SECTION_work_activity#
	$query = mysql_query( "
					SELECT wa.*, 
						a.postcode, a.region, a.district, a.city, a.location, a.street, a.house, a.block, a.appt, a.phone_country, a.phone_city, a.phone_number, a.phone_add,
						CONCAT_WS( ', ', a.postcode, a.region, a.city, a.street, a.house) as company_place_full
					FROM work_activity wa
					LEFT JOIN address a ON a.id = wa.company_place_id
					WHERE wa.anketa_id = ".$anketa_id."
					ORDER BY wa.id 
				");
	$SECTION_work_activity = "";
	$counter = 1;
	$file_list_origin = file_get_contents('./xml/work_activity.xml', false);
	while ($row = mysql_fetch_assoc($query)) {
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($counter) ,$file_list);
		$file_list = str_replace( '#date_start#', getDateShortForXML($row['date_start']) ,$file_list);
		$file_list = str_replace( '#date_end#', getDateShortForXML($row['date_end']) ,$file_list);
		$file_list = str_replace( '#company_name#', getStringForXML($row['company_name']) ,$file_list);
		$file_list = str_replace( '#position#', getStringForXML($row['position']) ,$file_list);
		$file_list = str_replace( '#recommendations#', getStringForXML($row['recommendations']) ,$file_list);
		$file_list = str_replace( '#dismiss_reason#', getStringForXML($row['dismiss_reason']) ,$file_list);
		$file_list = str_replace( '#company_place#', getStringForXML($row['postcode'].'¤'.$row['region'].'¤'.$row['district'].'¤'.$row['city'].'¤'.$row['location'].'¤'.$row['street'].'¤'.$row['house'].'¤'.$row['block'].'¤'.$row['appt']) ,$file_list);
		$SECTION_work_activity = $SECTION_work_activity.$file_list;
		$counter++;
	}
	mysql_free_result($query);
	for( $i = $counter; $i <= 20; $i++){
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($i) ,$file_list);
		$file_list = str_replace( '#date_start#', '' ,$file_list);
		$file_list = str_replace( '#date_end#', '' ,$file_list);
		$file_list = str_replace( '#company_name#', '' ,$file_list);
		$file_list = str_replace( '#position#', '' ,$file_list);
		$file_list = str_replace( '#recommendations#', '' ,$file_list);
		$file_list = str_replace( '#dismiss_reason#', '' ,$file_list);
		$file_list = str_replace( '#company_place#', '' ,$file_list);
		$SECTION_work_activity = $SECTION_work_activity.$file_list;
	}
	$file = str_replace( '#SECTION_work_activity#', $SECTION_work_activity ,$file);
	// #SECTION_relatives#
	$query = mysql_query( "
					SELECT r.*, rl.code as relation,
						a.postcode, a.region, a.district, a.city, a.location, a.street, a.house, a.block, a.appt, a.phone_country, a.phone_city, a.phone_number, a.phone_add,
						CONCAT_WS( ', ', a.postcode, a.region, a.city, a.street, a.house) as address_full
					FROM relatives r
					INNER JOIN relation rl ON rl.id = r.relation_id
					LEFT JOIN address a ON a.id = r.address_id
					WHERE r.anketa_id = ".$anketa_id."
					ORDER BY r.id 
				");
	$SECTION_relatives = "";
	$counter = 1;
	$file_list_origin = file_get_contents('./xml/relatives.xml', false);
	while ($row = mysql_fetch_assoc($query)) {
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($counter) ,$file_list);
		$file_list = str_replace( '#relation#', getStringForXML($row['relation']) ,$file_list);
		$file_list = str_replace( '#last_name#', getStringForXML($row['last_name']) ,$file_list);
		$file_list = str_replace( '#first_name#', getStringForXML($row['first_name']) ,$file_list);
		$file_list = str_replace( '#middle_name#', getStringForXML($row['middle_name']) ,$file_list);
		$file_list = str_replace( '#birthday#', getDateForXML($row['birthday']) ,$file_list);
		$file_list = str_replace( '#old_last_name#', getStringForXML($row['old_last_name']) ,$file_list);
		$file_list = str_replace( '#old_first_name#', getStringForXML($row['old_first_name']) ,$file_list);
		$file_list = str_replace( '#old_middle_name#', getStringForXML($row['old_middle_name']) ,$file_list);
		$file_list = str_replace( '#birth_place#', getStringForXML($row['birth_place']) ,$file_list);
		$file_list = str_replace( '#company#', getStringForXML($row['company']) ,$file_list);
		$file_list = str_replace( '#position#', getStringForXML($row['position']) ,$file_list);
		$file_list = str_replace( '#address#', getStringForXML($row['postcode'].'¤'.$row['region'].'¤'.$row['district'].'¤'.$row['city'].'¤'.$row['location'].'¤'.$row['street'].'¤'.$row['house'].'¤'.$row['block'].'¤'.$row['appt']) ,$file_list);
		$file_list = str_replace( '#company_and_position#', getStringForXML($row['company'].'¤'.$row['position']) ,$file_list);
		$SECTION_relatives = $SECTION_relatives.$file_list;
		$counter++;
	}
	mysql_free_result($query);
	for( $i = $counter; $i <= 10; $i++){
		$file_list = $file_list_origin;
		$file_list = str_replace( '#counter#', getStringForXML($i) ,$file_list);
		$file_list = str_replace( '#relation#', '' ,$file_list);
		$file_list = str_replace( '#last_name#', '' ,$file_list);
		$file_list = str_replace( '#first_name#', '' ,$file_list);
		$file_list = str_replace( '#middle_name#', '' ,$file_list);
		$file_list = str_replace( '#birthday#', '' ,$file_list);
		$file_list = str_replace( '#old_last_name#', '' ,$file_list);
		$file_list = str_replace( '#old_first_name#', '' ,$file_list);
		$file_list = str_replace( '#old_middle_name#', '' ,$file_list);
		$file_list = str_replace( '#birth_place#', '' ,$file_list);
		$file_list = str_replace( '#company#', '' ,$file_list);
		$file_list = str_replace( '#position#', '' ,$file_list);
		$file_list = str_replace( '#address#', '' ,$file_list);
		$file_list = str_replace( '#company_and_position#', '' ,$file_list);
		$SECTION_relatives = $SECTION_relatives.$file_list;
	}
	$file = str_replace( '#SECTION_relatives#', $SECTION_relatives ,$file);

	return $file;

}
?>
