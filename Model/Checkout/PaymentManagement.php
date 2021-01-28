<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Checkout;

use Payment\Checkout\Model\Quote\SignatureHasher;
use Payment\Checkout\Rest\Service\InitializePayment;
use Magento\Quote\Model\Quote;

class PaymentManagement
{
    /**
     * @var \Payment\Checkout\Rest\Service\AuthentificationInterface
     */
    private $authService;

    /**
     * @var InitializePayment
     */
    private $initPaymentService;

    /**
     * @var SignatureHasher
     */
    private $hasher;

    /**
     * CheckoutManagement constructor.
     */
    public function __construct(
        \Payment\Checkout\Rest\Service\AuthentificationInterface $authService,
        \Payment\Checkout\Rest\Service\InitializePayment $initPaymentService,
        SignatureHasher $hasher
    ) {
        $this->authService = $authService;
        $this->initPaymentService = $initPaymentService;
        $this->hasher = $hasher;
    }

    /**
     * @param Quote $quote
     *app/code/Payment/Checkout/Model/Checkout/PaymentManagement.php:41
     * @return \Payment\Checkout\Rest\Response\InitializePaymentResponse
     * @throws \Payment\Checkout\Rest\Authentification\AdapterException
     * @throws \Payment\Checkout\Rest\Exception\InitializePaymentException
     */
    public function initNewPayment(Quote $quote) : \Payment\Checkout\Rest\Response\InitializePaymentResponse
    {
        $websiteId = $quote->getStore()->getWebsiteId();

        // Authentificate website & receive access token
        $this->authService->authenticate($websiteId);
        $accessToken = $this->authService->getToken();

        $initPayment = $this->initPaymentService->initPayment($quote, $accessToken);
        $quote->setPaymentPurchaseId($initPayment->getPurchaseId());
        $quote->setPaymentQuoteSignature($this->hasher->getQuoteSignature($quote));

        // Instantiate Checkout and get purchase id & JWT
        return $initPayment;
    }
}
