<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Opengento\StorePathUrl\Model\Config\PathType;

use function array_map;

class PathTypes implements OptionSourceInterface
{
    private ?array $options = null;

    public function toOptionArray(): array
    {
        return $this->options ??= array_map(
            static fn(PathType $pathType): array => ['label' => $pathType->getLabel(), 'value' => $pathType->value],
            PathType::cases()
        );
    }
}
