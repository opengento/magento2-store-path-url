<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\App\Request;

use Magento\Framework\App\Request\PathInfo as PathInfoSubject;
use Magento\Framework\App\RequestInterface;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Service\StorePathFixer;

class PathInfo
{
    public function __construct(
        private Config $config,
        private StorePathFixer $storePathFixer,
        private RequestInterface $request
    ) {}

    public function beforeGetPathInfo(PathInfoSubject $subject, string $requestUri, string $baseUrl): array
    {
        if ($this->config->isEnabled()) {
            $requestUri = $this->storePathFixer->fix($baseUrl ? $requestUri : $this->request->getUriString(), $requestUri);
        }

        return [$requestUri, $baseUrl];
    }
}
