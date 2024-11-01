<?php
class SMS_Gateway_grinMedia extends APP_Gateway{

	public function __construct() {
		parent::__construct( 'sms-gateway', array(
			'dropdown' => __( 'SMS Gateway - grinMedia.ro', SMS ),
			'admin' => __( 'SMS Gateway - grinMedia.ro', SMS ),
		) );

	}

	public function form() {
        $sms_fields = array(
        
         array(
           'title' => __( 'Cheie de licentiere', SMS ),
           'name' => 'sms_licence_key',
           'type' => 'text', // text, textarea, checkbox
    ),
         array(
           'title' => __( 'ID User', SMS ),
           'name' => 'sms_user_key',
           'type' => 'text', // text, textarea, checkbox
    ),
         array(
           'title' => __( 'Process URL', SMS ),
           'name' => 'sms_process_url',
           'type' => 'text', // text, textarea, checkbox
    )
    );

		$sms_array = array(
			"title" => __( 'SMS Payment Gateway', SMS ),
			"fields" => $sms_fields
		);

		return $sms_array;

	}

	public function process( $order, $options ) {
/*
		$sent = get_post_meta( $order->get_ID(), 'bt-sentemail', true );
		if( empty( $sent ) ){
			appthemes_bank_transfer_pending_email( get_post( $order->get_ID() ) );
			update_post_meta( $order->get_ID(), 'bt-sentemail', true ); 
            
		}*/

	?>
    <style type="text/css">
		#bank-transfer fieldset{
			margin-bottom: 20px;
		}
		#bank-transfer .content{
			width: 795px;
			padding: 20px;
		}
		#bank-transfer pre{
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			padding-top: 10px;
		}
	</style>
	<div class="section-head"><h2><?php _e( 'SMS Gateway', SMS ); ?></h2></div>
	<form id="bank-transfer">
		<fieldset>
			<div class="featured-head"><h3><?php _e( 'Instructiuni:', SMS ); ?></h3></div>
            <div class="content"><pre>
<?
$licenta = $options['sms_licence_key'];
$licensekey = whmcs_sms_licence_get_licencekey();
$localkey = whmcs_sms_licence_get_localkey();
$results = check_license($licensekey,$localkey);
#echo "<textarea cols=100 rows=20>"; print_r($results); echo "</textarea>";
if ($results["status"]=="Active") {
    whmch_sms_license_install_db($licenta);
    if ( sms_check_sms_credit() <= $order->get_total()) {echo "<br />"; _e('Incarca-ti contul SMS. Pentru a face acest lucru este nevoie sa mergeti in dashboard-ul dvs.', 'sms'); }
	else { 
	   $order->complete(); 
       $cost = $order->get_total();
       sms_pay_by_sms_credit($cost);
       }
	}
 elseif ($results["status"]=="Invalid") {
    # Show Invalid Message
	    whmch_sms_license_install_db($licenta);
        echo "<br />";
        _e('Licenta SMS Invalida. Va rugam sa contactati administratorul site-ului pentru a continua.', 'sms');

} elseif ($results["status"]=="Expired") {
    # Show Expired Message
	    whmch_sms_license_install_db($licenta);
        echo "<br />";
        _e('Licenta SMS Expirata. Va rugam sa contactati administratorul site-ului pentru a continua.', 'sms');

} elseif ($results["status"]=="Suspended") {
    # Show Suspended Message
	    whmch_sms_license_install_db($licenta);
        echo "<br />";
        _e('Licenta SMS Expirata. Va rugam sa contactati administratorul site-ului pentru a continua.', 'sms');
}
?></pre></div>
			<div class="content">
				<pre></pre>
			</div>
		</fieldset>
		<fieldset>
			<div class="featured-head"><h3><?php _e( 'Informatii Comanda', SMS ); ?></h3></div>

			<div class="content">
<pre><?php _e( 'ID Comanda:', SMS ); ?> <?php echo $order->get_id(); ?> 
<?php _e( 'Total Comanda:', SMS ); ?> <?php echo APP_Currencies::get_price( $order->get_total(), $order->get_currency() ); ?>


<?php _e( 'Pentru intrebari sau probleme, va rugam sa ne contactati la:', SMS ) ?> <?php echo get_option('admin_email'); ?>

</pre>
			</div>
		</fieldset>
	</form>
	<div class="clear"></div>
	<?php
	}
}

appthemes_register_gateway( 'SMS_Gateway_grinMedia' );