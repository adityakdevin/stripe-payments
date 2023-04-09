<?php
require_once 'config.php';
$stripe = new \Stripe\StripeClient(
    config('secret_key')
);
$price = '0';
if (!empty($_REQUEST['price_id'])) {
    $price_id = $_REQUEST['price_id'];
    try {
        $price = $stripe->prices->retrieve(
            $price_id,
            []
        );
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo json_encode($e->getMessage());
    }
} else {
    try {
        $prices = $stripe->prices->all(['limit' => 1]);
        $price = $prices->data[0];
    } catch (\Stripe\Exception\ApiErrorException $e) {
    }
}
echo json_encode($price);
die();
