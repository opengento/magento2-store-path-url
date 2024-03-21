<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Service;

use Magento\Framework\App\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Model\Config\PathType;

use function str_replace;
use function strtok;
use function strtolower;

class PathResolver
{
    public function __construct(private Config $config) {}

    public function resolve(ScopeInterface|StoreInterface $scope): string
    {
        return strtolower(match ($this->config->getStorePathType()) {
            PathType::StoreCode => $scope->getCode(),
            PathType::CountryCode => $this->config->getCountry($scope),
            PathType::LanguageCode => strtok($this->config->getLocale($scope), '_'),
            PathType::LocaleUnderscore => $this->config->getLocale($scope),
            PathType::LocaleHyphen => str_replace('_', '-', $this->config->getLocale($scope)),
            PathType::Custom => $this->config->getCustomPathMapper()[(int)$scope->getId()] ?? $scope->getCode(),
        });
    }
}
