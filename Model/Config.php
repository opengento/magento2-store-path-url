<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Model;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Opengento\StorePathUrl\Model\Config\PathType;

class Config
{
    private const CONFIG_PATH_STORE_PATH_URL = 'web/url/store_path_url';
    private const CONFIG_PATH_CUSTOM_PATH_MAPPER = 'web/url/custom_path_mapper';
    private const CONFIG_PATH_UNSET_SINGLE_STORE_PATH = 'web/url/unset_single_store_path';

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
        return PathType::from($this->scopeConfig->getValue(self::CONFIG_PATH_STORE_PATH_URL));
    }

    public function isUnsetSingleStorePath(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_UNSET_SINGLE_STORE_PATH);
    }

    public function getCountry(StoreInterface $store): string
    {
        return (string)$this->scopeConfig->getValue(
            Data::XML_PATH_DEFAULT_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $store->getId()
        );
    }

    public function getLocale(StoreInterface $store): string
    {
        return (string)$this->scopeConfig->getValue(
            Data::XML_PATH_DEFAULT_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store->getId()
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
