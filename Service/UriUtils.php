<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Service;

use Magento\Store\Api\Data\StoreInterface;

use function ltrim;
use function parse_url;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr_replace;

use const PHP_URL_PATH;

class UriUtils
{
    public function __construct(private PathResolver $pathResolver) {}

    public function replaceScopeCode(string $url, StoreInterface $store): string
    {
        return $this->replaceLeadingPath($store->getCode(), $this->pathResolver->resolve($store), $url);
    }

    public function replacePathCode(string $url, StoreInterface $store): string
    {
        return $this->replaceLeadingPath($this->pathResolver->resolve($store), $store->getCode(), $url);
    }

    private function replaceLeadingPath(string $search, string $replace, string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        return $path !== '/' && str_starts_with(ltrim($path, '/'), ltrim($search, '/'))
            ? str_replace($path, $this->replacePath($search, $replace, $path), $uri)
            : $uri;
    }

    private function replacePath(string $search, string $replace, string $path): string
    {
        return str_replace('//', '/', substr_replace($path, $replace, (int)str_starts_with($path, '/'), strlen($search)));
    }
}
