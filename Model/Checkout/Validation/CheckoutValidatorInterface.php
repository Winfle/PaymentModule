<?php

namespace Payment\Checkout\Model\Checkout\Validation;

use Payment\Checkout\Model\Checkout\CheckoutException;

interface CheckoutValidatorInterface
{
    /**
     * @throws CheckoutException
     */
    public function validate() : void;
}
