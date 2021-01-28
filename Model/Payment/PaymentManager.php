<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Payment;

use Payment\Checkout\Api\PaymentManagementInterface;
use Payment\Checkout\Rest\Adapter\GetPaymentStatus;
use Payment\Checkout\Rest\Exception\PaymentStatusException;
use Payment\Checkout\Rest\Service\Authentication;

class PaymentManager implements PaymentManagementInterface
{
    /**
     * @var Authentication
     */
    private $authenticationService;

    /**
     * @var GetPaymentStatus
     */
    private $getPaymentStatusService;

    /**
     * PaymentManager constructor.
     *
     * @param Authentication $authenticationService
     * @param GetPaymentStatus $getPaymentStatusService
     */
    public function __construct(
        Authentication $authenticationService,
        GetPaymentStatus $getPaymentStatusService
    ) {
        $this->authenticationService = $authenticationService;
        $this->getPaymentStatusService = $getPaymentStatusService;
    }

    /**
     * @param $purchaseId
     *
     * @return \Payment\Checkout\Rest\Response\GetPaymentStatusResponse
     * @throws PaymentStatusException
     */
    public function getPaymentStatus($purchaseId): \Payment\Checkout\Rest\Response\GetPaymentStatusResponse
    {
        try {
            $this->authenticationService->authenticate();
            $authToken = $this->authenticationService->getToken();

            return $this->getPaymentStatusService->getStatus($purchaseId, $authToken);
        } catch (\Exception $e) {
            throw PaymentStatusException::create($e);
        }
    }
}
