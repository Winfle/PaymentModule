<?php declare(strict_types=1);

namespace Payment\Checkout\Plugin\Sales\Api;

class AssignExtensionAttributes
{
    /**
     * @param $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $order->getExtensionAttributes()->setPaymentPurchaseId(
            $order->getPaymentPurchaseId()
        );

        return $order;
    }
}
