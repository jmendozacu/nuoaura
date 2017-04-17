<?php

class Propoza_Propoza_Block_Adminhtml_System_Config_Buttons_Testconnection extends Mage_Adminhtml_Block_System_Config_Form_Field
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
            $this->setTemplate('propoza/system/config/buttons/button_test_connection.phtml');
        }
        $apiKey = Mage::getStoreConfig('propoza/general/api_key');
        if (!empty($apiKey) && !Mage::helper('propoza')->isValidApiKey($apiKey)) {
            $link = sprintf('<a href="%s" target="_blank">Dashboard</a>', Mage::helper('propoza')->getDashboardPropozaUrl());
            $this->getMessagesBlock()->addWarning(Mage::helper('propoza')->__('Your Propoza API key is outdated. For security reasons please generate a new API key and save it to your dashboard and module. %s', $link));
        }
        return $this;
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('propoza')->__($originalData['button_label']),
            'link_text' => Mage::helper('propoza')->__($originalData['link_text']),
            'html_id' => $element->getHtmlId(),
        ));
        return $this->_toHtml();
    }
}