<?php

namespace Payment\Checkout\Rest\Exception;

use Payment\Checkout\Rest\Webservice\Exception\HttpException;

class PaymentStatusException extends HttpException
{
    /**
     * @param \Exception $cause
     * @return PaymentStatusException
     */
    public static function create(\Exception $cause)
    {
        $message = 'Payment status fetching failed';

        return new static($message, $cause->getCode(), $cause);
    }
}
