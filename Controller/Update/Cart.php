<?php
namespace Payment\Checkout\Controller\Update;

use Payment\Checkout\Model\Content\ResponseHandler;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Cart
 *
 * @package Payment\Checkout\Controller\Update
 */
class Cart extends \Magento\Checkout\Controller\Action
{
    use ResponseHandler;

    /**
     * @var \Payment\Checkout\Rest\Service\Authentication
     */
    private $authService;

    /**
     * @var \Payment\Checkout\Rest\Request\InitializePaymentRequestFactory
     */
    private $initializePaymentRequestFactory;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var \Payment\Checkout\Model\Checkout\PaymentCheckout
     */
    private $paymentCheckout;

    /**
     * Cart constructor.
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
        Session $checkoutSession,
        \Payment\Checkout\Model\Checkout\PaymentCheckout $paymentCheckout
    ) {
        parent::__construct($context, $customerSession, $customerRepository, $accountManagement);

        $this->checkoutSession = $checkoutSession;
        $this->paymentCheckout = $paymentCheckout;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Payment\Checkout\Rest\Exception\UpdateCartException
     */
    public function execute()
    {
        $checkoutSession = $this->checkoutSession;
        try {
            $this->paymentCheckout->updateItems($checkoutSession->getPaymentPurchaseId(), $checkoutSession->getQuote());
        } catch (\Exception $e) {
            echo $e->getMessage();exit;
            $this->getResponse()->setBody(json_encode([
                'messages' => __('Can not update cart.')
            ]));
            return;
        }

        $updateBlocks = [
            'cart',
            'coupon',
            'shipping',
            'messages',
            'payment'
        ];

        $this->handleResponse($updateBlocks, true);
    }
}
