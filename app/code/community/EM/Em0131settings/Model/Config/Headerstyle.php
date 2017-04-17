<?php
class EM_Em0131settings_Model_Config_Headerstyle
{
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'style03', 'label'=>Mage::helper('adminhtml')->__('Style 03')),
            array('value' => 'style08', 'label'=>Mage::helper('adminhtml')->__('Style 08')),
            array('value' => 'style14', 'label'=>Mage::helper('adminhtml')->__('Style 14')),
            array('value' => 'style27', 'label'=>Mage::helper('adminhtml')->__('Style 27')),            
            array('value' => 'style28', 'label'=>Mage::helper('adminhtml')->__('Style 28')),
            array('value' => 'style29', 'label'=>Mage::helper('adminhtml')->__('Style 29')),
            array('value' => 'style30', 'label'=>Mage::helper('adminhtml')->__('Style 30')),
        );
    }
}
?>