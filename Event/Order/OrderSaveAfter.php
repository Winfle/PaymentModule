<?php declare(strict_types=1);

namespace Payment\Checkout\Event\Order;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class OrderSaveAfter
 *
 * @package Payment\Checkout\Event\Order
 */
class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Payment\Checkout\Model\Service\PaymentProcessor
     */
    private $paymentProcessor;

    /**
     * OrderSaveAfter constructor.
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Payment\Checkout\Model\Service\PaymentProcessor $paymentProcessor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->paymentProcessor = $paymentProcessor;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        if ($order->getPayment()->getMethod() !== \Payment\Checkout\Model\Payment\Payment::CODE) {
            return;
        }

        if ($order->getStatus() == $this->getOrderCaptureStatus()) {
            $this->paymentProcessor->processPayment($order->getPayment());
        }
    }

    /**
     * @return mixed
     */
    private function getOrderCaptureStatus()
    {
        return $this->scopeConfig->getValue('payment/checkout_config/complete_status');
    }
}
