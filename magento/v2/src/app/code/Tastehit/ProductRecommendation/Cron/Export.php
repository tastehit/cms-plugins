<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;

class Export
{
    const XML_PATH_EXPORT_ENABLED = 'productrecommendation/general/enabled';
    const XML_PATH_EXPORT_PATH = 'productrecommendation/general/export_path';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Export data
     *
     * @var \Tastehit\ProductRecommendation\Helper\Data
     */
    protected $_exportData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Api\Data\WebsiteInterface[]
     */
    protected $_websites;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $_stockItemRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_productAttributeCollectionFactory;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvProcessor;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     */
    protected $categoryResource;

    /**
     * @var \Magento\Catalog\Api\CategoryAttributeRepositoryInterface $categoryAttributeRepositoryInterface
     */
    protected $categoryAttributeRepositoryInterface;

    /**
     * @var \Magento\Framework\Filesystem\Io\File $io
     */
    protected $io;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    protected $date;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Tastehit\ProductRecommendation\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemsCollectionFactory;

    /**
     * Catalog Image Helper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     * @param \Magento\Catalog\Api\CategoryAttributeRepositoryInterface $categoryAttributeRepositoryInterface
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem,
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date,
     * @param \Magento\Framework\Filesystem\Io\File $io,
     * @param \Tastehit\ProductRecommendation\Helper\Data $helper,
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper,
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magento\Catalog\Api\CategoryAttributeRepositoryInterface $categoryAttributeRepositoryInterface,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Filesystem\Io\File $io,
        \Tastehit\ProductRecommendation\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemsCollectionFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockItemRepository = $stockItemRepository;

        $this->_productAttributeCollectionFactory = $productAttributeCollectionFactory;

        $this->categoryResource = $categoryResource;
        $this->categoryAttributeRepositoryInterface = $categoryAttributeRepositoryInterface;

        $this->_csvProcessor = $csvProcessor;

        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->date = $date;
        $this->io = $io;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_helper = $helper;
        $this->orderItemsCollectionFactory = $orderItemsCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    /**
     * Export CSV
     *
     * @return $this
     */
    public function execute()
    {
        if (!$this->_scopeConfig->isSetFlag(self::XML_PATH_EXPORT_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return $this;
        }

        $this->_errors = [];
        try {
            foreach ($this->_getWebsites() as $website) {
                /* @var $website \Magento\Store\Model\Website */
                if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                    continue;
                }
                $storeId = $website->getDefaultGroup()->getDefaultStore()->getId();
                $products = $this->_getProducts();
                $this->_writeProductsToFile($products, $storeId);
                $this->_helper->setLastProductExport();
                $soldProductCollection = $this->_getSales();
                $this->_writeSalesToFile($soldProductCollection, $storeId);
                $this->_helper->setLastSalesExport();
            }
        } catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
            $this->_logger->info($e->getMessage());
            $this->_logger->critical($e);
        }

