<?php
function whmch_sms_license_install_db(&$licenta) {
   global $wpdb;
	$sql = "CREATE TABLE IF NOT EXISTS vantage_licence(ID int, licensekey VARCHAR(500), localkey VARCHAR(500))";
	$wpdb->query($sql);
		$empty = $wpdb->get_col($wpdb->prepare("SELECT * FROM vantage_licence LIMIT 1" ) );
if ( empty($empty) ){
	$license_key = $licenta;
$sql2 = "INSERT INTO vantage_licence (ID, licensekey, localkey) VALUES ('1', '" .$license_key. "', 'None')";
$wpdb->query($sql2);
}
	$license_key = $licenta;
    		$sql3 = "UPDATE vantage_licence SET `licensekey`='$license_key' WHERE ID = 1";
$wpdb->query($sql3);
}
function whmcs_sms_licence_get_localkey() {
	global $wpdb;
	$sql = "SELECT localkey FROM vantage_licence WHERE ID = 1 ";
	$active_rows = $wpdb->get_results($sql);

	foreach ($active_rows as $active_row){
		return $active_row->localkey;
	}
}
function whmcs_sms_licence_get_licencekey(){
	global $wpdb;
	$sql = "SELECT licensekey FROM vantage_licence WHERE ID = 1";	
    $active_rows = $wpdb->get_results($sql);

	foreach ($active_rows as $active_row){
		return $active_row->licensekey;
	}
}

function check_license($licensekey,$localkey="") {
    $whmcsurl = "http://grinmedia.ro/clienti/";
    $licensing_secret_key = "grinMedia"; # Unique value, should match what is set in the product configuration for MD5 Hash Verification
    $check_token = time().md5(mt_rand(1000000000,9999999999).$licensekey);
    $checkdate = date("Ymd"); # Current date
    $usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
    $localkeydays = 1; # How long the local key is valid for in between remote checks
    $allowcheckfaildays = 5; # How many days to allow after local key expiry before blocking access if connection cannot be made
    $localkeyvalid = false;
    if ($localkey) {
        $localkey = str_replace("\n",'',$localkey); # Remove the line breaks
		$localdata = substr($localkey,0,strlen($localkey)-32); # Extract License Data
		$md5hash = substr($localkey,strlen($localkey)-32); # Extract MD5 Hash
        if ($md5hash==md5($localdata.$licensing_secret_key)) {
            $localdata = strrev($localdata); # Reverse the string
    		$md5hash = substr($localdata,0,32); # Extract MD5 Hash
    		$localdata = substr($localdata,32); # Extract License Data
    		$localdata = base64_decode($localdata);
    		$localkeyresults = unserialize($localdata);
            $originalcheckdate = $localkeyresults["checkdate"];
            if ($md5hash==md5($originalcheckdate.$licensing_secret_key)) {
                $localexpiry = date("Ymd",mktime(0,0,0,date("m"),date("d")-$localkeydays,date("Y")));
                if ($originalcheckdate>$localexpiry) {
                    $localkeyvalid = true;
                    $results = $localkeyresults;
                    $validdomains = explode(",",$results["validdomain"]);
                    if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    $validips = explode(",",$results["validip"]);
                    if (!in_array($usersip, $validips)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    if ($results["validdirectory"]!=dirname(__FILE__)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                }
            }
        }
    }
    if (!$localkeyvalid) {
        $postfields["licensekey"] = $licensekey;
        $postfields["domain"] = $_SERVER['SERVER_NAME'];
        $postfields["ip"] = $usersip;
        $postfields["dir"] = dirname(__FILE__);
        if ($check_token) $postfields["check_token"] = $check_token;
        if (function_exists("curl_exec")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $whmcsurl."modules/servers/licensing/verify.php");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            $fp = fsockopen($whmcsurl, 80, $errno, $errstr, 5);
	        if ($fp) {
        		$querystring = "";
                foreach ($postfields AS $k=>$v) {
                    $querystring .= "$k=".urlencode($v)."&";
                }
                $header="POST ".$whmcsurl."modules/servers/licensing/verify.php HTTP/1.0\r\n";
        		$header.="Host: ".$whmcsurl."\r\n";
        		$header.="Content-type: application/x-www-form-urlencoded\r\n";
        		$header.="Content-length: ".@strlen($querystring)."\r\n";
        		$header.="Connection: close\r\n\r\n";
        		$header.=$querystring;
        		$data="";
        		@stream_set_timeout($fp, 20);
        		@fputs($fp, $header);
        		$status = @socket_get_status($fp);
        		while (!@feof($fp)&&$status) {
        		    $data .= @fgets($fp, 1024);
        			$status = @socket_get_status($fp);
        		}
        		@fclose ($fp);
            }
        }
        if (!$data) {
            $localexpiry = date("Ymd",mktime(0,0,0,date("m"),date("d")-($localkeydays+$allowcheckfaildays),date("Y")));
            if ($originalcheckdate>$localexpiry) {
                $results = $localkeyresults;
            } else {
                $results["status"] = "Invalid";
                $results["description"] = "Remote Check Failed";
                return $results;
            }
        } else {
            preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
            $results = array();
            foreach ($matches[1] AS $k=>$v) {
                $results[$v] = $matches[2][$k];
            }
        }
        if ($results["md5hash"]) {
            if ($results["md5hash"]!=md5($licensing_secret_key.$check_token)) {
                $results["status"] = "Invalid";
                $results["description"] = "MD5 Checksum Verification Failed";
                return $results;
            }
        }
        if ($results["status"]=="Active") {
            $results["checkdate"] = $checkdate;
            $data_encoded = serialize($results);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($checkdate.$licensing_secret_key).$data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded.md5($data_encoded.$licensing_secret_key);
            $data_encoded = wordwrap($data_encoded,80,"\n",true);
            $results["localkey"] = $data_encoded;
        }
        $results["remotecheck"] = true;
    }
    unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);
    return $results;
}

