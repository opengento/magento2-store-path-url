<?php

declare(strict_types=1);

/**
 * Copyright © OpenGento, All rights reserved.
 */

namespace Opengento\StorePathUrl\Plugin\App\Request;

use Magento\Framework\App\Request\Http;
use Magento\Store\App\Request\StorePathInfoValidator as StorePathInfoValidatorSubject;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Service\StorePathFixer;

class StorePathInfoValidator
{
    public function __construct(
        private Config $config,
        private StorePathFixer $storePathFixer
    ) {}

    public function beforeGetValidStoreCode(
        StorePathInfoValidatorSubject $subject,
        Http $request,
        string $pathInfo = ''
    ): array {
        if ($pathInfo !== '' && $this->config->isEnabled()) {
            $pathInfo = $this->storePathFixer->fix($request->getBaseUrl(), $pathInfo);
        }

        return [$request, $pathInfo];
    }
}
