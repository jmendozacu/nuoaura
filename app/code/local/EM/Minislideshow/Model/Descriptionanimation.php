<?php
class EM_Minislideshow_Model_Descriptionanimation extends Varien_Object
{
    static public function getOptionArray()
    {
        return array(
            array('value' => 'none', 'label'=>Mage::helper('adminhtml')->__('None')),
            array('value' => 'fadeIn', 'label'=>Mage::helper('adminhtml')->__('fadeIn')),
            array('value' => 'fadeInDown', 'label'=>Mage::helper('adminhtml')->__('fadeInDown')),                    
            array('value' => 'fadeInLeft', 'label'=>Mage::helper('adminhtml')->__('fadeInLeft')),
            array('value' => 'fadeInRight', 'label'=>Mage::helper('adminhtml')->__('fadeInRight')),                    
            array('value' => 'fadeInUp', 'label'=>Mage::helper('adminhtml')->__('fadeInUp')), 
            array('value' => 'fadeOut', 'label'=>Mage::helper('adminhtml')->__('fadeOut')),
            array('value' => 'fadeOutDown', 'label'=>Mage::helper('adminhtml')->__('fadeOutDown')),                    
            array('value' => 'fadeOutLeft', 'label'=>Mage::helper('adminhtml')->__('fadeOutLeft')),
            array('value' => 'fadeOutRight', 'label'=>Mage::helper('adminhtml')->__('fadeOutRight')),                    
            array('value' => 'fadeOutUp', 'label'=>Mage::helper('adminhtml')->__('fadeOutUp')), 
            array('value' => 'slideInUp', 'label'=>Mage::helper('adminhtml')->__('slideInUp')),
            array('value' => 'slideInDown', 'label'=>Mage::helper('adminhtml')->__('slideInDown')),
            array('value' => 'slideInLeft', 'label'=>Mage::helper('adminhtml')->__('slideInLeft')),
            array('value' => 'slideInRight', 'label'=>Mage::helper('adminhtml')->__('slideInRight')),
            array('value' => 'slideOutUp', 'label'=>Mage::helper('adminhtml')->__('slideOutUp')),
            array('value' => 'slideOutDown', 'label'=>Mage::helper('adminhtml')->__('slideOutDown')),
            array('value' => 'slideOutLeft', 'label'=>Mage::helper('adminhtml')->__('slideOutLeft')),
            array('value' => 'slideOutRight', 'label'=>Mage::helper('adminhtml')->__('slideOutRight')),
        );
    }
}