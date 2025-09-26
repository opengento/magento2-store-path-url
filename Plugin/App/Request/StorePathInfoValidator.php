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
    // This hack is necessary to prevent infinite loop because of https://github.com/opengento/magento2-store-path-url/issues/3
    // The fix is shipped in the version 2.4.9 of Magento: https://github.com/magento/magento2/pull/38717
    private int $stack = 0;

    public function __construct(
        private Config $config,
        private StoreRepositoryInterface $storeRepository
    ) {}

    public function beforeGetValidStoreCode(Subject $subject, Http $request, string $pathInfo = ''): array
    {
        // If $pathInfo is empty, we are resolving the current scope ID.
        // It could also be the path info processor which tries to trim the store path from the url.
        if (++$this->stack === 1 && $pathInfo === '' && $this->config->isStoreInPath() && $this->config->isBaseUrlResolverEnabled()) {
            $pathInfo = $this->resolveStoreCode($request);
        }

        return [$request, $pathInfo];
    }

    public function afterGetValidStoreCode(Subject $subject, ?string $store, Http $request, string $path = ''): ?string
    {
        $this->stack--;

        return $store;
    }

    private function resolveStoreCode(Http $request): string
    {
        $uri = strtok($request->getUriString(), '?') . '/';
        if ($uri !== false) {
            $pathInfo = parse_url($uri, PHP_URL_PATH);
            if ($pathInfo === false) {
                return '';
            }
            // The uri has a valid format, we can look for the matching store base url.
            $pathInfo = $this->resolveByLinkUrl($uri);
            // If the store cannot be resolved, with look for the closest lookalike store.
            return $pathInfo !== false ? $pathInfo : $this->resolveByWebUrl($uri);
        }

        return '';
    }

    private function resolveByLinkUrl(string $uri): bool|string
    {
        $storeCode = false;
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
                    if ($storeMatch === null) {
                        $storeMatch = $store->getCode();
                    }
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
