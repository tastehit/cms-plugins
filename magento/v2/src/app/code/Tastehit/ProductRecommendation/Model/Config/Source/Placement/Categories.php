<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Model\Config\Source\Placement;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected static $_options;

    const BEFORE_PRODUCT_LIST   = 1;
    const AFTER_PRODUCT_LIST    = 2;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = [
                ['label' => __('At the top of the page, before the category listings'), 'value' => self::BEFORE_PRODUCT_LIST],
                ['label' => __('At the bottom of the page, after the category listings'), 'value' => self::AFTER_PRODUCT_LIST],
            ];
        }
        return self::$_options;
    }
}
