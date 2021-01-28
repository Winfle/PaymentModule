<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Service\PaymentAction;

use Magento\Sales\Model\Order\Payment;

class Authorize implements PaymentActionInterface
{
    /**
     * @var bool
     */
    private $capturePayment;

    /**
     * Authorize constructor.
     *
     * @param $capturePayment
     */
    public function __construct(bool $capturePayment)
    {
        $this->capturePayment = $capturePayment;
    }

    /**
     * @param Payment $payment
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process(Payment $payment) : void
    {
        $payment->authorize(true, $payment->getOrder()->getBaseTotalDue());
        if ($this->capturePayment) {
            $payment->capture();
        }
    }
}
