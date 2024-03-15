<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Html\Select;
use Opengento\StorePathUrl\Block\Adminhtml\Config\Form\Field\Select\Store;

class StorePath extends AbstractFieldArray
{
    /**
     * @throws LocalizedException
     */
    public function getStoreSelectRenderer(): Select
    {
        if (!$this->hasData('store_select_renderer')) {
            $this->setData(
                'store_select_renderer',
                $this->getLayout()->createBlock(
                    Store::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            );
        }

        return $this->getData('store_select_renderer');
    }

    /**
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'store',
            [
                'label' => new Phrase('Store'),
                'class' => 'required-entry',
                'renderer' => $this->getStoreSelectRenderer(),
            ]
        );
        $this->addColumn('path', ['label' => new Phrase('Path'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = (new Phrase('Add Store Path'))->render();
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $row->setData(
            'option_extra_attrs',
            [
                'option_' . $this->getStoreSelectRenderer()->calcOptionHash($row->getData('store')) => 'selected="selected"',
            ]
        );
    }
}
