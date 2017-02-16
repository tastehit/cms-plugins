<?php
/**
 * ProductRecommendation
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Adminhtml_TastehitController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Export Action
     *
     * @return $this
     */
    public function exportAction()
    {
        $exportResult = Mage::getModel('tastehit_productrecommendation/observer')->export();

        return $this;
    }
}