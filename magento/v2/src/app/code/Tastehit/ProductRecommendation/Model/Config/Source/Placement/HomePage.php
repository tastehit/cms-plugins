<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Model\Config\Source\Placement;

class HomePage implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected static $_options;

    const BEFORE_CARROUSEL  = 1;
    const BEFORE_FOOTER     = 2;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = [
                ['label' => __('At the top of the page, before the main carrousel/block'), 'value' => self::BEFORE_CARROUSEL],
                ['label' => __('At the bottom of the home page, before the footer'), 'value' => self::BEFORE_FOOTER],
            ];
        }
        return self::$_options;
    }
}
