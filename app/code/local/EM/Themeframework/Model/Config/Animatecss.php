<?php
class EM_Themeframework_Model_Config_Animatecss
{
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {        
        return array(
            array(
                'label'=>'Attention Seekers', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'bounce', 'label'=>Mage::helper('adminhtml')->__('bounce')),
                    array('value' => 'flash', 'label'=>Mage::helper('adminhtml')->__('flash')),
                    array('value' => 'pulse', 'label'=>Mage::helper('adminhtml')->__('pulse')),
                    array('value' => 'rubberBand', 'label'=>Mage::helper('adminhtml')->__('rubberBand')),            
                    array('value' => 'shake', 'label'=>Mage::helper('adminhtml')->__('shake')),
                    array('value' => 'swing', 'label'=>Mage::helper('adminhtml')->__('swing')),
                    array('value' => 'tada', 'label'=>Mage::helper('adminhtml')->__('tada')),
                    array('value' => 'wobble', 'label'=>Mage::helper('adminhtml')->__('wobble')),
                )
            ),
            array(
                'label'=>'Bouncing Entrances', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'bounceIn', 'label'=>Mage::helper('adminhtml')->__('bounceIn')),
                    array('value' => 'bounceInDown', 'label'=>Mage::helper('adminhtml')->__('bounceInDown')),
                    array('value' => 'bounceInLeft', 'label'=>Mage::helper('adminhtml')->__('bounceInLeft')),
                    array('value' => 'bounceInRight', 'label'=>Mage::helper('adminhtml')->__('bounceInRight')),            
                    array('value' => 'bounceInUp', 'label'=>Mage::helper('adminhtml')->__('bounceInUp')),
                )
            ),
            array(
                'label'=>'Bouncing Exits', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'bounceOut', 'label'=>Mage::helper('adminhtml')->__('bounceOut')),
                    array('value' => 'bounceOutDown', 'label'=>Mage::helper('adminhtml')->__('bounceOutDown')),
                    array('value' => 'bounceOutLeft', 'label'=>Mage::helper('adminhtml')->__('bounceOutLeft')),
                    array('value' => 'bounceOutRight', 'label'=>Mage::helper('adminhtml')->__('bounceOutRight')),            
                    array('value' => 'bounceOutUp', 'label'=>Mage::helper('adminhtml')->__('bounceOutUp')),
                )
            ),
            array(
                'label'=>'Fading Entrances', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'fadeIn', 'label'=>Mage::helper('adminhtml')->__('fadeIn')),
                    array('value' => 'fadeInDown', 'label'=>Mage::helper('adminhtml')->__('fadeInDown')),
                    array('value' => 'fadeInDownBig', 'label'=>Mage::helper('adminhtml')->__('fadeInDownBig')),
                    array('value' => 'fadeInLeft', 'label'=>Mage::helper('adminhtml')->__('fadeInLeft')),            
                    array('value' => 'fadeInLeftBig', 'label'=>Mage::helper('adminhtml')->__('fadeInLeftBig')),
                    array('value' => 'fadeInRight', 'label'=>Mage::helper('adminhtml')->__('fadeInRight')),
                    array('value' => 'fadeInRightBig', 'label'=>Mage::helper('adminhtml')->__('fadeInRightBig')),
                    array('value' => 'fadeInUp', 'label'=>Mage::helper('adminhtml')->__('fadeInUp')),
                    array('value' => 'fadeInUpBig', 'label'=>Mage::helper('adminhtml')->__('fadeInUpBig')),
                )
            ),
            array(
                'label'=>'Fading Exits', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'fadeOut', 'label'=>Mage::helper('adminhtml')->__('fadeOut')),
                    array('value' => 'fadeOutDown', 'label'=>Mage::helper('adminhtml')->__('fadeOutDown')),
                    array('value' => 'fadeOutDownBig', 'label'=>Mage::helper('adminhtml')->__('fadeOutDownBig')),
                    array('value' => 'fadeOutLeft', 'label'=>Mage::helper('adminhtml')->__('fadeOutLeft')),            
                    array('value' => 'fadeOutLeftBig', 'label'=>Mage::helper('adminhtml')->__('fadeOutLeftBig')),
                    array('value' => 'fadeOutRight', 'label'=>Mage::helper('adminhtml')->__('fadeOutRight')),
                    array('value' => 'fadeOutRightBig', 'label'=>Mage::helper('adminhtml')->__('fadeOutRightBig')),
                    array('value' => 'fadeOutUp', 'label'=>Mage::helper('adminhtml')->__('fadeOutUp')),
                    array('value' => 'fadeOutUpBig', 'label'=>Mage::helper('adminhtml')->__('fadeOutUpBig')),
                )
            ),
            array(
                'label'=>'Flippers', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'flip', 'label'=>Mage::helper('adminhtml')->__('flip')),
                    array('value' => 'flipInX', 'label'=>Mage::helper('adminhtml')->__('flipInX')),
                    array('value' => 'flipInY', 'label'=>Mage::helper('adminhtml')->__('flipInY')),
                    array('value' => 'flipOutX', 'label'=>Mage::helper('adminhtml')->__('flipOutX')),            
                    array('value' => 'flipOutY', 'label'=>Mage::helper('adminhtml')->__('flipOutY')),
                )
            ),
            array(
                'label'=>'Lightspeed', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'lightSpeedIn', 'label'=>Mage::helper('adminhtml')->__('lightSpeedIn')),
                    array('value' => 'lightSpeedOut', 'label'=>Mage::helper('adminhtml')->__('lightSpeedOut')),
                )
            ),
            array(
                'label'=>'Rotating Entrances', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'rotateIn', 'label'=>Mage::helper('adminhtml')->__('rotateIn')),
                    array('value' => 'rotateInDownLeft', 'label'=>Mage::helper('adminhtml')->__('rotateInDownLeft')),
                    array('value' => 'rotateInDownRight', 'label'=>Mage::helper('adminhtml')->__('rotateInDownRight')),
                    array('value' => 'rotateInUpLeft', 'label'=>Mage::helper('adminhtml')->__('rotateInUpLeft')),            
                    array('value' => 'rotateInUpRight', 'label'=>Mage::helper('adminhtml')->__('rotateInUpRight')),
                )
            ),
            array(
                'label'=>'Rotating Exits', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'rotateOut', 'label'=>Mage::helper('adminhtml')->__('rotateOut')),
                    array('value' => 'rotateOutDownLeft', 'label'=>Mage::helper('adminhtml')->__('rotateOutDownLeft')),
                    array('value' => 'rotateOutDownRight', 'label'=>Mage::helper('adminhtml')->__('rotateOutDownRight')),
                    array('value' => 'rotateOutUpLeft', 'label'=>Mage::helper('adminhtml')->__('rotateOutUpLeft')),            
                    array('value' => 'rotateOutUpRight', 'label'=>Mage::helper('adminhtml')->__('rotateOutUpRight')),
                )
            ),
            array(
                'label'=>'Rotating Exits', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'slideInDown', 'label'=>Mage::helper('adminhtml')->__('slideInDown')),
                    array('value' => 'slideInLeft', 'label'=>Mage::helper('adminhtml')->__('slideInLeft')),
                    array('value' => 'slideInRight', 'label'=>Mage::helper('adminhtml')->__('slideInRight')),
                    array('value' => 'slideOutLeft', 'label'=>Mage::helper('adminhtml')->__('slideOutLeft')),            
                    array('value' => 'slideOutRight', 'label'=>Mage::helper('adminhtml')->__('slideOutRight')),
                    array('value' => 'slideOutUp', 'label'=>Mage::helper('adminhtml')->__('slideOutUp')),
                )
            ),
            array(
                'label'=>'Specials', //label of the option group
                'value'=>array(//options in the option group
                    array('value' => 'hinge', 'label'=>Mage::helper('adminhtml')->__('hinge')),
                    array('value' => 'rollIn', 'label'=>Mage::helper('adminhtml')->__('rollIn')),
                    array('value' => 'rollOut', 'label'=>Mage::helper('adminhtml')->__('rollOut')),
                )
            )
        );
    }
}
?>