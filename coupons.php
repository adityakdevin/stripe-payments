<?php
require_once 'config.php';
$stripe = new \Stripe\StripeClient(
    config('secret_key')
);
try {
    $price = $stripe->prices->retrieve($_REQUEST['price_id'], []);
    try {
        $promotionCodes = $stripe->promotionCodes->all(['code' => $_REQUEST['coupon_code']]);
        if (!empty($promotionCodes['data'])) {
            $promotionCode = $stripe->promotionCodes->retrieve($promotionCodes->data[0]->id);
            $return_data = ['code' => $promotionCode->code, 'status' => false, 'msg' => 'Invalid code.'];
            if ($promotionCode->active) {
                $coupon = $promotionCode->coupon;
                if ($coupon->valid) {
                    $total_price = $price->unit_amount;
                    if (!empty($coupon->percent_off)) {
                        $total_discount = ($coupon->percent_off / 100) * $total_price;
                        $return_data['status'] = true;
                        $return_data['msg'] = "Coupon code applied.";
                    }
                    if (!empty($coupon->amount_off) && $return_data['status'] === false && $total_price > $coupon->amount_off) {
                        $total_discount = $total_price - $coupon->amount_off;
                        $return_data['status'] = true;
                        $return_data['msg'] = "Coupon code applied.";
                    }
                    if ($return_data['status']) {
                        if (isset($total_discount)) {
                            $price->unit_amount = $total_price - $total_discount;
                            $price->unit_amount_decimal -= $total_discount;
                        }
                        $return_data['price'] = $price;
                        $return_data['promotion_code'] = $promotionCodes->data[0]->id;
                    }
                }
            }
            echo json_encode($return_data);
        } else {
            echo json_encode(['code' => $_REQUEST['coupon_code'], 'status' => false, 'msg' => 'Invalid Code']);
        }
    } catch (\Stripe\Exception\ApiErrorException $e) {
    }
} catch (\Stripe\Exception\ApiErrorException $e) {
}

