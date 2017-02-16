<?php
/**
 * ProductRecommendation main helper
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Tastehit_ProductRecommendation_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PLACEMENT_NODE = 'productrecommendation/placement/';

    const EXPORT_CATALOG_FILENAME = 'products.csv';
    const EXPORT_HISTORY_FILENAME = 'history.csv';
    /**
     * Is the module enabled in configuration.
     *
     * @return bool
     */
    public function isEnabled()
    {
        $storeId = Mage::app()->getStore()->getId();
        return (bool)Mage::getStoreConfig('productrecommendation/general/enable', $storeId);
    }

    /**
     * Get Customer ID from configuration.
     *
     * @return string
     */
    public function getCustomerId()
    {
        $code = Mage::getSingleton('adminhtml/config_data')->getStore();
        $storeId = Mage::getModel('core/store')->load($code)->getId();

        return (string)Mage::getStoreConfig('productrecommendation/general/customer_id', $storeId);
    }

    /**
     * Get History export DateTime
     *
     * @return string
     */
    public function getHistoryExportDateTime()
    {
        $code = Mage::getSingleton('adminhtml/config_data')->getStore();
        $storeId = Mage::getModel('core/store')->load($code)->getId();
        return (string)Mage::getStoreConfig('productrecommendation/info/history_export', $storeId);
    }

    /**
     * Get Product export DateTime
     *
     * @return string
     */
    public function getProductExportDateTime()
    {
        $code = Mage::getSingleton('adminhtml/config_data')->getStore();
        $storeId = Mage::getModel('core/store')->load($code)->getId();
        return (string)Mage::getStoreConfig('productrecommendation/info/product_export', $storeId);
    }

    /**
     * Get Public Path for Exports from configuration.
     *
     * @return string
     */
    public function getPublicExportPath()
    {
        $storeId = Mage::app()->getStore()->getId();
        return (string)Mage::getStoreConfig('productrecommendation/general/export_path', $storeId) . DS;
    }

    /**
     * Get Export Directory Path
     * @param $storeId
     * @return string
     */
    public function getExportDirPath($storeId)
    {
        return Mage::getBaseDir('media') . DS . (string)Mage::getStoreConfig('productrecommendation/general/export_path', $storeId) . DS;
    }


    /**
     * Get Export URL
     *
     * @return string
     */
    public function getExportUrl()
    {
        $code = Mage::getSingleton('adminhtml/config_data')->getStore();
        $storeId = Mage::getModel('core/store')->load($code)->getId();
        return Mage::getBaseUrl('media') . (string)Mage::getStoreConfig('productrecommendation/general/export_path', $storeId) . DS;
    }


    public function isDisplayOnHomePage()
    {
        $storeId = Mage::app()->getStore()->getId();
        return Mage::getStoreConfigFlag(self::PLACEMENT_NODE . '' , $storeId);
    }

    /**
     * Get Widgets Data from configuration
     *
     * @return array
     */
    public function getWidgetsData()
    {
        $storeId = Mage::app()->getStore()->getId();
        $data = array();
        $widgets = array('product_page', 'home_page', 'category_page');
        // Limitation to 3 widgets of every kind
        foreach ($widgets as $widget) {
            if (Mage::getStoreConfigFlag(self::PLACEMENT_NODE . 'enable_' . $widget)) {
                $data[$widget] = array();
                for ($i = 1; $i <= 3; $i++) {
                    if (Mage::getStoreConfigFlag(self::PLACEMENT_NODE . $widget . '_enable_' . $i, $storeId)) {
                        $data[$widget][$i]['id'] = Mage::getStoreConfig(self::PLACEMENT_NODE . $widget . '_id_' . $i, $storeId);
                        $data[$widget][$i]['display'] = Mage::getStoreConfig(self::PLACEMENT_NODE . $widget . '_display_' . $i, $storeId);
                    }
                }
            }
        }

        return $data;
    }


    /**
     * Generate Layout update
     *
     * @param $widget
     * @param $id
     * @param $position
     * @return null|string
     */
    public function getLayoutUpdate($widget, $id, $position)
    {
        $update = null;
        switch ($widget) {
            case ($widget == 'product_page'):
                switch ($position) {
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Products::AFTER_DESCRIPTION):
                        $update = '<reference name="content"><block type="tastehit_productrecommendation/catalog_product" name="tastehit.product.'. $id .'" after="product.info"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Products::BETWEEN_DESCRIPTION_AND_IMAGE):
                        $update = '<reference name="product.info.media.after"><block type="tastehit_productrecommendation/catalog_product" name="tastehit.product.'. $id .'"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Products::AFTER_ADD_TO_CART):
                        $update = '<reference name="product.info.extrahint"><block type="tastehit_productrecommendation/catalog_product" name="tastehit.product.'. $id .'"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Products::CUSTOM_POSITION):
                        $update = '<reference name="product.info"><block type="tastehit_productrecommendation/catalog_product" name="tastehit.product.'. $id .'"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                }
                break;
            case ($widget == 'home_page'):
                switch ($position) {
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Home::BEFORE_CARROUSEL):
                        $update = '<reference name="content"><block type="tastehit_productrecommendation/home" name="tastehit.home.'. $id .'" before="-"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Home::BEFORE_FOOTER):
                        $update = '<reference name="content"><block type="tastehit_productrecommendation/home" name="tastehit.home.'. $id .'" after="-"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                }
                break;
            case ($widget == 'category_page'):
                switch ($position) {
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Category::BEFORE_PRODUCT_LIST):
                        $update = '<reference name="content"><block type="tastehit_productrecommendation/catalog_category" name="tastehit.category.' . $id . '" before="product_list"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                    case ($position == Tastehit_ProductRecommendation_Model_System_Config_Source_Placement_Category::AFTER_PRODUCT_LIST):
                        $update = '<reference name="content"><block type="tastehit_productrecommendation/catalog_category" name="tastehit.category.' . $id . '" after="product_list"><action method="setData"><name>widget_id</name><value>'. $id .'</value></action></block></reference>';
                        break;
                }
                break;
        }

        return $update;
    }
}
