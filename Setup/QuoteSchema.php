<?php

namespace Payment\Checkout\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

interface QuoteSchema
{
    const TABLE = 'quote';
    const PURCHASE_ID = 'payment_purchase_id';
    const QUOTE_SIGNATURE = 'payment_quote_signature';
}
