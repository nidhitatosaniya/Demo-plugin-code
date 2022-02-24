<?php
/*
Plugin Name: Create contact in mailjet when order is placed
*/
require_once ABSPATH. 'wp-load.php';
require 'mailjetrestapi/vendor/autoload.php';
use \Mailjet\Resources;
//$admin_email =  get_option( 'admin_email' );
//echo $admin_email;



add_action( 'woocommerce_new_order', 'create_contact_mailjet_order_placed', 10, 3 );
//add_action( 'init', 'create_contact_mailjet_order_placed', 10, 0 );

function create_contact_mailjet_order_placed($order_id) {
global $wpdb;
$order = wc_get_order( $order_id );
$order_id  = $order->get_id();
$billing_email  = $order->get_billing_email();
$billing_firstname  = $order->get_billing_first_name();
$billing_lastname  = $order->get_billing_last_name();
$admin_email = get_option( 'admin_email' );



$apikey = 'd897aebc4e090d4959157a14b0a9628a';
$apisecret = 'b311375498156ea38057d2f82d6e8a9a';

$mj = new \Mailjet\Client($apikey, $apisecret);

$body = [
    'Email' => $billing_email
];
$response = $mj->post(Resources::$Contact, ['body' => $body]);
$response->success() && var_dump($response->getData());
//echo"<pre>";print_r($response->success());echo"</pre>";
if($response->success() === TRUE){

    $to = $admin_email;
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $subject = 'Contact exported successsfully';
    $msg = "
    <html>
    <head>
    <title>HTML email</title>
    </head>
    <body>
    <p><b>Order No :    </b> $order_id </p>
    <p><b>First Name :  </b> $billing_firstname </p>
    <p><b>Last Name :   </b> $billing_lastname </p>
    </body>
    </html>
    ";


    if (wp_mail($to, $subject, $msg, $headers)) {
        echo "sent mail successsfully";
    } else {
       echo "could not sent email";
    }


}

}

