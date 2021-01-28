<?php

namespace Payment\Checkout\Model\Checkout\CheckoutSession;

interface AttributeSchema
{
    public const PURCHASE_ID = 'payment_purchase_id';
    public const JWT         = 'payment_jwt';
    public const EXPIRED_UTC = 'payment_expired_utc';
}
