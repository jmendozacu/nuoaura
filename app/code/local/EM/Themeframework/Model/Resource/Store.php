<?php
class EM_Themeframework_Model_Resource_Store extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct()
    {
        $this->_init('themeframework/active_store', 'store_id');
    }
}