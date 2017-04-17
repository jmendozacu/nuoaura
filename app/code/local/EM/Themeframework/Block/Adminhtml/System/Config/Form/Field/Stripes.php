<?php 
class EM_Themeframework_Block_Adminhtml_System_Config_Form_Field_Stripes extends Mage_Adminhtml_Block_System_Config_Form_Field
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
		foreach (Mage::getModel('themeframework/config_stripes')->toOptionArray() as $row) {
			$html .= sprintf('<a href="#" class="%s %s" data-input-value="%s"><img src="%s" style="background-image:url(%s)" /></a> ', 
				$element->getId(),
				$element->getValue() == $row['value'] ? 'selected' : '',
				$row['value'],
				Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em_themeframework/background/stripes/blank.gif',
				Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'em_themeframework/background/stripes/'.$row['value']);
		}
		
        return $html;
    } 
}
?>