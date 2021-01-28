<?php declare(strict_types=1);

namespace Payment\Checkout\Rest\Service;

use Payment\Checkout\Model\Config\ApiConfig;
use Payment\Checkout\Rest\Adapter\UpdateCart as UpdateCartAdapter;
use Payment\Checkout\Rest\Exception\UpdateCartException;

class UpdateCart
{
    /**
     * @var UpdateCartAdapter
     */
    private $updateCartAdapter;

    /**
     * UpdateCart constructor.
     *
     * @param ApiConfig $config
     * @param UpdateCartAdapter $updateCartAdapter
     */
    public function __construct(
        ApiConfig $config,
        UpdateCartAdapter $updateCartAdapter
    ) {
        $this->config = $config;
        $this->updateCartAdapter = $updateCartAdapter;
    }

    /**
     * @param array $items
     * @param $purchaseId
     * @param $accessToken
     *
     * @throws UpdateCartException
     */
    public function updateItems(array $items, $purchaseId, $accessToken) : void
    {
        $this->updateCartAdapter->updateItems($items, $purchaseId, $accessToken);
    }
}
