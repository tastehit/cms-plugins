<?php
/**
 * ProductRecommendation
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Category
{
    protected static $_options;

    const BEFORE_PRODUCT_LIST    = 1;
    const AFTER_PRODUCT_LIST    = 2;

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('At the top of the page, before the category listings'),
                    'value' => self::BEFORE_PRODUCT_LIST,
                ),
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('At the bottom of the page, after the category listings'),
                    'value' => self::AFTER_PRODUCT_LIST,
                ),
            );
        }

        return self::$_options;
    }
}
