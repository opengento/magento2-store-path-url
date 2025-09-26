<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Plugin\Model;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\BaseUrlChecker as Subject;
use Magento\Store\Model\StoreManagerInterface;

use function parse_url;

class BaseUrlChecker
{
    public function __construct(private StoreManagerInterface $storeManager) {}

    /**
     * @throws NoSuchEntityException
     */
    public function beforeExecute(Subject $subject, array $uri, Http $request): array
    {
        return [parse_url($this->storeManager->getStore()->getBaseUrl()), $request];
    }
}
