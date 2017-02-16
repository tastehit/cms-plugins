<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;

/**
 * Backend system config datetime field renderer
 */
class HistoryUrl extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * StoreManager Interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $fileName = 'history.csv';
        $exportDirectory = $this->_scopeConfig->getValue(\Tastehit\ProductRecommendation\Cron\Export::XML_PATH_EXPORT_PATH, ScopeInterface::SCOPE_STORE);
        $fullUrl = $mediaUrl . $exportDirectory . '/' . $fileName;

        return '<a href="' . $fullUrl . '">' . $fullUrl . '</a>';
    }
}
