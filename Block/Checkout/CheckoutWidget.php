<?php

namespace Payment\Checkout\Block\Checkout;

use Magento\Framework\View\Element\Template;

/**
 * @method getPurchaseId()
 * @method setJwtToken(string $getJwt)
 * @method setPurchaseId(string $purchaseId)
 */
class CheckoutWidget extends Template
{
    /**
     * @var \Payment\Checkout\Model\Config\CheckoutSetup
     */
    private $checkoutSetupConfig;

    /**
     * Sidebar constructor.
     *
     * @param Template\Context $context
     * @param \Payment\Checkout\Model\Config\CheckoutSetup $checkoutSetupConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Payment\Checkout\Model\Config\CheckoutSetup $checkoutSetupConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSetupConfig = $checkoutSetupConfig;
    }

    /**
     * @return string
     */
    public function getCheckoutCallbackUrl()
    {
        return $this->checkoutSetupConfig->getCallbackUrl();
    }

}
