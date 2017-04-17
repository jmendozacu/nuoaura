<?php
class EM_Themeframework_Model_Config_Sidebar
{
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'left', 'label'=>Mage::helper('adminhtml')->__('Left')),
            array('value' => 'right', 'label'=>Mage::helper('adminhtml')->__('Right')),
            array('value' => 'none', 'label'=>Mage::helper('adminhtml')->__('None')),
            array('value' => 'all', 'label'=>Mage::helper('adminhtml')->__('Left and Right')),
        );
    }
}
?>