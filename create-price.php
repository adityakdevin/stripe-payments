<?php
require_once 'config.php';
$stripe = new \Stripe\StripeClient(
    config('secret_key')
);
if (!empty($_POST)) {
    try {
        $price = $stripe->prices->create([
            'product' => $_POST['product_id'],
            'unit_amount' => $_POST['unit_amount'] * 100,
            'currency' => 'usd',
            'recurring' => [
                'interval' => $_POST['interval'],
            ],
        ]);

        $products = $stripe->products->all();
        $prices = $stripe->prices->all();
        echo json_encode($price);
    } catch (\Stripe\Exception\ApiErrorException $e) {
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Price</title>
</head>
<body>
<table border="1" width="100%">
    <thead>
    <tr>
        <th>Price ID</th>
        <th>Amount</th>
        <th>Type</th>
        <th>Recurring Interval</th>
    </tr>
    </thead>
    <?php
    if (!empty($prices)) {
        foreach ($prices as $price) {
            ?>
            <tr>
                <td><?= $price->id ?></td>
                <td><?= $price->unit_amount ?></td>
                <td><?= $price->type ?></td>
                <td><?= $price->type == 'recurring' ? $price->recurring->interval : '' ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
<hr>
<h3>Add New Price</h3>
<form action="" method="post">
    <label for="product_id">Select Product</label>
    <select name="product_id" id="product_id">
        <?php
        if (!empty($products)) {
            foreach ($products as $product) {
                ?>
                <option value="<?= $product->id ?>"><?= $product->name ?></option>
                <?php
            }
        }
        ?>
    </select>
    <label for="unit_amount">Amount</label>
    <input type="number" name="unit_amount">
    <label for="interval">Select Type</label>
    <select name="interval" id="interval">
        <option value="month">Monthly</option>
        <option value="year">Yearly</option>
    </select>
    <input type="submit" name="create_price" value="Create">
</form>
</body>
</html>
