<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Block\Catalog;

/**
 * Product Widget Block
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductWidget extends AbstractWidget
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
    }
}