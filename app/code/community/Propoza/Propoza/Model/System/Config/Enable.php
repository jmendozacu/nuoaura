<?php

class Propoza_Propoza_Model_System_Config_Enable extends Mage_Core_Model_Config_Data {
    public $configPath = "advanced/modules_disable_output/Propoza_Propoza";

    /*protected function _afterLoad() {
        $this->setValue(!Mage::getStoreConfig($this->configPath, $this->getScopeId()));

        return  parent::_afterLoad();
    }*/


    public function save() {
        $this->_disabledModule( !$this->getValue());

        return parent::save();
    }

    protected function _disabledModule( $disable) {
        Mage::getModel('core/config')->saveConfig($this->configPath, $disable, $this->getScope(), $this->getScopeId());
    }
}