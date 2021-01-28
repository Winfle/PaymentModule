<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Service\PaymentAction;

use Payment\Checkout\Api\PaymentStatusInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

class ActionFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \Payment\Checkout\Model\Config\Payment
     */
    private $paymentConfig;

    /**
     * Factory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        \Payment\Checkout\Model\Config\Payment $paymentConfig
    ) {
        $this->objectManager = $objectManager;
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * @param $paymentType
     *
     * @return PaymentActionInterface
     */
    public function get($paymentType)
    {
        switch ($paymentType) {
            case PaymentStatusInterface::TYPE_CARD:
            case PaymentStatusInterface::TYPE_MASTERCARD:
                return $this->objectManager->create(
                    Authorize::class,
                    [
                        'capturePayment' => $this->paymentConfig->isAutoCapture()
                    ]
                );
                break;
            case PaymentStatusInterface::TYPE_INVOICE:
            case PaymentStatusInterface::TYPE_LOAN:
            case PaymentStatusInterface::TYPE_SWISH:
                return $this->objectManager->create(CaptureInvoice::class);
                break;
        }

        return $this->objectManager->create(NullAction::class);
    }
}
