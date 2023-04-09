<?php require_once 'config.php' ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Stripe Secure Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="<?= config('meta_description') ?>" />
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/global.css" />
  </head>
  <body>
    <div class="sr-root">
      <div class="sr-main">
        <header class="sr-header">
          <div class="sr-header__logo" style="background: url('<?= config('logo') ?>')"></div>
        </header>
        <div class="sr-payment-summary payment-view">
          <h1 class="order-amount" id="order-amount-id"></h1>
          <h4 id="plan-description-id"></h4>
        </div>
        <form id="payment-form">
          <input type="hidden" id="selected_price" value="<?=@$_REQUEST['price_id']?>"/>
          <div id="coupondiv" class="sr-payment-form payment-view hidden">
            <div id="coupon-text-wrapper" >
              <input type="text" id="coupon-code-id" placeholder="Coupon Code" />
              <input type="hidden" id="promotion_code" />
            </div>
            <button  id="coupon-apply-button" class="coupon-button" >
              <div id="spinner-apply" class="hidden"></div>
              <span id="button-text-apply">Apply Coupon</span>
            </button>
          </div>
          <div class="sr-payment-form payment-view">
            <div class="sr-form-row">
              <label for="card-element">
                Payment details
              </label>
              <div class="sr-combo-inputs">
                <div class="sr-combo-inputs-row">
                  <input type="text" id="email" placeholder="Email" autocomplete="cardholder" class="sr-input" />
                </div>
                <div class="sr-combo-inputs-row">
                  <div class="sr-input sr-card-element" id="card-element"></div>
                </div>
              </div>
              <div class="sr-field-error" id="card-errors" role="alert"></div>
            </div>
            <div class="sr-form-row" id="terms-check-wrapper">
              <input type="checkbox" class="form-check-input" id="terms-check">
              <label class="form-check-label" for="termsCheckLabel">I hereby accept the <a href="<?=config('tmc')?>" target="_blank"> Terms and Conditions</a></label>
      		    <div class="text-center">
                    <label class="form-check-label">
                        <a id="showCouponLink" href="#"><span style="color:#ddf">Click to Apply Coupon</span></a>
                    </label>
                </div>
            </div>
            <button id="submit">
              <div id="spinner" class="hidden"></div>
              <span id="button-text">Subscribe</span>
            </button>
            <div class="sr-legal-text">
              Your card will be immediately charged
              <span class="order-total" id="order-amount-small-id"></span>.
            </div>
          </div>
        </form>
        <div class="sr-payment-summary hidden" id="pay-result">
          <h1>Your subscription is <span class="order-status"></span></h1>
        </div>
      </div>
      <div class="sr-content">
       
      </div>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/script.js" defer></script>
  </body>
</html>
