<?php

namespace Payment\Checkout\Rest\Adapter;

use Payment\Checkout\Model\Config\ApiConfig;
use Payment\Checkout\Rest\Exception\AdapterException;
use Payment\Checkout\Rest\Exception\InitializePaymentException;
use Payment\Checkout\Rest\Request\InitializePaymentRequest;
use Payment\Checkout\Rest\Response\InitializePaymentResponse;
use Payment\Checkout\Rest\RestClient;
use Payment\Checkout\Rest\Schema\Parser;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class InitializePayment
 *
 * @package Payment\Checkout\Rest\Adapter
 */
class InitializePayment
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
     * @param Parser $schemaParser
     * @param LoggerInterface $logger
     */
    public function __construct(
        ApiConfig $config,
        RestClient $restClient,
        Parser $schemaParser,
        LoggerInterface $logger
    ) {
        $this->endpoint = $config->getAuthBackendUrl();
        $this->restClient = $restClient;
        $this->schemaParser = $schemaParser;
        $this->logger = $logger;
    }

    /**
     * @param \Payment\Checkout\Rest\Request\InitializePaymentRequest $request
     *
     * @param $accessToken
     *
     * @return mixed
     * @throws InitializePaymentException
     */
    public function initialize(InitializePaymentRequest $request, $accessToken) : InitializePaymentResponse
    {
        return new \Payment\Checkout\Rest\Response\InitializePaymentResponse(
            new DataObject([
                'purchaseId' => 'purchaseId',
                'jwt' => 'jwt',
                'expireUtc' => '123',

            ])
        );
        if (!$accessToken) {
            throw new InitializePaymentException('Missing access token');
        }

        $uri = sprintf('%s/api/partner/payments', $this->endpoint);
        $requestBody = $request->getRequestBody();

        $headers = [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', $accessToken)
        ];

        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));
        try {
            $rawResponse = $this->restClient->post($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            /** @var InitializePaymentResponse $initPaymentResponse */
            $initPaymentResponse = $this->schemaParser->parse($rawResponse, InitializePaymentResponse::class);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $initPaymentResponse;
    }
}
