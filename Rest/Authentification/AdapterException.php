<?php declare(strict_types=1);

namespace Payment\Checkout\Rest\Authentification;

class AdapterException extends \Exception
{
    /**
     * @param \Exception $cause
     *
     * @return \Payment\Checkout\Rest\Authentification\AdapterException
     */
    public static function create(\Exception $cause)
    {
        $message = 'API connection failed';

        return new static($message, $cause->getCode(), $cause);
    }
}
