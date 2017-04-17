<?php 
class EM_Themeframework_Block_Adminhtml_System_Config_Form_Field_Product_View extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
     * Override field method to add js
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$html = parent::_getElementHtml($element);      
		$html .= sprintf('<p><a href="%s" class="preview-product-view" data-lightbox="product-view" data-title="Product View Column"><img width="100px" height="100px" alt="" src="%s" /></a></p><p><span>Product View Columns</span></p> ', 
		Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em_themeframework/preview/product-view.jpg',        
        Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em_themeframework/preview/product-view.jpg');
		
        return $html;
    } 
}
?>