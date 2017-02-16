<?php
/**
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Block\Catalog;

/**
 * Class AbstractWidget
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractWidget extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Tastehit_ProductRecommendation::tastehit/widget.phtml';

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
