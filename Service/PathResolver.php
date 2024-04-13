<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Service;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Model\Config\PathType;

use function str_replace;
use function strtok;
use function strtolower;

class PathResolver
{
    public function __construct(private Config $config) {}

    public function resolve(StoreInterface $store): string
    {
        if ($store instanceof Store && $this->isSingleStore($store) && $this->config->isUnsetSingleStorePath()) {
            return '';
        }

        return strtolower(match ($this->config->getStorePathType()) {
            PathType::StoreCode => $store->getCode(),
            PathType::CountryCode => $this->config->getCountry($store),
            PathType::LanguageCode => strtok($this->config->getLocale($store), '_'),
            PathType::LocaleUnderscore => $this->config->getLocale($store),
            PathType::LocaleHyphen => str_replace('_', '-', $this->config->getLocale($store)),
            PathType::Custom => $this->config->getCustomPathMapper()[(int)$store->getId()] ?? '',
        });
    }

    private function isSingleStore(Store $store): bool
    {
        try {
            return $store->getWebsiteId() && $store->getWebsite()->getStoresCount() === 1;
        } catch (NoSuchEntityException) {
            return true;
        }
    }
}
