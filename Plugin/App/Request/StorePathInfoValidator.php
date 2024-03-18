<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\App\Request;

use Magento\Framework\App\Request\Http;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\App\Request\StorePathInfoValidator as Subject;
use Magento\Store\Model\Store;
use Opengento\StorePathUrl\Model\Config;

class StorePathInfoValidator
{
    public function __construct(
        private Config $config,
        private StoreRepositoryInterface $storeRepository
    ) {}

    public function beforeGetValidStoreCode(Subject $subject, Http $request, string $pathInfo = ''): array
    {
        if ($this->config->isEnabled()) {
            $uri = $request->getUriString();
            /** @var Store $store */
            foreach ($this->storeRepository->getList() as $store) {
                if ($store->getId() && str_starts_with($uri, $store->getBaseUrl())) {
                    $pathInfo = $store->getCode();
                }
            }
        }

        return [$request, $pathInfo];
    }
}
