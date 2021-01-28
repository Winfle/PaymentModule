<?php declare(strict_types=1);

namespace Payment\Checkout\Rest\Adapter;

use Payment\Checkout\Model\Config\ApiConfig;
use Payment\Checkout\Rest\Exception\AdapterException;
use Payment\Checkout\Rest\Response\GetPaymentStatusResponse;
use Payment\Checkout\Rest\Response\InitializePaymentResponse;
use Payment\Checkout\Rest\RestClient;
use Payment\Checkout\Rest\Schema\Parser;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class GetPaymentStatus
{
    /**
     * @var ApiConfig
     */
    private $config;

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
     * GetPaymentStatus constructor.
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
        $this->config = $config;
        $this->logger = $logger;
        $this->schemaParser = $schemaParser;
    }

    /**
     * @param $purchaseId
     * @param $accessToken
     *
     * @return GetPaymentStatusResponse
     * @throws AdapterException
     */
    public function getStatus($purchaseId, $accessToken) : GetPaymentStatusResponse
    {
        if (!$accessToken) {
            throw new AdapterException('Missing access token');
        }

        $uri = sprintf(
            '%s/api/partner/payments/%s',
            $this->endpoint,
            $purchaseId
        );
        $headers = [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', $accessToken)
        ];

        try {
            $rawResponse = $this->restClient->get($uri, [], $headers);
            $paymentStatusResponse = $this->schemaParser->parse($rawResponse, GetPaymentStatusResponse::class);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $paymentStatusResponse;
    }
}
