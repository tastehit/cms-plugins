<?php
/**
 * ProductRecommendation Observer
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Model_Observer
{
    /**
     * Add Widgets
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addWidgets(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('tastehit_productrecommendation');
        $widgets = array('product_page', 'home_page', 'category_page');
        $routeName = Mage::app()->getRequest()->getRouteName();
        $fullActionName = Mage::app()->getFrontController()->getAction()->getFullActionName();
        $identifier = Mage::getSingleton('cms/page')->getIdentifier();
        $layoutUpdate = $observer->getEvent()->getLayout()->getUpdate();
        $widgetsData = Mage::helper('tastehit_productrecommendation')->getWidgetsData();
        foreach ($widgetsData as $widget => $data) {
            for ($i = 1; $i <= 3; $i++) {
                $update = null;
                if ($fullActionName == 'catalog_product_view' && Mage::getStoreConfigFlag('productrecommendation/placement/enable_product_page') && $widget == 'product_page') {
                    if (!empty($widgetsData['product_page']) && isset($data[$i]) && Mage::getStoreConfigFlag('productrecommendation/placement/product_page_enable_' .  $i)) {
                        $update = $helper->getLayoutUpdate($widget, $data[$i]['id'], $data[$i]['display']);
                        if ($update) {
                            $layoutUpdate->addUpdate($update);
                        }
                    }
                } elseif ($fullActionName == 'catalog_category_view' && Mage::getStoreConfigFlag('productrecommendation/placement/enable_category_page') && $widget == 'category_page') {
                    if (!empty($widgetsData['category_page']) && Mage::getStoreConfigFlag('productrecommendation/placement/category_page_enable_' .  $i)) {
                        $update = $helper->getLayoutUpdate($widget, $data[$i]['id'], $data[$i]['display']);
                        if ($update) {
                            $layoutUpdate->addUpdate($update);
                        }
                    }
                } elseif ($routeName == 'cms' && $identifier == 'home' && Mage::getStoreConfigFlag('productrecommendation/placement/enable_home_page') && $widget == 'home_page') {
                    if (!empty($widgetsData['home_page']) && Mage::getStoreConfigFlag('productrecommendation/placement/home_page_enable_' .  $i)) {
                        $update = $helper->getLayoutUpdate($widget, $data[$i]['id'], $data[$i]['display']);
                        if ($update) {
                            $layoutUpdate->addUpdate($update);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Export csv files
     */
    public function export()
    {
        $helper = Mage::helper('tastehit_productrecommendation');
        //TODO: Implement export for multistore setup
        $store = Mage::app()->getDefaultStoreView();
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);

        $storeCode = Mage::app()->getRequest()->getParam('store');
        if ($storeCode) {
            $storeIdFromRequest = $storeCode;
        } else {
            $storeIdFromRequest = $store->getId();
        }
        $exportPath = $helper->getExportDirPath($storeIdFromRequest);
        $productExportFile = $exportPath . $helper::EXPORT_CATALOG_FILENAME;

        $io->open(array('path' => $exportPath));
        $io->streamOpen($productExportFile, 'w+');
        $io->streamLock(true);

        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributeCollection */
        $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $attributeCollection->addFieldToFilter('is_filterable', true);
        foreach ($attributeCollection as $attribute) {
            $attributeCodesArray[] = $attribute->getAttributeCode();
        }
        $productCsvHeader = array(
            'id',
            'sku',
            'availability',
            'item_url',
            'title',
            'description',
            'category',
            'sale_price',
            'image_url',
            'small_image_url',
            'thumbnail_url'
        );

        $io->streamWriteCsv(array_merge($productCsvHeader, $attributeCodesArray), '|', ' ');

        $products = Mage::getModel('catalog/product')->getCollection()->addUrlRewrite()->addAttributeToSelect('*');
        $products->addFieldToFilter('visibility',
            array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
            )
        );
        $products->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        foreach ($products as $product){
            $product->setStoreId($store->getId());
            $stockText = '';
            $stockStatus = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock();
            if ($stockStatus == Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK) {
                $stockText = 'In Stock';
            } elseif (Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK) {
                $stockText = 'Out of Stock';
            }
            $productUrl = preg_replace('/\?___store\=.*/', '', $product->getProductUrl());
            $mainData = array(
                $product->getId(),
                $product->getSku(),
                $stockText,
                $productUrl,
                str_replace('|', '_', $product->getName()),
                str_replace('|', '_',$product->getDescription()),
                $this->_getCategories($product->getCategoryIds()),
                number_format($product->getPrice(), 2),
                $product->getFinalPrice(),
                $this->_getProductImage($product, 'image'),
                $this->_getProductImage($product, 'small_image', 165),
                $this->_getProductImage($product, 'thumbnail', 400)
            );

            $filterableAttributesData = array();
            foreach ($attributeCodesArray as $attributeCode) {
                $filterableAttributesData[] = (string)$product->getAttributeText($attributeCode);
            }

            $io->streamWriteCsv(array_merge($mainData, $filterableAttributesData), '|', ' ');
        }

        $io->streamUnlock();
        $io->streamClose();
        $productExportDateTime = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        Mage::getConfig()->saveConfig('productrecommendation/info/product_export', $productExportDateTime, 'default', $store->getId());

        $previousSalesExportTime = $helper->getHistoryExportDateTime();
        $salesExportFrequency = Mage::getStoreConfig('productrecommendation/info/export_frequency', $store->getId());
        $to = date('Y-m-d H:i:s',time());

        $salesExportFile = $exportPath . $helper::EXPORT_HISTORY_FILENAME;
        $from = $previousSalesExportTime;

        $soldProductCollection = $collection = Mage::getResourceModel('sales/order_item_collection');

        $soldProductCollection
            ->addFieldToFilter(
                'main_table.created_at', array('gt' => array($from))
            )
            ->addFieldToFilter(
                'main_table.created_at', array('lt' => array($to))
            );
        $soldProductCollection->join(
            array('orders_table' => 'sales/order'),
            'orders_table.entity_id = main_table.order_id',
            array('customer_id' => 'customer_id')
        );

        if ($soldProductCollection->count() > 0) {
            $historyCsvHeader = array(
                'id_user',
                'product_id'
            );
            $io->open(array('path' => $exportPath));
            $io->streamOpen($salesExportFile, 'w+');
            $io->streamLock(true);
            $io->streamWriteCsv($historyCsvHeader, ';', ' ');
            foreach ($soldProductCollection as $item) {
                $customerId = $item->getId();
                $sku = $item->getSku();
                if ($sku && $customerId) {
                    $io->streamWriteCsv(array($customerId, $sku), ';', ' ');
                }

            }
            $io->streamUnlock();
            $io->streamClose();
            $historyExportDateTime = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            Mage::getConfig()->saveConfig('productrecommendation/info/history_export', $historyExportDateTime, 'default', $store->getId());
        }

        Mage::app()->getCacheInstance()->cleanType('config');
        return true;
    }


    /**
     * Get product Categories
     *
     * @param $categoryIds
     * @return array|string
     * @throws Mage_Core_Exception
     */
    protected function _getCategories($categoryIds)
    {
        $categoriesArray = array();
        if ($categoryIds && !empty($categoryIds)) {
            $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', $categoryIds);
            foreach ($categories as $category) {
                $categoriesArray[] = $category->getName();
            }

            return implode(', ',$categoriesArray);
        } else {
            return '';
        }
    }

    /**
     * Get product image
     *
     * @param $product
     * @param $imageType
     * @param $size
     * @return string
     */
    protected function _getProductImage($product, $imageType, $size = null)
    {
        $imgSrc = '';
        try{
            if ($size) {
                $imgSrc = Mage::helper('catalog/image')->init($product, $imageType)->keepFrame(false)->resize($size);
            } else {
                $imgSrc = Mage::helper('catalog/image')->init($product, $imageType);
            }
        }
        catch(Exception $e) {
            $imgSrc = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/small_image.jpg',array('_area'=>'frontend'));
        }

        return (string) $imgSrc;
    }
}