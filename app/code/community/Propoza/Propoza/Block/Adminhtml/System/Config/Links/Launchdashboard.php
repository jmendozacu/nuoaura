<?php

class Propoza_Propoza_Block_Adminhtml_System_Config_Links_Launchdashboard extends Mage_Adminhtml_Block_System_Config_Form_Field
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
            $this->setTemplate('propoza/configuration/link_propoza_dashboard.phtml');
        }
        return $this;
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array('link_text' => Mage::helper('propoza')->__($originalData['link_text'])));
        return $this->_toHtml();
    }
}