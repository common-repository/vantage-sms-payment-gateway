<?php
if (isset($_GET['run'])) $linkchoice=$_GET['run']; 					
else $linkchoice=''; 

switch($linkchoice){ 

case 'incarca' : 
    sms_get_sms_credit();
    break; 
case 'confirma' : 
    sms_add_sms_credit_db();
    break; 
default : 
}
?>