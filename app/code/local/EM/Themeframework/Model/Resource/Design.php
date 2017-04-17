<?php
class EM_Themeframework_Model_Resource_Design extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('themeframework/design_change_theme', 'id');
    }
}