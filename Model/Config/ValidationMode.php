<?php

namespace Imagina\Placetopay\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class ValidationMode implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'development', 'label' => __('Development')],
            ['value' => 'testing', 'label' => __('Testing')],
            ['value' => 'production', 'label' => __('Production')],
        ];
    }
}