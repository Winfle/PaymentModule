<?php

namespace Payment\Checkout\Rest\Service;

use Payment\Checkout\Rest\Authentification\AdapterException;

interface AuthentificationInterface
{
    const SESSION_THRESHOLD = 3600;

    const DATA_KEY_SESSION_TOKEN = 'payment_api_token';
    const DATA_KEY_SESSION_TOKEN_EXPIRY = 'payment_api_token_expiry';

    /**
     * @param null $websiteId
     *
     * @return void
     * @throws AdapterException
     */
    public function authenticate($websiteId = null) : void;

    /**
     * Read Session Token.
     *
     * @return string
     * @throws AdapterException
     */
    public function getToken() : string;

    /**
     * Read Session Token Expiry Date Time.
     *
     * @return string
     * @throws AdapterException
     */
    public function getTokenExpiry() : string;
}
