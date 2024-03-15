<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\Url;

use Magento\Framework\Url\ScopeInterface;
use Magento\Framework\UrlInterface;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Service\UriUtils;

class Scope
{
    public function __construct(
        private Config $config,
        private UriUtils $uriUtils
    ) {}

    public function afterGetBaseUrl(
        ScopeInterface $subject,
        string $baseUrl,
        string $type = UrlInterface::URL_TYPE_LINK,
        ?bool $secure = null
    ): string {
        return $type === UrlInterface::URL_TYPE_LINK && $this->config->isEnabled()
            ? $this->uriUtils->replaceScopeCode($baseUrl, $subject)
            : $baseUrl;
    }
}
