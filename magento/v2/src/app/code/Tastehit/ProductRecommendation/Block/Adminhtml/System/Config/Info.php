<?php
/**
 * Tastehit
 *
 * @package     Tastehit_ProductRecommendation
 * @copyright   Copyright (c) 2016 Tastehit (http://www.tastehit.com)
 * @license     http://wiki.tastehit.com/wiki/EULA  End-user License Agreement
 */

namespace Tastehit\ProductRecommendation\Block\Adminhtml\System\Config;

use \Magento\Store\Model\ScopeInterface;

class Info extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager     = $storeManager;
        $this->_objectManager    = $objectManager;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_getAdditionalInfoHtml();
    }

    protected function _getAdditionalInfoHtml()
    {
        $html = <<<HTML
<a href="https://www.tastehit.com/login" target="_blank">  
    <img src="https://www.tastehit.com/static/img/th-logo.png">  
</a>
HTML;
        return $html;
    }
}
