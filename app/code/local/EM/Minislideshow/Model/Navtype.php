<?php
class EM_Minislideshow_Model_Navtype extends Varien_Object
{
    static public function getOptionArray()
    {
        return array(
            array('value' => 'navigation:true', 'label'=>Mage::helper('adminhtml')->__('Next/Pre')),
            array('value' => 'pagination:true', 'label'=>Mage::helper('adminhtml')->__('Bullet')),
            array('value' => 'pagination:true,paginationNumbers:true', 'label'=>Mage::helper('adminhtml')->__('Number')),
            array('value' => 'thumbnail', 'label'=>Mage::helper('adminhtml')->__('Thumb')),  
        );
    }
}