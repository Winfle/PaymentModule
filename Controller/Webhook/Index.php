<?php
namespace Payment\Checkout\Controller\Webhook;

use Payment\Checkout\Model\Checkout\Context\Callback as CallbackContext;
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
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

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
     * @var \Payment\Checkout\Model\Payment\ResponseHandler
     */
    private $quoteResponseHandler;

    /**
     * @var \Payment\Checkout\Model\Service\PaymentProcessor
     */
    private $paymentProcessor;

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
        CallbackContext $callbackContext
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
        $this->quoteResponseHandler = $callbackContext->getResponseHandler();
        $this->paymentProcessor = $callbackContext->getPaymentProcessor();
    }

    public function getTestOrder()
    {
        $orderCollection = \Magento\Framework\App\ObjectManager::getInstance()->get(CollectionFactory::class)->create();
        return $orderCollection
            ->addFieldToFilter('entity_id', ['eq' => 73])
            ->getFirstItem();
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     * @throws \Payment\Checkout\Rest\Authentification\AdapterException
     * @throws \Payment\Checkout\Rest\Exception\AdapterException
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute()
    {
        try {
            $quote = $this->initQuote();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('We can not instantiate your payment request. Please try again.');
            return $this->_redirect('payment');
        }

        $paymentStatus = $this->getPaymentStatus();
        if (!$this->isPaymentSuccessful($paymentStatus)) {
            $this->messageManager->addErrorMessage('Please verify your payment data.');
            return $this->_redirect('payment');
        }

        try {
            $this->quoteManager->setDataFromResponse($quote, $paymentStatus);
            $this->prepareShippingRates($quote);
            $this->quoteResponseHandler->handlePaymentStatus($quote, $paymentStatus);

            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->cartManager->submit($quote);
            $this->checkoutSession
                ->setLastQuoteId($quote->getId())
                ->setLastSuccessQuoteId($quote->getId())
                ->clearHelperData();

            $this->paymentProcessor->processPayment($order->getPayment());
            $this->dispatchPostEvents($order, $quote);

            /**
             * a flag to set that there will be redirect to third party after confirmation
             */
            $redirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl();

            $this->checkoutSession
                ->setLastOrderId($order->getId())
                ->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId())
                ->setLastOrderStatus($order->getStatus())
                ->unsPaymentPurchaseId();

            return $this->_redirect('payment/order/success');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Can not instantiate your payment request. Please try again.');
            return $this->_redirect('payment');
        }
    }

    /**
     * @param GetPaymentStatusResponse $paymentStatusResponse
     *
     * @return bool
     */
    private function isPaymentSuccessful(GetPaymentStatusResponse $paymentStatusResponse)
    {
        $statusData = $paymentStatusResponse->getPaymentData();

        return ($statusData['step']['current'] ?? false) === 'Completed';
    }

    /**
     * @param Quote $quote
     *
     * @return bool|string|void
     * @throws \Exception
     */
    private function prepareShippingRates(Quote $quote)
    {
        if ($quote->isVirtual()) {
            return;
        }

        // This is needed by shipping method with minimum amount
        $quote->collectTotals();

        $shipping = $quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates();
        $allRates = $shipping->getAllShippingRates();

        if (!count($allRates)) {
            return false;
        }

        $rates = [];
        foreach ($allRates as $rate) {
            /** @var $rate Quote\Address\Rate  **/
            $rates[$rate->getCode()] = $rate->getCode();
        }

        // Check if selected shipping method exists
        $method = $shipping->getShippingMethod();
        if ($method && isset($rates[$method])) {
            return $method;
        }

        // Check if default shipping method exists, use it then!
        $method = 'shipping';
        if ($method && isset($rates[$method])) {
            $shipping->setShippingMethod($method);
            return $method;
        }

        // Fallback, use first shipping method found
        $rate = $allRates[0];
        $method = $rate->getCode();
        $shipping->setShippingMethod($method);
        $shipping->save();
    }

    /**
     * @throws \Payment\Checkout\Rest\Authentification\AdapterException
     * @throws \Payment\Checkout\Rest\Exception\AdapterException
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    private function getPaymentStatus() : GetPaymentStatusResponse
    {
        $purchaseId = $this->getPurchaseId();
        $this->authService->authenticate();
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

    /**
     * Dispatch post events
     *
     * @param $order
     * @param $quote
     */
    private function dispatchPostEvents($order, $quote)
    {
        $this->_eventManager->dispatch(
            'checkout_type_onepage_save_order_after',
            ['order' => $order, 'quote' => $quote]
        );

        $this->_eventManager->dispatch(
            'payment_checkout_complete',
            ['order' => $order, 'quote' => $quote]
        );
    }

    /**
     * @return Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function initQuote()
    {
        $quote = $this->getQuote();
        if (! $quote->getPaymentPurchaseId()) {
            $quote->setPaymentPurchaseId($this->getPurchaseId());
            $quote->getExtensionAttributes()->setPaymentPurchaseId($this->getPurchaseId());
        }

        return $quote;
    }

    /**
     * @return Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getQuote() : Quote
    {
        return $this->checkoutSession->getQuote();
    }
}
