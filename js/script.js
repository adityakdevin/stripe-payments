let stripe;

let stripeElements = function (publicKey) {
    stripe = Stripe(publicKey);
    let elements = stripe.elements();
    let style = {
        base: {
            fontSize: '16px',
            color: '#32325d',
            fontFamily:
                '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif',
            fontSmoothing: 'antialiased',
            '::placeholder': {
                color: 'rgba(0,0,0,0.4)'
            }
        }
    };
    let card = elements.create('card', {style: style});
    card.mount('#card-element');
    // Element focus ring
    card.on('focus', function () {
        let el = document.getElementById('card-element');
        el.classList.add('focused');
    });
    card.on('blur', function () {
        let el = document.getElementById('card-element');
        el.classList.remove('focused');
    });
    document.querySelector('#submit').addEventListener('click', function (evt) {
        evt.preventDefault();
        if ($("#terms-check").prop("checked") == true) {
            changeLoadingState(true);
            createPaymentMethodAndCustomer(stripe, card);
        } else {
            alert("You Must Accept the Terms and Conditions before proceeding!")
        }
    });
};

function showCardError(error) {
    changeLoadingState(false);
    // The card was declined (i.e. insufficient funds, card has expired, etc)
    let errorMsg = document.querySelector('.sr-field-error');
    errorMsg.textContent = error.message;
    setTimeout(function () {
        errorMsg.textContent = '';
    }, 8000);
}

let createPaymentMethodAndCustomer = function (stripe, card) {
    let cardholderEmail = document.querySelector('#email').value;
    stripe.createPaymentMethod('card', card, {
        billing_details: {
            email: cardholderEmail
        }
    }).then(function (result) {
        if (result.error) {
            showCardError(result.error);
        } else {
            createCustomer(result.paymentMethod.id, cardholderEmail);
        }
    });
};

async function createCustomer(paymentMethod, cardholderEmail) {
    $.ajax({
        type: "POST",
        url: "create-customer.php",
        data: {
            email: cardholderEmail,
            payment_method: paymentMethod,
            coupon_code: $("#coupon-code-id").val(),
            promotion_code: $("#promotion_code").val(),
            price_id: $("#selected_price").val()
        },
        dataType: 'json',
        success: function (res) {
            handleSubscription(res);
        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function handleSubscription(subscription) {
    const payment_intent = subscription.payment_intent;
    if (payment_intent) {
        const client_secret = payment_intent.client_secret;
        const status = payment_intent.status;
        console.log("client_secret " + client_secret);
        console.log("status " + status);
        if (status === 'requires_action' || status === 'requires_payment_method' || status === 'requires_source_action') {
            stripe.confirmCardPayment(client_secret).then(function (result) {
                if (result.error) {
                    changeLoadingState(false);
                    showCardError(result.error);
                } else {
                    confirmSubscription(subscription.id);
                }
            });
        } else {
            confirmSubscription(subscription.id);
        }
    } else {
        confirmSubscription(subscription.id);
    }
}

function confirmSubscription(subscriptionId) {
    $.ajax({
        type: "POST",
        url: "subscription.php",
        data: {
            subscriptionId: subscriptionId
        },
        dataType: 'json',
        success: function (res) {
            orderComplete(res);
        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function getPlanDetails() {
    $.ajax({
        type: "POST",
        url: "prices.php",
        data: {price_id: $("#selected_price").val()},
        dataType: 'json',
        success: function (res) {
            if ($("#selected_price").val()===''){
                $("#selected_price").val(res.id);
            }
            $("#order-amount-id").text(`Purchasing ${res.nickname} for Monthly amount of $${res.unit_amount / 100}`);
            $("#order-amount-small-id").text(`$${res.unit_amount / 100}`);
            $("#plan-description-id").text(`You are Subscribing for ${res.nickname}`);
        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function getPublicKey() {
    $.ajax({
        type: "POST",
        url: "public_key.php",
        dataType: 'json',
        success: function (res) {
            stripeElements(res.publicKey);
        }
    });
}


/* ------- Post-payment helpers ------- */

/* Shows a success / error message when the payment is complete */
let orderComplete = function (subscription) {
    changeLoadingState(false);

    // let subscriptionJson = JSON.stringify(subscription, null, 2);
    document.querySelectorAll('.payment-view').forEach(function (view) {
        view.classList.add('hidden');
    });
    document.querySelectorAll('.completed-view').forEach(function (view) {
        view.classList.remove('hidden');
    });
    document.querySelector('.order-status').textContent = subscription.status;
    document.querySelector('#pay-result').classList.remove('hidden');
    // document.querySelector('code').textContent = subscriptionJson;
};
let changeLoadingStateApply = function (isLoading) {
    if (isLoading) {
        document.querySelector('#spinner-apply').classList.add('loading');
        document.querySelector('#coupon-apply-button').disabled = true;

        document.querySelector('#button-text-apply').classList.add('hidden');
    } else {
        document.querySelector('#coupon-apply-button').disabled = false;
        document.querySelector('#spinner-apply').classList.remove('loading');
        document.querySelector('#button-text-apply').classList.remove('hidden');
    }
};
// Show a spinner on subscription submission
let changeLoadingState = function (isLoading) {
    if (isLoading) {
        document.querySelector('#spinner').classList.add('loading');
        document.querySelector('button').disabled = true;

        document.querySelector('#button-text').classList.add('hidden');
    } else {
        document.querySelector('button').disabled = false;
        document.querySelector('#spinner').classList.remove('loading');
        document.querySelector('#button-text').classList.remove('hidden');
    }
};
let applyCouponClick = function () {
    changeLoadingStateApply(true);
    $.ajax({
        type: "POST",
        url: "coupons.php",
        dataType: 'json',
        data:{
            price_id: $("#selected_price").val(),
            coupon_code: $("#coupon-code-id").val(),
        },
        success: function (res) {
            if (res.status) {
                document.querySelector("#coupon-text-wrapper").classList.add("success");
                document.querySelector("#coupon-text-wrapper").classList.remove("fail");
                $("#order-amount-id").text("Purchasing " + res.price.nickname + " for Monthly amount of $" + res.price.unit_amount / 100);
                $("#order-amount-small-id").text("$" + res.price.unit_amount / 100);
                $("#plan-description-id").text("You are Subscribing for " + res.price.nickname);
                $("#coupon-code-id").prop("disabled", true);
                $("#promotion_code").val(res.promotion_code);
            } else {
                document.querySelector("#coupon-text-wrapper").classList.remove("success");
                document.querySelector("#coupon-text-wrapper").classList.add("fail");
                $("#promotion_code").val('res.promotion_code');
            }
            changeLoadingStateApply(false);
        }
    });
    return false;
}

/**
 * Startup Action
 */
$(document).ready(function () {
    getPlanDetails();
    getPublicKey();
    $("#coupon-apply-button").click(function (e) {
        e.preventDefault();
        applyCouponClick();
    });
    $("#showCouponLink").click(function () {
        $("#coupondiv").removeClass("hidden");
    });
});
  
