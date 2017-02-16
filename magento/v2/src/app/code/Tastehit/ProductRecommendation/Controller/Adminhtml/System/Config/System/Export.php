<?php
/**
 *
 * Copyright © 2016 Tastehit. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tastehit\ProductRecommendation\Controller\Adminhtml\System\Config\System;

class Export extends \Magento\Backend\App\Action
{
    /**
     * Export action
     *
     * @return void
     */
    public function execute()
    {
        session_write_close();
        $cronModel = $this->_objectManager->create('\Tastehit\ProductRecommendation\Cron\Export');
        try {
            $cronModel->execute();
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
    }
}
