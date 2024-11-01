<?php
function smsapi($userkey,$cod){
		$key = $userkey*2121;
		$hash = smshash($key,$cod);


		$url = "http://grinmedia.ro/clienti/api/rsmsapi.php";
		$postfields['clientid'] = $userkey;
    	$postfields['cod'] = $cod;
		$postfields['hash'] = $hash;


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$data = curl_exec($ch);
		curl_close($ch);


return $data;
}







		function smshash($key,$cod) {
			$b = 64; // byte length for md5
			if (strlen($key) > $b) {
				$key = pack("H*",md5($key));
			}
			$key  = str_pad($key, $b, chr(0x00));
			$ipad = str_pad('', $b, chr(0x36));
			$opad = str_pad('', $b, chr(0x5c));
			$k_ipad = $key ^ $ipad;
			$k_opad = $key ^ $opad;
			$data1 = $cod;
			$smshresp = md5($k_opad  . pack("H*",md5($k_ipad . $data1)));
			return $smshresp;
		}
?>