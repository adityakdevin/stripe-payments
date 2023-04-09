<?php
require_once 'config.php';
$stripe = new \Stripe\StripeClient(
    config('secret_key')
);
try {
    $product = $stripe->products->create([
        'name' => 'SEO BASIC',
    ]);
    echo json_encode(['product' => $product]);
} catch (\Stripe\Exception\ApiErrorException $e) {
}