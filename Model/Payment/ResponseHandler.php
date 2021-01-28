<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Payment;

use Payment\Checkout\Rest\Response\GetPaymentStatusResponse;
use Magento\Quote\Model\Quote\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;

class ResponseHandler
{
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param GetPaymentStatusResponse $paymentStatus
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function handlePaymentStatus(
        \Magento\Quote\Model\Quote $quote,
        GetPaymentStatusResponse $paymentStatus
    ) {
        $payment = $quote->getPayment();
        $payment->unsMethodInstance()->setMethod(\Payment\Checkout\Model\Payment\Payment::CODE);

        $data = [
            'payment_purchase_id'    => $paymentStatus->getPurchaseId(),
            'payment_method'         => $paymentStatus->getSelectedPaymentMethod(),
            'payment_payment_status' => $paymentStatus->getPaymentStatus(),
        ];

        $this->setPaymentData($payment, $data);
    }

    /**
     * @param Payment $payment
     * @param array $data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setPaymentData(Payment $payment, $data = []) : void
    {
        foreach ($data as $key => $value) {
            $payment->setAdditionalInformation(
                $key,
                $value
            );
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    public function addAuthTransaction(\Magento\Sales\Model\Order\Payment $payment)
    {
        $payment->authorize(true, $payment->getAmountOrdered());
    }
}
