<?php declare(strict_types=1);

namespace Payment\Checkout\Plugin\Block\Checkout;

use Payment\Checkout\Model\Config\Checkout;
use Magento\Framework\View\Element\Template;

class Url
{
    /**
     * @var Checkout
     */
    private $checkoutConfig;

    /**
     * Url constructor.
     */
    public function __construct(Checkout $checkoutConfig)
    {
        $this->checkoutConfig = $checkoutConfig;
    }

    /**
     * @param $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetCheckoutUrl(Template $subject, $checkoutUrl)
    {
        return $this->checkoutConfig->getCheckoutUrl();
    }
}
