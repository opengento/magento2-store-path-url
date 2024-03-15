<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Model;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeInterface as AppScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Opengento\StorePathUrl\Model\Config\PathType;

class Config
{
    private const CONFIG_PATH_USE_STORE_PATH = 'web/url/use_store_path';
    private const CONFIG_PATH_CUSTOM_PATH_MAPPER = 'web/url/custom_path_mapper';

    private ?array $customPathMapper = null;

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private SerializerInterface $serializer
    ) {}

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(Store::XML_PATH_STORE_IN_URL)
            && $this->getStorePathType() !== PathType::StoreCode;
    }

    public function getStorePathType(): PathType
    {
        return PathType::from($this->scopeConfig->getValue(self::CONFIG_PATH_USE_STORE_PATH));
    }

    public function getCountry(AppScopeInterface|StoreInterface $scope): string
    {
        return (string)$this->scopeConfig->getValue(
            Data::XML_PATH_DEFAULT_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $scope->getId()
        );
    }

    public function getLocale(AppScopeInterface|StoreInterface $scope): string
    {
        return (string)$this->scopeConfig->getValue(
            Data::XML_PATH_DEFAULT_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $scope->getId()
        );
    }

    public function getCustomPathMapper(): array
    {
        return $this->customPathMapper ??= $this->resolveCustomPathMapper();
    }

    private function resolveCustomPathMapper(): array
    {
        $customPaths = $this->serializer->unserialize(
            $this->scopeConfig->getValue(self::CONFIG_PATH_CUSTOM_PATH_MAPPER) ?? '{}'
        );

        $mapper = [];
        foreach ($customPaths as $customPath) {
            if (isset($customPath['store'], $customPath['path'])) {
                $mapper[(int)$customPath['store']] = (string)$customPath['path'];
            }
        }

        return $mapper;
    }
}
