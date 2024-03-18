<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Service;

use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;

use function str_starts_with;

class StorePathFixer
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
        private UriUtils $uriUtils
    ) {}

    /**
     * Replace the request uri path with the store code, if the base url match any of the registered store base url.
     */
    public function fix(string $baseUrl, string $requestUri): string
    {
        /** @var Store $store */
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() && str_starts_with($baseUrl, $store->getBaseUrl())) {
                return $this->uriUtils->replacePathCode($requestUri, $store);
            }
        }

        return $requestUri;
    }
}
