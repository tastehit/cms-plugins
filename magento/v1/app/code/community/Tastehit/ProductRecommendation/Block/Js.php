<?php
/**
 * ProductRecommendation Js block
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Block_Js extends Mage_Core_Block_Template
{
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tastehit/js.phtml');
    }
}
