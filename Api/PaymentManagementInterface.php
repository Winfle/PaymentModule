<?php

namespace Payment\Checkout\Api;

interface PaymentManagementInterface
{
    /**
     * @throws \Payment\Checkout\Rest\Exception\PaymentStatusException
     * @return \Payment\Checkout\Rest\Response\GetPaymentStatusResponse
     */
    public function getPaymentStatus($purchaseId) : \Payment\Checkout\Rest\Response\GetPaymentStatusResponse;
}
