<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\App\Request;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\App\Request\StorePathInfoValidator as Subject;
use Magento\Store\Model\Store;
use Opengento\StorePathUrl\Model\Config;

use function parse_url;
use function strtok;

use const PHP_URL_PATH;

class StorePathInfoValidator
{
    public function __construct(
        private Config $config,
        private StoreRepositoryInterface $storeRepository
    ) {}

    public function beforeGetValidStoreCode(Subject $subject, Http $request, string $pathInfo = ''): array
    {
        if ($this->config->isBaseUrlResolverEnabled()) {
            $originalPathInfo = $pathInfo;
            $uri = strtok($request->getUriString(), '?') . '/';
            if ($uri !== false) {
                if ($pathInfo === '') {
                    $pathInfo = parse_url($uri, PHP_URL_PATH);
                    if ($pathInfo === false) {
                        return [$request, $originalPathInfo];
                    }
                    $pathInfo = strtok($pathInfo, '/');
                }
                $pathInfo = $pathInfo === false ? $this->resolveByWebUrl($uri) : $this->resolveByLinkUrl($uri);
                $pathInfo = $pathInfo === '' ? $originalPathInfo : $pathInfo;
            }
        }

        return [$request, $pathInfo];
    }

    private function resolveByLinkUrl(string $uri): string
    {
        /** @var Store $store */
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() && str_starts_with($uri, $store->getBaseUrl())) {
                return $store->getCode();
            }
        }

        return '';
    }

    private function resolveByWebUrl(string $uri): string
    {
        $matches = [];

        /** @var Store $store */
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() && str_starts_with($uri, $store->getBaseUrl(UrlInterface::URL_TYPE_WEB))) {
                try {
                    $website = $store->getWebsite();
                    if ($website->getIsDefault()) {
                        if ($store->isDefault()) {
                            return $store->getCode();
                        }
                        $matches[0] = $store->getCode();
                    } elseif ($store->isDefault()) {
                        $matches[1] = $store->getCode();
                    } else {
                        $matches[2] = $store->getCode();
                    }
                } catch (NoSuchEntityException) {}
            }
        }

        return $matches[0] ?? $matches[1] ?? $matches[2] ?? '';
    }
}
