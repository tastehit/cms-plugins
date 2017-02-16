<?php
/**
 * ProductRecommendation Product Widget block
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Block_Catalog_Product extends Mage_Core_Block_Template
{
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        //TODO: move to template
        $widgetId = $this->getWidgetId();
        return '<div class="thRecommendations" data-widgetid="' . $widgetId . '"></div>';
    }
}
