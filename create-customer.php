<?php
require_once 'config.php';
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
$stripe = new StripeClient(
    config('secret_key')
);
try {
    $customers = $stripe->customers->all(['email' => $_REQUEST['email']]);
    try {
        $price = $stripe->prices->retrieve($_REQUEST['price_id']);
        try {
            $payment = $stripe->paymentMethods->retrieve($_REQUEST['payment_method']);
            if (empty($customers->data)) {
                try {
                    $customer = $stripe->customers->create(['email' => $_REQUEST['email']]);
                } catch (ApiErrorException $e) {
                    echo json_encode($e->getMessage());
                }
            } else {
                try {
                    $customer = $stripe->customers->retrieve($customers->data[0]->id);
                } catch (ApiErrorException $e) {
                    echo json_encode($e->getMessage());
                }
            }
            if (!empty($customer)) {
                try {
                    $paymentMethod = $stripe->paymentMethods->attach(
                        $payment->id,
                        ['customer' => $customer->id]
                    );

                    try {
                        $customer = $stripe->customers->update($customer->id, [
                            'invoice_settings' => [
                                'default_payment_method' => $paymentMethod->id,
                            ],
                            'metadata' => [
                                'coupon_code' => @$_REQUEST['coupon_code'],
                                'price_id' => $price->id
                            ]
                        ]);

                        $subscription_data = ['customer' => $customer->id,
                            'items' => [
                                [
                                    'price' => $price->id,
                                    'quantity' => 1,
                                ]
                            ],
                            'default_payment_method' => $paymentMethod->id,
                            'expand' => ['latest_invoice.payment_intent'],
                        ];
                        if (!empty($_REQUEST['coupon_code'])) {
                            $subscription_data['promotion_code'] = $_REQUEST['promotion_code'];
                        }
                        $subscription = $stripe->subscriptions->create($subscription_data);
                        echo json_encode($subscription);
                    } catch (ApiErrorException $e) {
                        echo json_encode($e->getMessage());
                    }
                } catch (ApiErrorException $e) {
                    echo json_encode($e->getMessage());
                }
            }
        } catch (ApiErrorException $e) {
            echo json_encode($e->getMessage());
        }
    } catch (ApiErrorException $e) {
        echo json_encode($e->getMessage());
    }
} catch (ApiErrorException $e) {
    echo json_encode($e->getMessage());
}