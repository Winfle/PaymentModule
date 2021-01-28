<?php

namespace Payment\Checkout\Controller\Update;

use Payment\Checkout\Model\Content\ResponseHandler;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SaveShippingMethod extends \Magento\Checkout\Controller\Action
{
    use ResponseHandler;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var
     */
    private $quote;

    /**
     * @var \Payment\Checkout\Model\Service\QuoteManagement
     */
    private $paymentQuoteManagement;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param Session $checkoutSession
     * @param \Payment\Checkout\Model\Service\QuoteManagement $paymentQuoteManagement
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        Session $checkoutSession,
        \Payment\Checkout\Model\Service\QuoteManagement $paymentQuoteManagement
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement
        );
        $this->checkoutSession = $checkoutSession;
        $this->paymentQuoteManagement = $paymentQuoteManagement;
    }

    /**
     * Save shipping method action
     */
    public function execute()
    {
        if (! $this->ajaxRequestAllowed()) {
            return;
        }

        $shippingMethod = $this->getRequest()->getPost('shipping_method', '');
        $postcode = $this->getRequest()->getPost('postcode', '');
        if (!$shippingMethod) {
            $this->getResponse()->setBody(json_encode([
                'messages' => 'Please choose a valid shipping method.'
            ]));
            return;
        }

        try {
            $quote = $this->getQuote();
            $purchaseId = $this->getPaymentPurchaseId();
            $this->updateShippingMethod($shippingMethod, $postcode);
            $this->paymentQuoteManagement->refresh($purchaseId, $quote);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t update shipping method.')
            );
        }

        $this->handleResponse(['cart', 'coupon', 'messages', 'payment', 'newsletter']);
    }

    /**
     * @param $methodCode
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateShippingMethod($methodCode, $postcode = null)
    {
        $quote = $this->getQuote();

        if ($quote->isVirtual()) {
            return;
        }

        $shippingAddress = $quote->getShippingAddress();
        if ($methodCode != $shippingAddress->getShippingMethod() ||
            $shippingAddress->getPostcode() != $postcode ||
            $methodCode == 'nwtunifaun_udc') {
            if ($postcode) {
                $shippingAddress->setPostcode($postcode);
            }

            $this->ignoreAddressValidation();
            $shippingAddress->setShippingMethod($methodCode)->setCollectShippingRates(true);
            $quote->setTotalsCollectedFlag(false)
                  ->collectTotals()
                  ->save();
        }
    }

    /**
     * Make sure addresses will be saved without validation errors
     *
     * @return void
     */
    private function ignoreAddressValidation()
    {
        $quote = $this->getQuote();
        $quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$quote->getIsVirtual()) {
            $quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * @return mixed
     */
    private function getPaymentPurchaseId()
    {
        return $this->checkoutSession->getPaymentPurchaseId();
    }

    /**
     * Quote object getter
     *
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote()
    {
        if ($this->quote === null) {
            return $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }
}
