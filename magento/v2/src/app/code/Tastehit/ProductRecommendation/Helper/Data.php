<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\Filesystem;

/**
 * Data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const PLACEMENT_NODE = 'productrecommendation/placement/';

    const EXPORT_CATALOG_FILENAME = 'products.csv';
    const EXPORT_HISTORY_FILENAME = 'history.csv';

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_configSectionId = 'productrecommendation';

    /**
     * Application Cache Manager
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cacheManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Filesystem $filesystem
     * @param \Magento\Framework\App\CacheInterface $cacheInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Filesystem $filesystem,
        \Magento\Framework\App\CacheInterface $cacheInterface
    ) {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
        $this->_cacheManager = $cacheInterface;
    }

    /**
     * Is the module enabled in configuration
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            $this->_configSectionId.'/general/enabled', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Customer ID from configuration
     *
     * @return string
     */
    public function getCustomerId()
    {
        return (string)$this->scopeConfig->getValue(
            $this->_configSectionId.'/general/customer_id', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Widgets Data from configuration
     *
     * @return array
     */
    public function getWidgetsData()
    {
        $data = [];
        $widgets = ['product_page', 'home_page', 'category_page'];
        // Limitation to 3 widgets of every kind
        foreach ($widgets as $widget) {
            if ($this->scopeConfig->isSetFlag(
                self::PLACEMENT_NODE . 'enable_' . $widget,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {

                $data[$widget] = [];
                for ($i = 1; $i <= 3; $i++) {
                    if ($this->scopeConfig->isSetFlag(
                        self::PLACEMENT_NODE . $widget . '_enable_' . $i,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                        $data[$widget][$i]['id'] = $this->scopeConfig->getValue(
                            self::PLACEMENT_NODE . $widget . '_id_' . $i,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        $data[$widget][$i]['display'] = $this->scopeConfig->getValue(
                            self::PLACEMENT_NODE . $widget . '_display_' . $i,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Generate Layout update
     * 
     * @param string $widget
     * @param int $id
     * @param int $position
     * @return null
     */
    public function getLayoutUpdate($widget, $id, $position)
    {
        $update = null;
        switch ($widget) {
            case ($widget == 'product_page'):
                switch ($position) {
                    case ($position == \Tastehit\ProductRecommendation\Model\Config\Source\Placement\Products::AFTER_DESCRIPTION):
                        $update = '<referenceContainer name="content">
<block class="Tastehit\ProductRecommendation\Block\Catalog\ProductWidget" name="tastehit.product.'. $id .'" after="product.info.overview">
       <arguments>
           <argument name="widget_id" xsi:type="string">'. $id .'</argument>
       </arguments>
    </block>
</referenceContainer>';
                        break;
                    case ($position == \Tastehit\ProductRecommendation\Model\Config\Source\Placement\Products::BETWEEN_DESCRIPTION_AND_IMAGE):
                        $update = '<referenceContainer name="product.info.media">
<block class="Tastehit\ProductRecommendation\Block\Catalog\ProductWidget" name="tastehit.product.'. $id .'" after="-">
       <arguments>
           <argument name="widget_id" xsi:type="string">'. $id .'</argument>
       </arguments>
    </block>
</referenceContainer>';
                        break;
                    case ($position == \Tastehit\ProductRecommendation\Model\Config\Source\Placement\Products::AFTER_ADD_TO_CART):
                        $update = '<referenceContainer name="product.info.main">
<block class="Tastehit\ProductRecommendation\Block\Catalog\ProductWidget" name="tastehit.product.'. $id .'" after="-">
       <arguments>
           <argument name="widget_id" xsi:type="string">'. $id .'</argument>
       </arguments>
    </block>
</referenceContainer>';
                        break;
                }
                break;
            case ($widget == 'home_page'):
                switch ($position) {
                    case ($position == \Tastehit\ProductRecommendation\Model\Config\Source\Placement\HomePage::BEFORE_CARROUSEL):
                        $update = '<referenceContainer name="content">
<block class="Tastehit\ProductRecommendation\Block\Catalog\HomePageWidget" name="tastehit.home.page.'. $id .'" before="-">
       <arguments>
           <argument name="widget_id" xsi:type="string">'. $id .'</argument>
       </arguments>
    </block>
</referenceContainer>';
                        break;
                    case ($position == \Tastehit\ProductRecommendation\Model\Config\Source\Placement\HomePage::BEFORE_FOOTER):
                        $update = '<referenceContainer name="content">
<block class="Tastehit\ProductRecommendation\Block\Catalog\HomePageWidget" name="tastehit.home.page.'. $id .'" after="-">
       <arguments>
           <argument name="widget_id" xsi:type="string">'. $id .'</argument>
       </arguments>
    </block>
</referenceContainer>';
                        break;
                }
                break;
            case ($widget == 'category_page'):
                switch ($position) {
                    case ($position == \Tastehit\ProductRecommendation\Model\Config\Source\Placement\Categories::BEFORE_PRODUCT_LIST):
                        $update = '<referenceContainer name="content">
<block class="Tastehit\ProductRecommendation\Block\Catalog\CategoryWidget" name="tastehit.category.'. $id .'" before="-">
       <arguments>
           <argument name="widget_id" xsi:type="string">'. $id .'</argument>
       </arguments>
    </block>
</referenceContainer>';
                        break;
                    case ($position == \Tastehit\ProductRecommendation\Model\Config\Source\Placement\Categories::AFTER_PRODUCT_LIST):
                        $update = '<referenceContainer name="content">
<block class="Tastehit\ProductRecommendation\Block\Catalog\CategoryWidget" name="tastehit.category.'. $id .'" after="-">
       <arguments>
           <argument name="widget_id" xsi:type="string">'. $id .'</argument>
       </arguments>
    </block>
</referenceContainer>';
                        break;

                }
                break;
        }

        return $update;
    }

    /**
     * Retrieve Last products export time
     *
     * @return int
     */
    public function getLastProductExport()
    {
        return $this->_cacheManager->load('productrecommendation_last_product_export');
    }

    /**
     * Set last products export time (now)
     *
     * @return $this
     */
    public function setLastProductExport()
    {
        $this->_cacheManager->save(time(), 'productrecommendation_last_product_export');
        return $this;
    }

    /**
     * Retrieve Last sales export time
     *
     * @return int
     */
    public function getLastSalesExport()
    {
        return $this->_cacheManager->load('productrecommendation_last_sales_export');
    }

    /**
     * Set last sales export time (now)
     *
     * @return $this
     */
    public function setLastSalesExport()
    {
        $this->_cacheManager->save(time(), 'productrecommendation_last_sales_export');
        return $this;
    }
}
