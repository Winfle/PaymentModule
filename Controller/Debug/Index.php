<?php
namespace Payment\Checkout\Controller\Debug;

use Payment\Checkout\Model\Checkout\Context\Callback as CallbackContext;
use Payment\Checkout\Model\Payment\Payment;
use Payment\Checkout\Rest\Adapter\GetPaymentStatus;
use Payment\Checkout\Rest\Response\GetPaymentStatusResponse;
use Payment\Checkout\Rest\Service\Authentication;
use Magento\Checkout\Controller\Action;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Payment\Checkout\Controller\Callback
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var GetPaymentStatus
     */
    private $paymentStatusService;

    /**
     * @var Authentication
     */
    private $authService;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    private $cartManager;

    /**
     * @var \Payment\Checkout\Model\Quote\QuoteManagement
     */
    private $quoteManager;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Quote\Model\QuoteValidator
     */
    private $quoteValidator;

    /**
     * @var \Payment\Checkout\Model\Payment\Payment
     */
    private $payment;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param Callback $callbackContext
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        CallbackContext $callbackContext,
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        Payment $payment
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement
        );

        $this->pageFactory = $callbackContext->getPageFactory();
        $this->paymentStatusService = $callbackContext->getPaymentStatusService();
        $this->authService = $callbackContext->getAuthService();
        $this->checkoutSession = $callbackContext->getCheckoutSession();
        $this->cartManager = $callbackContext->getCartManager();
        $this->quoteManager = $callbackContext->getQuoteManager();
        $this->orderRepository = $callbackContext->getOrderRepository();
        $this->quoteValidator = $quoteValidator;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     * @throws \Payment\Checkout\Rest\Authentification\AdapterException
     * @throws \Payment\Checkout\Rest\Exception\AdapterException
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();
//        $this->quoteValidator->validateBeforeSubmit($this->checkoutSession->getQuote());
        $paymentStatus = $this->getPaymentStatus();
        echo '<pre>' . print_r($paymentStatus->getData(), true). '</pre>';
        exit;
    }

    /**
     *
     */
    private function payment()
    {
        print_r($this->payment);exit;
    }


    /**
     * @throws \Payment\Checkout\Rest\Authentification\AdapterException
     * @throws \Payment\Checkout\Rest\Exception\AdapterException
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    private function getPaymentStatus() : GetPaymentStatusResponse
    {
        $purchaseId = $this->getPurchaseId();
        $this->authService->authenticate($this->checkoutSession->getQuote()->getStoreId());
        $authToken = $this->authService->getToken();

        return $this->paymentStatusService->getStatus($purchaseId, $authToken);
    }

    /**
     * Get purchase ID
     *
     * @return string
     */
    private function getPurchaseId() : ?string
    {
        return $this->checkoutSession->getPaymentPurchaseId();
    }
}
