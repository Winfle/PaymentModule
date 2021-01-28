<?php

namespace Payment\Checkout\Controller\Update;

use Payment\Checkout\Model\Content\ResponseHandler;
use Magento\Checkout\Controller\Action;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SaveComment extends Action
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
     * @var \Payment\Checkout\Rest\Service\UpdateCart
     */
    private $updateCartService;

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

    public function execute()
    {
        try {
            $comment = $this->getRequest()->getPost('payment_customer_comment', '');
            $quote = $this->getQuote();
            $quote->setCustomerNote($comment)->setCustomerNoteNotify(false);
            $quote->save();

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t update your comment.')
            );
        }

        $this->handleResponse(['comment']);
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
