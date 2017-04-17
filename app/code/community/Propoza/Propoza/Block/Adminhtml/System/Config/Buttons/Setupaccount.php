<?php

class Propoza_Propoza_Block_Adminhtml_System_Config_Buttons_Setupaccount extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('propoza/system/config/buttons/button_setup_account.phtml');
        }
        return $this;
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('propoza')->__($originalData['button_label']),
            'html_id' => $element->getHtmlId(),
        ));

        return $this->_toHtml();
    }
}