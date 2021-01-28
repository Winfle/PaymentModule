<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Service;

use Payment\Checkout\Model\Payment\Payment;
use Payment\Checkout\Model\Service\PaymentAction\ActionFactory;
use Magento\Sales\Model\Order\Payment;

/**
 * Class PaymentProcessor
 *
 * @package Payment\Checkout\Model\Service
 */
class PaymentProcessor
{
    /**
     * @var PaymentAction\ActionFactory
     */
    private $actionFactory;

    /**
     * PaymentProcessor constructor.
     *
     * @param PaymentAction\ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * @param Payment $payment
     */
    public function processPayment(Payment $payment)
    {
        $actionType = $payment->getAdditionalInformation()[Payment::INFO_METHOD];

        $paymentAction = $this->actionFactory->get($actionType);
        $paymentAction->process($payment);
    }
}
