<?php
/**
 * ProductRecommendation
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH  = 'crontab/jobs/tastehit_export/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/tastehit_export/run/model';
    
    /**
     * Cron settings after save
     *
     * @return Tastehit_ProductRecommendation_Model_System_Config_Backend_Cron
     */
    protected function _afterSave()
    {
        $frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $time       = $this->getData('groups/general/fields/export_time/value');
        $frequency   = $this->getData('groups/general/fields/export_frequency/value');

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',        # Day of the Week
        );
        $cronExprString = join(' ', $cronExprArray);
        
        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        }
        catch (Exception $e) {
            Mage::throwException(Mage::helper('backup')->__('Unable to save the cron expression.'));
        }
    }
}
