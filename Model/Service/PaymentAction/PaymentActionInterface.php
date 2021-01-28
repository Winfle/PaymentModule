<?php

namespace Payment\Checkout\Model\Service\PaymentAction;

use Magento\Sales\Model\Order\Payment;

interface PaymentActionInterface
{
    /**
     * @param Payment $payment
     *
     * @return void
     */
    public function process(Payment $payment) : void;
}
