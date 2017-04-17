<?php 
class EM_Em0131settings_Block_Adminhtml_System_Config_Form_Field_Footerstyle extends Mage_Adminhtml_Block_System_Config_Form_Field
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
        $html .= '<ul class="em-preview-list">';
		foreach (Mage::getModel('em0131settings/config_footerstyle')->toOptionArray() as $row) {            
			$html .= sprintf('<li class="em-img-preview"><p><a href="%s" class="%s %s" data-lightbox="footer-style" data-title="%s"><img width="100px" height="100px" alt="" src="%s" /></a></p><p><span>%s</span></p></li> ', 
				Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/em0131/default/images/preview/footer/'.$row['value'].'.jpg',
                $element->getId(),
				$element->getValue() == $row['value'] ? 'selected' : '',
                $row['label'],                
                Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/em0131/default/images/preview/footer/'.$row['value'].'.jpg',
                $row['label']);            
		}
        $html .= '</ul>';
		
        return $html;
    } 
}
?>