<?php declare(strict_types=1);

namespace Payment\Checkout\Block\Adminhtml\Payment\Checkout;


class Info extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'Payment_Checkout::payment/checkout/info.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Payment_Checkout::payment/checkout/pdf.phtml');
        return $this->toHtml();
    }

    /**
     * @return mixed|string
     */
    public function getPaymentPaymentMethod()
    {
        try {
            return $this->getInfo()->getAdditionalInformation('payment_method');
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * @return mixed|string
     */
    public function getPaymentPurchaseId()
    {
        try {
            return $this->getInfo()->getAdditionalInformation('payment_purchase_id');
        } catch (\Exception $e) {
            return "";
        }
    }
}
