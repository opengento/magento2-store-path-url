<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Service;

use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;

use function parse_url;
use function str_starts_with;

use const PHP_URL_PATH;

class StorePathFixer
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
        private UriUtils $uriUtils
    ) {}

    public function fix(string $baseUrl, string $requestUri): string
    {
        /** @var Store $store */
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId()) {
                if ($baseUrl === '') {
                    $path = parse_url($store->getBaseUrl(), PHP_URL_PATH);
                    if (str_starts_with($requestUri . '/', $path)) {
                        return $this->uriUtils->replacePathCode($requestUri, $store);
                    }
                } elseif (str_starts_with($baseUrl . $requestUri, $store->getBaseUrl())) {
                    return $this->uriUtils->replacePathCode($requestUri, $store);
                }
            }
        }

        return $requestUri;
    }
}
