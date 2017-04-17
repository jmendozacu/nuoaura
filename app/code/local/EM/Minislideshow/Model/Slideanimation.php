<?php
class EM_Minislideshow_Model_Slideanimation extends Varien_Object
{
    static public function getOptionArray()
    {
        return array(
            array('value' => 'fade', 'label'=>Mage::helper('adminhtml')->__('Fade')),
            array('value' => 'backSlide', 'label'=>Mage::helper('adminhtml')->__('Back Slide')),                    
            array('value' => 'goDown', 'label'=>Mage::helper('adminhtml')->__('Go Down')),
            array('value' => 'scaleUp', 'label'=>Mage::helper('adminhtml')->__('Scale Up')),
        );
    }
}