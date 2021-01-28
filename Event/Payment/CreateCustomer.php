<?php declare(strict_types=1);

namespace Payment\Checkout\Event\Payment;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class OrderSaveAfter
 *
 * @package Payment\Checkout\Event\Payment
 */
class CreateCustomer implements ObserverInterface
{
    /**
     * @var \Payment\Checkout\Model\Config\CheckoutSetup
     */
    private $checkoutConfig;

    /**
     * @var \Magento\Sales\Api\OrderCustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    private $customerRepository;

    /**
     * OrderSaveAfter constructor.
     */
    public function __construct(
       \Payment\Checkout\Model\Config\CheckoutSetup $checkoutConfig,
       \Magento\Sales\Api\OrderCustomerManagementInterface $customerManagement,
       \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository

    ) {
        $this->checkoutConfig = $checkoutConfig;
        $this->customerManagement = $customerManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($order->getPayment()->getMethod() !== \Payment\Checkout\Model\Payment\Payment::CODE ||
            ! $this->checkoutConfig->getRegisterOnCheckout()
        ) {
            return;
        }

        try {
            $newCustomer = $this->customerManagement->create($order->getId());
            $newCustomer->setCustomAttribute('payment_customer_token', $quote->getPaymentCustomerToken());
            $this->customerRepository->save($newCustomer);
        } catch (\Exception $e) {}
    }
}
