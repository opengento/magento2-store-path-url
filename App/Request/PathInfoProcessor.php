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

        $storeCode = $this->storePathInfoValidator->getValidStoreCode($request, $pathInfo);
        if ($storeCode !== null) {
            try {
                $path = $this->pathResolver->resolve($this->storeRepository->getActiveStoreByCode($storeCode));
            } catch (LocalizedException) {
                return $pathInfo;
            }
            if (!$request->isDirectAccessFrontendName($path)) {
                $pathInfo = substr($pathInfo, strlen($path) + (int)str_starts_with($pathInfo, '/')) ?: '/';
            } else {
                //no route in case we're trying to access a store that has the same code as a direct access
                $request->setActionName(Base::NO_ROUTE);
            }
        }

        return $pathInfo;
    }
}
