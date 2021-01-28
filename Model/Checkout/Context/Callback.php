<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Checkout\Context;

use Payment\Checkout\Rest\Adapter\GetPaymentStatus;
use Payment\Checkout\Rest\Service\Authentication;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartManagementInterface;
use Payment\Checkout\Model\Quote\QuoteManagement;
use Magento\Sales\Api\OrderRepositoryInterface;

class Callback
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $pageFactory;

    /**
     * @var Authentication
     */
    private $authService;

    /**
     * @var GetPaymentStatus
     */
    private $paymentStatusService;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var QuoteManagement
     */
    private $quoteManager;

    /**
     * @var \Payment\Checkout\Model\Quote\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var CartManagementInterface
     */
    private $cartManager;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Payment\Checkout\Model\Payment\ResponseHandler
     */
    private $responseHandler;

    /**
     * @var \Payment\Checkout\Model\Service\PaymentProcessor
     */
    private $paymentProcessor;

    /**
     * Callback constructor.
     *
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param Authentication $authService
     * @param GetPaymentStatus $paymentStatusService
     * @param Session $checkoutSession
     * @param QuoteManagement $quoteManager
     * @param \Payment\Checkout\Model\Quote\QuoteRepository $quoteRepository
     * @param CartManagementInterface $cartManager
     * @param OrderRepositoryInterface $orderRepository
     * @param \Payment\Checkout\Model\Payment\ResponseHandler $responseHandler
     * @param \Payment\Checkout\Model\Service\PaymentProcessor $paymentProcessor
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        Authentication $authService,
        GetPaymentStatus $paymentStatusService,
        Session $checkoutSession,
        QuoteManagement $quoteManager,
        \Payment\Checkout\Model\Quote\QuoteRepository $quoteRepository,
        CartManagementInterface $cartManager,
        OrderRepositoryInterface $orderRepository,
        \Payment\Checkout\Model\Payment\ResponseHandler $responseHandler,
        \Payment\Checkout\Model\Service\PaymentProcessor $paymentProcessor
    ) {
        $this->pageFactory = $pageFactory;
        $this->authService = $authService;
        $this->paymentStatusService = $paymentStatusService;
        $this->checkoutSession = $checkoutSession;
        $this->quoteManager = $quoteManager;
        $this->quoteRepository = $quoteRepository;
        $this->cartManager = $cartManager;
        $this->orderRepository = $orderRepository;
        $this->responseHandler = $responseHandler;
        $this->paymentProcessor = $paymentProcessor;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getPageFactory(): \Magento\Framework\View\Result\PageFactory
    {
        return $this->pageFactory;
    }

    /**
     * @return Authentication
     */
    public function getAuthService(): Authentication
    {
        return $this->authService;
    }

    /**
     * @return GetPaymentStatus
     */
    public function getPaymentStatusService(): GetPaymentStatus
    {
        return $this->paymentStatusService;
    }

    /**
     * @return Session
     */
    public function getCheckoutSession(): Session
    {
        return $this->checkoutSession;
    }

    /**
     * @return QuoteManagement
     */
    public function getQuoteManager(): QuoteManagement
    {
        return $this->quoteManager;
    }

    /**
     * @return \Payment\Checkout\Model\Quote\QuoteRepository
     */
    public function getQuoteRepository(): \Payment\Checkout\Model\Quote\QuoteRepository
    {
        return $this->quoteRepository;
    }

    /**
     * @return CartManagementInterface
     */
    public function getCartManager(): CartManagementInterface
    {
        return $this->cartManager;
    }

    /**
     * @return OrderRepositoryInterface
     */
    public function getOrderRepository(): OrderRepositoryInterface
    {
        return $this->orderRepository;
    }

    /**
     * @return \Payment\Checkout\Model\Payment\ResponseHandler
     */
    public function getResponseHandler(): \Payment\Checkout\Model\Payment\ResponseHandler
    {
        return $this->responseHandler;
    }

    /**
     * @return \Payment\Checkout\Model\Service\PaymentProcessor
     */
    public function getPaymentProcessor(): \Payment\Checkout\Model\Service\PaymentProcessor
    {
        return $this->paymentProcessor;
    }
}
