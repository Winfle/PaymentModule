<?php declare(strict_types=1);

namespace Payment\Checkout\Rest\Authentification\Cache;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class AuthentificationCache extends TagScope
{
    /**
     * @var string
     */
    const TYPE_IDENTIFIER = 'payment_cache_authentifiaction';

    /**
     * @var string
     */
    const CACHE_TAG = 'PAYMENT_CACHE_AUTHENTIFICATION';

    /**
     * @var FrontendPool
     */
    private $cacheFrontendPool;

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct(
            $cacheFrontendPool->get(self::TYPE_IDENTIFIER),
            self::CACHE_TAG
        );
        $this->cacheFrontendPool = $cacheFrontendPool;
    }
}
