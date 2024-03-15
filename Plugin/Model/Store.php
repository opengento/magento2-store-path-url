<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\Model;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store as SubjectStore;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Service\UriUtils;

class Store
{
    public function __construct(
        private Config $config,
        private UriUtils $uriUtils
    ) {}

    public function afterGetBaseUrl(
        SubjectStore $subject,
        string $baseUrl,
        string $type = UrlInterface::URL_TYPE_LINK,
        ?bool $secure = null
    ): string {
        return $type === UrlInterface::URL_TYPE_LINK && $this->config->isEnabled()
            ? $this->uriUtils->replaceStoreCode($baseUrl, $subject)
            : $baseUrl;
    }
}
