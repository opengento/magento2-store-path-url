<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Service;

use Magento\Store\Api\Data\StoreInterface;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Model\Config\PathType;

use function explode;
use function implode;
use function strtolower;

class PathResolver
{
    public function __construct(private Config $config) {}

    public function resolve(StoreInterface $store): string
    {
        return strtolower(match ($this->config->getStorePathType()) {
            PathType::StoreCode => $store->getCode(),
            PathType::CountryCode => $this->config->getCountry($store),
            PathType::LocaleUnderscore => $this->config->getLocale($store),
            PathType::LocaleHyphen => implode('-', explode('_', $this->config->getLocale($store))),
            PathType::Custom => $this->config->getCustomPathMapper()[(int)$store->getId()] ?? $store->getCode(),
        });
    }
}
