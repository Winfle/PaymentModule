<?php

namespace Payment\Checkout\Rest\Adapter;

use Payment\Checkout\Model\Config\ApiConfig;
use Payment\Checkout\Rest\Exception\AdapterException;
use Payment\Checkout\Rest\Exception\UpdateCartException;
use Payment\Checkout\Rest\Response\InitializePaymentResponse;
use Payment\Checkout\Rest\RestClient;
use Payment\Checkout\Rest\Schema\Parser;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class InitializePayment
 *
 * @package Payment\Checkout\Rest\Adapter
 */
class UpdateCart
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var RestClient
     */
    private $restClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Parser
     */
    private $schemaParser;

    /**
     * AuthAdapter constructor.
     *
     * @param ApiConfig $config
     * @param RestClient $restClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        ApiConfig $config,
        RestClient $restClient,
        LoggerInterface $logger
    ) {
        $this->endpoint = $config->getAuthBackendUrl();
        $this->restClient = $restClient;
        $this->logger = $logger;
    }

    /**
     * @param array $items
     * @param $purchaseId
     *
     * @param $authToken
     *
     * @throws UpdateCartException
     */
    public function updateItems(array $items, $purchaseId, $authToken) : void
    {
        return;
        if (!$purchaseId) {
            throw new UpdateCartException('Missing purchase id');
        }

        $uri = sprintf(
            '%s/api/partner/payments/%s/items',
            $this->endpoint,
            $purchaseId
        );

        $requestBody = \json_encode([
            'items' => $items
        ]);

        $headers = [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', $authToken)
        ];

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));
        try {
            $rawResponse = $this->restClient->put($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }
    }
}
