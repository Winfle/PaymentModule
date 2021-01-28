<?php

namespace Payment\Checkout\Controller\Index;

use Payment\Checkout\Block\Checkout\CheckoutWidget;
use Payment\Checkout\Model\Checkout\PaymentCheckout;
use Payment\Checkout\Model\Checkout\CheckoutException;
use Magento\Checkout\Controller\Action;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var PaymentCheckout
     */
    private $checkout;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param PaymentCheckout $checkout
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        PaymentCheckout $checkout
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement
        );

        $this->checkout = $checkout;
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        try {
            $paymentData = $this->checkout->initCheckout();
        } catch (CheckoutException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('checkout/cart');
            return;
        }

        return $this->getCheckoutLayout(
            $paymentData->getJwt(),
            $paymentData->getPurchaseId()
        );
    }

    /**
     * @param $jwtToken
     * @param $purchaseId
     *
     * @return \Magento\Framework\View\Result\Page
     */
    private function getCheckoutLayout($jwtToken, $purchaseId)
    {
        $checkoutLayout = $this->createLayoutPage();

        /** @var $paymentCheckoutBlock CheckoutWidget */
        $paymentCheckoutBlock = $checkoutLayout->getLayout()->getBlock('checkout.widget');
        $paymentCheckoutBlock
            ->setJwtToken($jwtToken)
            ->setPurchaseId($purchaseId);

        return $checkoutLayout;
    }

    /**
     * Plugin extension point for extending the layout
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function createLayoutPage()
    {
        return $this->pageFactory->create();
    }
}
