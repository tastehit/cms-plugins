<?php
/**
 * ProductRecommendation
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Products
{
    protected static $_options;

    const AFTER_DESCRIPTION             = 1;
    const BETWEEN_DESCRIPTION_AND_IMAGE = 2;
    const AFTER_ADD_TO_CART             = 3;
    const CUSTOM_POSITION               = 4;

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('After the product description'),
                    'value' => self::AFTER_DESCRIPTION,
                ),
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('Between the product picture and description'),
                    'value' => self::BETWEEN_DESCRIPTION_AND_IMAGE,
                ),
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('After Add to Cart button'),
                    'value' => self::AFTER_ADD_TO_CART,
                ),
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('Custom Position'),
                    'value' => self::CUSTOM_POSITION,
                ),
            );
        }

        return self::$_options;
    }
}
