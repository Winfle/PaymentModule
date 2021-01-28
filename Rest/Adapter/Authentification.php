<?php

namespace Payment\Checkout\Rest\Adapter;

use Payment\Checkout\Model\Config\ApiConfig;
use Payment\Checkout\Rest\Exception\AdapterException;
use Payment\Checkout\Rest\Request\AuthRequest;
use Payment\Checkout\Rest\Response\AuthentificationResponse;
use Payment\Checkout\Rest\RestClient;
use Payment\Checkout\Rest\Schema\Parser;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Authentification
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
     * @var ApiConfig
     */
    private $config;

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
        $this->logger = $logger;
        $this->config = $config;
        $this->schemaParser = $schemaParser;
    }

    /**
     * @param AuthRequest $request
     *
     * @return AuthentificationResponse
     */
    public function startSession(AuthRequest $request) : AuthentificationResponse
    {
        return new \Payment\Checkout\Rest\Response\AuthentificationResponse(new DataObject([
            'tokenExpirationUtc' => '123',
            'token' => 'TOKEN',
        ]));

        $uri = sprintf('%s/api/partner/tokens', $this->endpoint);
        $requestBody = $request->getRequestBody();

        $headers = [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/json',
        ];


        $this->logger->log(LogLevel::DEBUG, sprintf("%s\n%s", $uri, $requestBody));
        try {
            $rawResponse = $this->restClient->post($uri, $requestBody, $headers);
            $this->logger->log(LogLevel::DEBUG, $rawResponse);

            $response = $this->schemaParser->parse($rawResponse, AuthentificationResponse::class);
        } catch (\Exception $e) {
            throw AdapterException::create($e);
        }

        return $response;
    }
}
