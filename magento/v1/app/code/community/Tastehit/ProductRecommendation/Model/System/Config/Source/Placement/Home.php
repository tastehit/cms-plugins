<?php
/**
 * ProductRecommendation
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Home
{
    protected static $_options;

    const BEFORE_CARROUSEL  = 1;
    const BEFORE_FOOTER     = 2;
//    const CUSTOM_POSITION   = 3;

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('At the top of the page, before the main carrousel/block'),
                    'value' => self::BEFORE_CARROUSEL,
                ),
                array(
                    'label' => Mage::helper('tastehit_productrecommendation')->__('At the bottom of the home page, before the footer'),
                    'value' => self::BEFORE_FOOTER,
                ),
//                array(
//                    'label' => Mage::helper('tastehit_productrecommendation')->__('Custom Position'),
//                    'value' => self::CUSTOM_POSITION,
//                ),
            );
        }

        return self::$_options;
    }
}
