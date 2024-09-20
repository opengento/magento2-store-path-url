<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\Url;

use Magento\Framework\Url\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Service\UriUtils;

class Scope
{
    private array $cache = [];

    public function __construct(
        private Config $config,
        private UriUtils $uriUtils
    ) {}

    public function afterGetBaseUrl(
        ScopeInterface $scope,
        string $baseUrl,
        string $type = UrlInterface::URL_TYPE_LINK,
        ?bool $secure = null
    ): string {
        return $this->cache[$scope->getId()][$type][(int)$secure] ??= $this->process($scope, $baseUrl, $type, $secure);
    }

    private function process(
        ScopeInterface $subject,
        string $baseUrl,
        string $type = UrlInterface::URL_TYPE_LINK,
        ?bool $secure = null
    ) {
        return $type === UrlInterface::URL_TYPE_LINK && $subject instanceof StoreInterface && $this->config->isEnabled()
            ? $this->uriUtils->replaceScopeCode($baseUrl, $subject)
            : $baseUrl;
    }
}
