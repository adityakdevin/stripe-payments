<?php
require_once 'config.php';
$stripe = new \Stripe\StripeClient(
    config('secret_key')
);

try {
    $subscription = $stripe->subscriptions->retrieve(
        $_REQUEST['subscriptionId'],
        []
    );
    echo json_encode($subscription);
} catch (\Stripe\Exception\ApiErrorException $e) {
}
