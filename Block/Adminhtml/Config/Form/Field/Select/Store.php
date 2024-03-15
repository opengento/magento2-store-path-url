<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Model\System\Store as StoreSource;

class Store extends Select
{
    private StoreSource $storeSource;

    public function __construct(
        Context $context,
        StoreSource $storeSource,
        array $data = []
    ) {
        $this->storeSource = $storeSource;
        parent::__construct($context, $data);
    }

    public function setInputName(string $inputName): self
    {
        return $this->setData('name', $inputName);
    }

    protected function _toHtml(): string
    {
        if ($this->getData('multiple')) {
            $this->setData('extra_params', 'multiple="multiple"');
            $this->setInputName($this->getData('name') . '[]');
        }
        if (!$this->getOptions()) {
            $this->setOptions($this->storeSource->toOptionArray());
        }

        return parent::_toHtml();
    }
}
