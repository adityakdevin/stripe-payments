<?php
require_once 'config.php';
$stripe = new \Stripe\StripeClient(
    config('secret_key')
);
try {
    $plan = $stripe->plans->create([
        'amount' => 100,
        'currency' => 'usd',
        'interval' => 'month',
        'product' => 'prod_KFvI2xyRmsytuS',
        'nickname' => 'BASIC PLAN FOR SEO',
        'active' => true,
        "metadata" => [
            'plan_name' => 'BASIC PLAN FOR SEO'
        ]
    ]);
    echo json_encode(['product' => $plan]);
} catch (\Stripe\Exception\ApiErrorException $e) {
}