        return $this;
    }

    /**
     * Get Product collection
     *
     * @return mixed
     */
    protected function _getProducts()
    {
        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addUrlRewrite()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->setVisibility($this->productVisibility->getVisibleInSiteIds());;

        return $productCollection;
    }

    /**
     * Get Sold products collection
     *
     * @return mixed
     */
    protected function _getSales()
    {
        $toDate = $this->date->date('Y-m-d H:i:s');
        $fromDate = $this->_helper->getLastSalesExport();

        $soldProductCollection = $this->orderItemsCollectionFactory->create();

        $soldProductCollection
            ->addFieldToFilter('main_table.created_at', ['gt' => [$fromDate]])
            ->addFieldToFilter('main_table.created_at', ['lt' => [$toDate]]);
        $soldProductCollection->join(
            ['orders_table' => $soldProductCollection->getTable('sales_order')],
            'orders_table.entity_id = main_table.order_id',
            ['customer_id' => 'customer_id']
        );

        return $soldProductCollection;
    }

    protected function _writeProductsToFile($products, $storeId)
    {
        if (count($products) > 0) {
            $fileName = 'products.csv';
            $exportDirectory = $this->_scopeConfig->getValue(self::XML_PATH_EXPORT_PATH, ScopeInterface::SCOPE_STORE);
            $this->mediaDirectory->create($exportDirectory);
            $mediaPath = $this->mediaDirectory->getAbsolutePath('/');
            $fullFilePath = $mediaPath . '/' . $exportDirectory . '/' . $fileName;

            /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollection */
            $attributeCollection = $this->_productAttributeCollectionFactory->create();
            $attributeCollection->addIsFilterableFilter();
            foreach ($attributeCollection as $attribute) {
                if ($attribute->getAttributeCode() != 'price') {
                    $attributeCodesArray[] = $attribute->getAttributeCode();
                }
            }

            $mainData = [];
            foreach ($products as $product) {
                $product->setStoreId($storeId);
                $productData = [
                    'id'    => $product->getId(),
                    'sku'   => $product->getSku(),
                    'availability'  => $this->_getStockInformation($product->getId()),
                    'item_url'      => $this->_getProductUrl($product, $storeId),
                    'title'         => str_replace('|', '_', $product->getName()),
                    'description'   => str_replace('|', '_', $product->getDescription()),
                    'price'         => number_format($product->getPrice(), 2),
                    'sale_price'    => $product->getFinalPrice(),
                    'category'      => $this->_getCategories($product->getCategoryIds()),
                    'image_url'     => $this->imageHelper->init($product, 'product_page_image_large')->setImageFile($product->getImage())->getUrl(),
                    'small_image_url' => $this->imageHelper->init($product, 'product_page_image_small')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)->resize(165)
                        ->setImageFile($product->getSmallImage())->getUrl(),
                    'thumbnail_url'   => $this->imageHelper->init($product, 'product_page_image_medium')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)->resize(400)
                        ->setImageFile($product->getThumbnail())->getUrl()
                ];

                $filterableAttributesData = [];
                foreach ($attributeCodesArray as $attributeCode) {
                    $attributeValue = $product->getAttributeText($attributeCode);
                    if (is_array($attributeValue)) {
                        $filterableAttributesData[$attributeCode] = implode(', ',$attributeValue);
                    } else {
                        $filterableAttributesData[$attributeCode] = (string)$attributeValue;
                    }
                }
                $productData = array_merge($productData, $filterableAttributesData);
                if (empty($mainData)) {
                    $mainData[] = array_keys($productData);
                }
                $mainData[] = $productData;
            }
            $this->_csvProcessor->setDelimiter('|');
            $this->_csvProcessor->setEnclosure(' ');
            $this->_csvProcessor->saveData($fullFilePath, $mainData);
        }
    }

    protected function _writeSalesToFile($soldProductCollection, $storeId)
    {
        if (count($soldProductCollection) > 0) {
            $fileName = 'history.csv';
            $exportDirectory = $this->_scopeConfig->getValue(self::XML_PATH_EXPORT_PATH, ScopeInterface::SCOPE_STORE);
            $this->mediaDirectory->create($exportDirectory);
            $mediaPath = $this->mediaDirectory->getAbsolutePath('/');
            $fullFilePath = $mediaPath . '/' . $exportDirectory . '/' . $fileName;
            $mainData = [];
            foreach ($soldProductCollection as $item) {
                $customerId = $item->getId();
                $sku = $item->getSku();
                if ($sku) {
                    if (empty($mainData)) {
                        $mainData[] = ['id_user', 'product_id'];
                    }
                    $mainData[] = [$customerId, $sku];
                }
            }
            $this->_csvProcessor->setDelimiter(';');
            $this->_csvProcessor->setEnclosure(' ');
            $this->_csvProcessor->saveData($fullFilePath, $mainData);
        }
    }

    /**
     *
     * @return string
     */
    protected function _getDestinationPath()
    {
        $this->mediaDirectory->create();

        return $this->mediaDirectory->getAbsolutePath('/');
    }

    /**
     * Get product Categories
     *
     * @param $categoryIds
     * @return array|string
     */
    protected function _getCategories($categoryIds)
    {
        $categoriesArray = array();
        if ($categoryIds && !empty($categoryIds)) {

            foreach ($categoryIds as $categoryId) {
                /** @var \Magento\Catalog\Api\CategoryAttributeRepositoryInterface $this->categoryAttributeRepository */
                $nameAttribute = $this->categoryAttributeRepositoryInterface->get('name');
                $categoriesArray[] = $this->categoryResource->getAttributeRawValue(
                    $categoryId,
                    $nameAttribute,
                    $this->storeManager->getStore()
                );
            }

            return implode(', ',$categoriesArray);
        } else {
            return '';
        }
    }

    /**
     * Get product store URL for given product
     *
     * @param $product
     * @param $storeId
     * @return string
     */
    protected function _getProductUrl($product, $storeId)
    {
        return $product->getProductUrl();
    }

    /**
     * Get Stock text for given product
     *
     * @param $productId
     * @return string
     */
    protected function _getStockInformation($productId)
    {
        $stockText = '';
        $productStock = $this->_stockItemRepository->get($productId);
        $stockStatus = $productStock->getIsInStock();
        if ($stockStatus == \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK) {
                $stockText = 'In Stock';
        } elseif (\Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK) {
                $stockText = 'Out of Stock';
        }

        return $stockText;
    }

    /**
     * Retrieve website collection array
     *
     * @return array
     */
    protected function _getWebsites()
    {
        if ($this->_websites === null) {
            try {
                $this->_websites = $this->storeManager->getWebsites();
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
            }
        }
        return $this->_websites;
    }
}
