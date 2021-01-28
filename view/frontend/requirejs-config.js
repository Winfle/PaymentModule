var config = {
    map: {
        '*': {
            paymentCheckout: 'Payment_Checkout/js/checkout'
        }
    },
    paths: {
        slick: 'Payment_Checkout/js/libs/slick.min'
    },
    shim: {
        slick: {
            deps: ['jquery']
        }
    }
};
