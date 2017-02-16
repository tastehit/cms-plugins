<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Model\Config\Source\Placement;

class Products implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected static $_options;

    const AFTER_DESCRIPTION             = 1;
    const BETWEEN_DESCRIPTION_AND_IMAGE = 2;
    const AFTER_ADD_TO_CART             = 3;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = [
                ['label' => __('After the product description'), 'value' => self::AFTER_DESCRIPTION],
                ['label' => __('Between the product picture and description'), 'value' => self::BETWEEN_DESCRIPTION_AND_IMAGE],
                ['label' => __('After Add to Cart button'), 'value' => self::AFTER_ADD_TO_CART],
            ];
        }
        return self::$_options;
    }
}