function db_sms_credit() {
   global $wpdb;
   $table_name = $wpdb->prefix . "users";
   $sms_credit = "0";
   $sql = "ALTER TABLE " .$table_name. " ADD sms_credit text";
   $wpdb->query($sql);
}
function sms_get_sms_credit_db() {
	global $wpdb, $ID, $current_user, $curauth;
	$ID = $curauth->ID;
	$table_name = $wpdb->prefix . "users";
	$sql = "SELECT sms_credit FROM " .$table_name. " WHERE ID = " .$ID. " ";
	$active_rows = $wpdb->get_results($sql);

	foreach ($active_rows as $active_row){
		echo $active_row->sms_credit;
	}
}
function sms_get_sms_credit_db_side() {
	global $wpdb, $ID, $current_user;
	get_currentuserinfo();
	$ID = $current_user->ID;
	$table_name = $wpdb->prefix . "users";
	$sql = "SELECT sms_credit FROM " .$table_name. " WHERE ID = " .$ID. " ";
	$active_rows = $wpdb->get_results($sql);

	foreach ($active_rows as $active_row){
		echo $active_row->sms_credit;
	}
}

function sms_get_sms_credit() {

include 'get_sms_code.php';

}

function sms_add_sms_credit_db() {
	global $wpdb, $current_user, $va_options;
	if(isset($_POST['cod'])){
	$cod = $_POST['cod'];
        $options = APP_Gateway_Registry::get_gateway_options( 'sms-gateway' );
        if( !empty( $options['sms_user_key'] ) )
	$userkey= $options['sms_user_key'];
	$processurl = $options['sms_process_url'];
	$res = smsapi($userkey,$cod,$userkey);
	$sms = explode(";",$res);
	if($sms[0]=="success"){
 		$tel = $sms[2];
  		$total = $sms[1];
		echo "<br />";
		echo "<div style='background-color:#FFFFCC;border:0px solid black;padding:10px;'> <p style='color: red; text-align: center'><strong>"; _e('Contul tau a fost incarcat de pe numarul ', 'sms'); echo $tel; _e(' cu suma de ', 'sms'); echo $m1 ="  $total</strong></p></div>";
        get_currentuserinfo();
	$ID = $current_user->ID;
	$table_name = $wpdb->prefix . "users";
	$sms_credit = $wpdb->get_var( $wpdb->prepare( "SELECT sms_credit FROM " .$table_name. " WHERE ID = " .$ID. "" ) );
	$sql = "UPDATE " .$table_name. " SET `sms_credit`= " .$sms_credit. " + " .$total. " WHERE ID = " .$ID. "";
	$wpdb->query($sql);
    
    $listings_permalink = $va_options->dashboard_listings_permalink;
	$permalink = $va_options->dashboard_permalink;
 
	if ( get_option('permalink_structure') != '' ) {
		$url = home_url( user_trailingslashit( $permalink ) );
	} else {
		$url = home_url( '?dashboard='.$listings_permalink.'&dashboard_author=self' );
	}
    $post_url = $url;
    
	?>
	<meta http-equiv="refresh" content="0; URL=<?php echo esc_url( $post_url ); ?>">
	<?php

	}else{
	$error .= $sms[0];
		echo "<div style='background-color:#FFFFCC;border:0px solid black;padding:10px;'>
		  <p style='color: red; text-align: center'><strong>"; _e('Ne pare rau.', 'sms'); echo $m1 = "$error</strong></p>";
		  	//echo $res;
?>
<form method="POST">
	<p align="center"><input type="text" name="cod" size="20"><input type="submit" class="btn_orange" value="<?php _e('Valideaza', 'sms'); ?>" name="B1" ></p>
</form>
<?php
	echo "</div><br />";
	}
}else{

	?>
	<div style='background-color:#FFFFCC;border:0px solid black;padding:10px;'>
	<p><center><strong><?php _e('Ai un COD SMS? Valideaza-l mai jos'); ?></strong></p>
	<em><p style='color: red; text-align: center'><?php _e('*ATENTIE: Validarea codului va duce automat la incarcarea contului dumneavoastra', 'sms'); ?></p></em>
	<form method="POST">
	<p align="center"><input type="text" name="cod" size="20"><input type="submit" class="btn_orange" value="<?php _e('Valideaza', 'sms'); ?>" name="B1" ></p>
</form></center></div><br>

<?php

}
	
}

