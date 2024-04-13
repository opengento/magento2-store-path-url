<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Model\StoreSwitcher;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreSwitcherInterface;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Service\UriUtils;

class RevertStorePath implements StoreSwitcherInterface
{
    public function __construct(
        private Config $config,
        private UriUtils $uriUtils
    ) {}

    public function switch(StoreInterface $fromStore, StoreInterface $targetStore, string $redirectUrl): string
    {
        return $this->config->isEnabled() ? $this->uriUtils->replacePathCode($redirectUrl, $targetStore) : $redirectUrl;
    }
}
