<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;

/**
 * Class AddHandles
 *
 * Add custom layout handles to te page
 *
 * @package Tastehit\ProductRecommendation\Observer
 */
class AddHandles implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;

    /**
     * AddHandles constructor.
     * 
     * @param \Magento\Framework\Registry $registry
     * @param \Tastehit\ProductRecommendation\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Tastehit\ProductRecommendation\Helper\Data $helper
    ) {
        $this->_registry = $registry;
        $this->_helper = $helper;
    }

    /**
     * Add handles to the page.
     *
     * @param Observer $observer
     * @event layout_load_before
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var LayoutInterface $layout */
        $layout = $observer->getData('layout');
        $fullActionName = $observer->getData('full_action_name');
        $widgetsData = $this->_helper->getWidgetsData();
        foreach ($widgetsData as $widget => $data) {
            for ($i = 1; $i <= 3; $i++) {
                if (isset($data[$i])) {
                    $update = null;
                    if ($fullActionName == 'catalog_product_view' && $widget == 'product_page') {
                        $update = $this->_helper->getLayoutUpdate($widget, $data[$i]['id'], $data[$i]['display']);
                        $layout->getUpdate()->addUpdate($update);
                    } elseif ($fullActionName == 'catalog_category_view' && $widget == 'category_page') {
                        $update = $this->_helper->getLayoutUpdate($widget, $data[$i]['id'], $data[$i]['display']);
                        $layout->getUpdate()->addUpdate($update);
                    } elseif ($fullActionName == 'cms_index_index' && $widget == 'home_page') {
                        $update = $this->_helper->getLayoutUpdate($widget, $data[$i]['id'], $data[$i]['display']);
                        $layout->getUpdate()->addUpdate($update);
                    }

                }

            }
        }
    }
}
