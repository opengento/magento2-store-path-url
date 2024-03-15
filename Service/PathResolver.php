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

use function explode;
use function implode;
use function strtolower;

class PathResolver
{
    public function __construct(private Config $config) {}

    public function resolve(ScopeInterface|StoreInterface $scope): string
    {
        return strtolower(match ($this->config->getStorePathType()) {
            PathType::StoreCode => $scope->getCode(),
            PathType::CountryCode => $this->config->getCountry($scope),
            PathType::LocaleUnderscore => $this->config->getLocale($scope),
            PathType::LocaleHyphen => implode('-', explode('_', $this->config->getLocale($scope))),
            PathType::Custom => $this->config->getCustomPathMapper()[(int)$scope->getId()] ?? $scope->getCode(),
        });
    }
}
