<?php
/**
 * ProductRecommendation
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$whitelistBlock = Mage::getModel('admin/block')->load('tastehit_productrecommendation/home', 'block_name');
$whitelistBlock->setData('block_name', 'tastehit_productrecommendation/home');
$whitelistBlock->setData('is_allowed', 1);
$whitelistBlock->save();

$installer->endSetup();
