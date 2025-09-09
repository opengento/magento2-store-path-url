<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\App\Request;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
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
    private int $stack = 0;

    public function __construct(
        private Config $config,
        private StoreRepositoryInterface $storeRepository
    ) {}

    public function beforeGetValidStoreCode(Subject $subject, Http $request, string $pathInfo = ''): array
    {
        if (++$this->stack === 1 && $this->config->isStoreInPath() && $this->config->isBaseUrlResolverEnabled()) {
            $storeCode = $this->resolveStoreCode($request, $pathInfo);
            $pathInfo = $storeCode === '' ? $pathInfo : $storeCode;
        }

        return [$request, $pathInfo];
    }
    
    public function afterGetValidStoreCode(Subject $subject, ?string $store, Http $request, string $path = ''): ?string
    {
        if ($this->stack === 1 && !$this->config->isStoreInPath() && $this->config->isBaseUrlResolverEnabled()) {
            try {
                $store = $this->storeRepository->getActiveStoreByCode(
                    $this->resolveStoreCode($request, $path)
                )->getCode();
            } catch (LocalizedException) {
                $store = null;
            }
        }
        $this->stack--;

        return $store;
    }

    private function resolveStoreCode(Http $request, string $pathInfo): string
    {
        $uri = strtok($request->getUriString(), '?') . '/';
        if ($uri !== false) {
            $pathInfo = $pathInfo ?: parse_url($uri, PHP_URL_PATH);
            if ($pathInfo === false) {
                return '';
            }
            $pathInfo = $this->resolveByLinkUrl($uri) ?: $this->resolveByWebUrl($uri);
        }

        return $pathInfo;
    }

    private function resolveByLinkUrl(string $uri): string
    {
        $storeCode = '';
        /** @var Store $store */
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId()) {
                $storeBaseUrl = $store->getBaseUrl();
                if (str_starts_with($uri, $storeBaseUrl)) {
                    $path = trim((string)parse_url($storeBaseUrl, PHP_URL_PATH), '/');
                    $storeCode = $store->getCode();
                    if ($path !== '') {
                        return $storeCode;
                    }
                }
            }
        }

        return $storeCode;
    }

    private function resolveByWebUrl(string $uri): string
    {
        $storeMatch = null;
        $highestScore = 0;

        /** @var Store $store */
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() && str_starts_with($uri, $store->getBaseUrl(UrlInterface::URL_TYPE_WEB))) {
                try {
                    $score = $this->calculatePreferenceScore($store);
                    $storeMatch ??= $store->getCode();
                    if ($highestScore < $score) {
                        $highestScore = $score;
                        $storeMatch = $store->getCode();
                    }
                } catch (NoSuchEntityException) {}
            }
        }

        return $storeMatch ?? '';
    }

    /**
     * @throws NoSuchEntityException
     */
    private function calculatePreferenceScore(Store $store): int
    {
        $website = $store->getWebsite();
        // Bonus point for the stores which are part of one of the groups from the default website.
        $score = $website->getIsDefault() ? 2 : 0;
        // Extra point for the stores which are part of the default group of its website.
        $score += (int)$website->getDefaultGroup()->getDefaultStoreId() === (int)$store->getId() ? 1 : 0;
        // Extra point is the store is the default one of its group.
        $score += $store->isDefault() ? 1 : 0;

        return $score;
    }
}
