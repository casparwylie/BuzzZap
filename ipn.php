<?php
require("requires.php");
$snc_suc_email = get_static_contents("snc_suc_email");
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval);
  if (count($keyval) == 2)
     $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
   $get_magic_quotes_exists = true;
} 
foreach ($myPost as $key => $value) {        
   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
        $value = urlencode(stripslashes($value)); 
   } else {
        $value = urlencode($value);
   }
   $req .= "&$key=$value";
}

// STEP 2: POST IPN data back to PayPal to validate
$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// In wamp-like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set 
// the directory path of the certificate as shown below:
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if( !($res = curl_exec($ch)) ) {
    // error_log("Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit;
}
curl_close($ch);


// STEP 3: Inspect IPN validation result and act accordingly
if (strcmp ($res, "VERIFIED") == 0) {
  
  $com_ident = htmlentities($_POST['custom']);
  $myfile = fopen("ipnnnn.txt", "w");
  fwrite($myfile, $com_ident);
  fclose($myfile);
  $db->query("UPDATE com_act SET act = 1 WHERE act = 0 AND ipn = ".$db->quote($com_ident));
  $com_id = $db->query("SELECT com_id FROM com_act WHERE ipn = ".$db->quote($com_ident)." LIMIT 1")->fetchColumn();
  $leadername = $db->query("SELECT user_firstname FROM users WHERE user_com = ".$db->quote($com_id)." AND user_rank = 3 LIMIT 1");
  $email = $db->query("SELECT user_email FROM users WHERE user_com = ".$db->quote($com_id)." AND user_rank = 3 LIMIT 1");;
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $headers .= "From: Administration@buzzzap.com" . "\r\n";
  $com_name = $db->query("SELECT com_name FROM communities WHERE com_id = ".$db->quote($com_id))->fetchColumn();
  $body = $snc_suc_email;
  mail($email,"BuzzZap Community Activation",$body,$headers);
   
}
?>