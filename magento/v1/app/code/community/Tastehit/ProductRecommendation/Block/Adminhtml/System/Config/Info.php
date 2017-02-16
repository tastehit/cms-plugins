<?php
/**
 * ProductRecommendation Info block
 *
 * @package    Tastehit_ProductRecommendation
 * @author     Tastehit
 * @copyright  Copyright (c) 2016 Tastehit. (https://www.tastehit.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tastehit_ProductRecommendation_Block_Adminhtml_System_Config_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render Info html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('tastehit_productrecommendation');
        $productExportDateTime = $helper->getProductExportDateTime();
        $historyExportDateTime = $helper->getHistoryExportDateTime();
        $catalogUrl = $helper->getExportUrl() . $helper::EXPORT_CATALOG_FILENAME;
        $historyUrl = $helper->getExportUrl() . $helper::EXPORT_HISTORY_FILENAME;
        $customerId = $helper->getCustomerId();
        $html = <<<HTML
<a href="https://www.tastehit.com/login" target="_blank">  
    <img src="https://www.tastehit.com/static/img/th-logo.png">  
</a>
<div class="content-header">
    <h3>
        <span>Current Status</span>
    </h3>
</div>
<table cellspacing="0" class="form-list">
    <colgroup class="label"></colgroup>
    <colgroup class="value"></colgroup>
    <tbody>
        <tr id="row_productrecommendation_info_state">
            <td class="label"><label for="productrecommendation_info_state">State of Service</label></td>
            <td class="value" id="productrecommendation_info_state">
                <div class="value label­danger">Not available</div>
            </td>
        </tr>
        <tr id="row_productrecommendation_info_recommendations">
            <td class="label"><label for="productrecommendation_info_recommendations">Recommendations</label></td>
            <td class="value"> </td>
        </tr>
        <tr id="row_productrecommendation_info_catalog_url">
            <td class="label"><label for="productrecommendation_info_catalog_url">Catalog URL</label></td>
            <td class="value"><a href="$catalogUrl">$catalogUrl</a></td>
        </tr>
        <tr id="row_productrecommendation_info_history_url">
            <td class="label"><label for="productrecommendation_info_history_url">Buying history URL</label></td>
            <td class="value"><a href="$historyUrl">$historyUrl</a></td>
        </tr>
        <tr id="row_productrecommendation_info_catalog_export_date">
            <td class="label"><label for="productrecommendation_info_catalog_export_date">Last catalog export</label></td>
            <td class="value">$productExportDateTime</td>
        </tr>
        <tr id="row_productrecommendation_info_history_export_date">
            <td class="label"><label for="productrecommendation_info_history_export_date">Last cart history export</label></td>
            <td class="value">$historyExportDateTime</td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">
//<![CDATA[
document.observe('dom:loaded', function(){
    var requestUri = '//www.tastehit.com/api/' + "$customerId" + '/stats';
    var container = $('productrecommendation_info_state').down();
    var loader = $('loading-mask');
    loader.show();

    function loadXMLDoc() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                if (xmlhttp.status == 200) {
                    response = JSON.parse(xmlhttp.responseText);
                    if (response.engine && response.engine.ready) {
                        container.innerHTML = 'Online';
                    }
                } else if (xmlhttp.status == 400) {
                    console.log('There was an error 400');
                } else {
                    console.log('something else other than 200 was returned');
                }
            }
        };

        xmlhttp.open("GET", requestUri, true);
        xmlhttp.send();
    };

    loadXMLDoc();
    loader.hide();
});
//]]>
</script>
HTML;

        return $html;
    }
}
