<?php
require_once 'config.php';
$stripe = new \Stripe\StripeClient(
    config('secret_key')
);
if (!empty($_REQUEST['planId'])) {
    $planId = $_REQUEST['planId'];
    try {
        $plan = $stripe->plans->retrieve(
            'plan_KFvNeS7n3P5Sf2',
            []
        );
        echo json_encode($plan);
    } catch (\Stripe\Exception\ApiErrorException $e) {
    }

} else {
    try {
        $stripe->plans->all(['limit' => 1]);
    } catch (\Stripe\Exception\ApiErrorException $e) {

    }
}