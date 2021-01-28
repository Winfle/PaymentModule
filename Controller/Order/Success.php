<?php
namespace Payment\Checkout\Controller\Order;

use Payment\Checkout\Rest\Exception\PaymentStatusException;
use Magento\Checkout\Controller\Action;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Success extends Action
{
    const BLOCK_NAME_SUCCESS = 'payment_checkout_success';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var \Payment\Checkout\Model\Payment\PaymentManager
     */
    private $paymentManager;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Payment\Checkout\Model\Payment\PaymentManager $paymentManager,
        Session $checkoutSession
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement
        );
        $this->pageFactory = $pageFactory;
        $this->paymentManager = $paymentManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $this->setPaymentLayoutData($resultPage);

        return $resultPage;
    }

    /**
     * @param Page $page
     */
    private function setPaymentLayoutData(\Magento\Framework\View\Result\Page $page)
    {
        $layout = $page->getLayout();

        /** @var \Payment\Checkout\Block\Checkout\Order\Success $successBlock */
        $successBlock = $layout->getBlock(self::BLOCK_NAME_SUCCESS);
        if (! $successBlock) {
            return;
        }

        try {
            $paymentResponse = $this->getPaymentResponse();
            $successBlock->setPaymentResponse($paymentResponse);
        } catch (PaymentStatusException $e) {
            // TODO: Log excepttion
        }
    }

    /**
     * @return \Payment\Checkout\Rest\Response\GetPaymentStatusResponse
     * @throws \Payment\Checkout\Rest\Exception\PaymentStatusException
     */
    private function getPaymentResponse()
    {
        $purchaseId = $this->checkoutSession->getPaymentPurchaseId();

        return $this->paymentManager->getPaymentStatus($purchaseId);
    }
}
