<?php

namespace Payment\Checkout\Model\Payment\Command;

use Payment\Checkout\Rest\Adapter\CancelAdapter;
use Payment\Checkout\Rest\Exception\RefundException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;

class Cancel implements CommandInterface
{
    /**
     * @var \Payment\Checkout\Rest\Service\Authentication
     */
    private $authenticationService;

    /**
     * @var \Payment\Checkout\Rest\Service\Refund
     */
    private $refundService;

    /**
     * @var CancelAdapter
     */
    private $cancelAdapter;

    public function __construct(
        CancelAdapter $cancelAdapter,
        \Payment\Checkout\Rest\Service\Authentication $authenticationService
    ) {
        $this->authenticationService = $authenticationService;
        $this->cancelAdapter = $cancelAdapter;
    }

    /**
     * @param array $commandSubject
     *
     * @return \Magento\Payment\Gateway\Command\ResultInterface|void|null
     * @throws AuthenticationException
     * @throws CommandException
     * @throws RefundException
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObject $data */
        $data = $commandSubject['payment'] ?? null;
        if (! $data) {
            $this->throwCommandException('Cannot cancel the order.');
        }

        $purchaseId = $this->getPurchaseId($data->getPayment());
        if (! $purchaseId) {
            $this->throwCommandException('Cannot cancel the order.');
        }

        try {
            $storeId = $data->getOrder()->getStoreId();
            $this->authenticationService->authenticate($storeId);
            $this->cancelAdapter->cancel(
                $this->authenticationService->getToken(),
                $purchaseId,
                'Canceled by admin'
            );
        } catch (\Exception $e) {
            $this->throwCommandException('Cannot cancel the order.');
        }
    }

    /**
     * @param InfoInterface $payment
     *
     * @return string|null
     */
    private function getPurchaseId(InfoInterface $payment)
    {
        return $payment->getAdditionalInformation()['payment_purchase_id'] ?? null;
    }

    /**
     * @param $text
     * @param $argc
     *
     * @throws CommandException
     */
    private function throwCommandException($text, $argc = [])
    {
        throw new CommandException(new \Magento\Framework\Phrase($text, $argc));
    }
}
