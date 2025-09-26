<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\App\Request;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Router\Base;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\App\Request\PathInfoProcessor as AppPathInfoProcessor;
use Magento\Store\App\Request\StorePathInfoValidator;
use Opengento\StorePathUrl\Model\Config;
use Opengento\StorePathUrl\Service\PathResolver;

use function str_starts_with;
use function strlen;
use function substr;

class PathInfoProcessor extends AppPathInfoProcessor
{
    public function __construct(
        private StorePathInfoValidator $storePathInfoValidator,
        private PathResolver $pathResolver,
        private StoreRepositoryInterface $storeRepository,
        private Config $config,
        private AppPathInfoProcessor $subject
    ) {}

    public function process(RequestInterface $request, $pathInfo): string
    {
        if (!$this->config->isEnabled()) {
            return $this->subject->process($request, $pathInfo);
        }

        $storeCode = $this->storePathInfoValidator->getValidStoreCode($request);
        if ($storeCode !== null) {
            try {
                $storePath = $this->pathResolver->resolve($this->storeRepository->getActiveStoreByCode($storeCode));
            } catch (LocalizedException) {
                return $pathInfo;
            }
            if (!$request->isDirectAccessFrontendName($storePath)) {
                // Armored test to prevent cases where the path wouldn't start by a "/" when it's expected.
                if (str_starts_with($pathInfo, '/')) {
                    $storePath = '/' . $storePath;
                }
                // Prevent to trim a part where the store path could be included.
                // E.g: We assume we have the store path "fr", we don't want to trim the "fr" of "/franchise".
                if (str_starts_with($pathInfo . '/', $storePath . '/')) {
                    $pathInfo = substr($pathInfo, strlen($storePath)) ?: '/';
                }
            } else {
                // No route in case we're trying to access a store that has the same code as a direct access.
                $request->setActionName(Base::NO_ROUTE);
            }
        }

        return $pathInfo;
    }
}
