<?php declare(strict_types=1);

namespace Payment\Checkout\Model\Checkout\OrderLine;

use Payment\Checkout\Model\Checkout\ApiBuilder\ApiBuilder;

class OrderItemCollector
{
    /**
     * @var array
     */
    private $collectors;

    /**
     * OrderLineCollector constructor.
     */
    public function __construct(array $collectors)
    {
        $this->collectors = $collectors;
    }

    /**
     * @param ApiBuilder $builder
     * @param $subject
     */
    public function collect(ApiBuilder $builder, $subject)
    {

    }
}