function sms_check_sms_credit() {
	global $wpdb, $ID, $current_user;
	get_currentuserinfo();
	$ID = $current_user->ID;
	$table_name = $wpdb->prefix . "users";
	$sql = "SELECT sms_credit FROM " .$table_name. " WHERE ID = " .$ID. " ";
	$active_rows = $wpdb->get_results($sql);

	foreach ($active_rows as $active_row){
		return $active_row->sms_credit;
	}
}
function sms_pay_by_sms_credit(&$cost) {
	global $wpdb, $current_user;
	get_currentuserinfo();
	$ID = $current_user->ID;
    $total_cost = $cost;
	$table_name = $wpdb->prefix . "users";
	$sms_credit = $wpdb->get_var( $wpdb->prepare( "SELECT sms_credit FROM " .$table_name. " WHERE ID = " .$ID. "" ) );
	$sql = "UPDATE " .$table_name. " SET `sms_credit`= " .$sms_credit. " - " .$total_cost. " WHERE ID = " .$ID. "";
	$wpdb->query($sql);

}
function sms_add_sms_style () {
	$sms_echo = '/wp-content/plugins/vantage-sms-payment-gateway/includes/sms_style.css';
	echo "<link rel='stylesheet' href='"; echo $sms_echo; echo "' type='text/css' media='all' />" . "\n";
}

function cp_author_info_sms() {
	global $wpdb, $ID, $current_user, $curauth, $cp_options;
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active('vantage-sms-payment-gateway/sms.php')) { 
	$auth_ID =  $curauth->ID;
	get_currentuserinfo();
	$curr_user_ID = $current_user->ID;
	# Get Variables from storage (retrieve from wherever it's stored - DB, file, etc...)
#echo "<textarea cols=100 rows=20>"; print_r($results); echo "</textarea>";
if ( is_user_logged_in() ) {
			if ($auth_ID == $curr_user_ID) {
			sms_get_sms_credit_db();

	}
	else {	sms_get_sms_credit_db_side();  }
	echo $m1 = "<div class='smsico'></div>";
       	_e(' &euro; credit ','sms'); echo $m2 = " - <a href='?run=incarca'>"; _e('Incarca-ti contul SMS', 'sms'); echo $m3 =" </a> - <a href='?run=confirma'>";	_e('Confirma Cod SMS', 'sms'); echo $m4 = "</a>";
	include_once( ABSPATH . '/wp-content/plugins/vantage-sms-payment-gateway/includes/select_function.php' );
    } 
    }
}
add_action( 'va_dashboard_sidebar_links', 'cp_author_info_sms');
?>