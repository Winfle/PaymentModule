<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Quote\CustomerDataAssigner;

use Payment\Checkout\Model\Quote\CustomerDataAssignerInterface;
use Magento\Quote\Model\Quote;

class Customer implements CustomerDataAssignerInterface
{
    public function assignData(Quote $quote): void
    {
        $customer = $quote->getCustomer();
        $quote->setCheckoutMethod(CustomerDataAssignerInterface::TYPE_CUSTOMER)
            ->setCustomerId($customer->getId())
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerFirstname($customer->getFirstname())
            ->setCustomerLastname($customer->getLastname())
            ->setCustomerIsGuest(false);
    }
}
