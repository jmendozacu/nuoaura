<?php

class EM_Minislideshow_Block_Adminhtml_Minislideshow_Edit_Tab_Appearance extends
    Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('minislideshow_appearance', array('legend' =>
                Mage::helper('minislideshow')->__('Slide Animation')));

        $fieldset->addField('auto_play', 'select', array(
            'label' => Mage::helper('minislideshow')->__('Auto Play'),
            'name' => 'appearance[auto_play]',
            'note' => Mage::helper('minislideshow')->__('Force manual transitions'),
            'values' => array(
                array(
                    'value' => 'true',
                    'label' => Mage::helper('minislideshow')->__('Enable'),
                    ),
                array(
                    'value' => 'false',
                    'label' => Mage::helper('minislideshow')->__('Disable'),
                    ),
                ),
            ));

        $fieldset->addField('animation_type', 'select', array(
            'label' => Mage::helper('minislideshow')->__('Animation Type'),
            'name' => 'appearance[animation_type]',
            'note' => Mage::helper('minislideshow')->__('Specify sets like: "fold,fade,sliceDown"'),
            'values' => Mage::getModel('minislideshow/slideanimation ')->getOptionArray()));

        $fieldset->addField('speed', 'text', array(
            'label' => Mage::helper('minislideshow')->__('Speed'),
            'name' => 'appearance[speed]',
            'note' => Mage::helper('minislideshow')->__('Slide transition speed'),
            'values' => '200'));

        $fieldset->addField('hover_pause', 'select', array(
            'label' => Mage::helper('minislideshow')->__('Stop On Hover'),
            'name' => 'appearance[hover_pause]',
            'note' => Mage::helper('minislideshow')->__('Stop animation while hovering'),
            'values' => array(
                array(
                    'value' => 'true',
                    'label' => Mage::helper('minislideshow')->__('Enable'),
                    ),
                array(
                    'value' => 'false',
                    'label' => Mage::helper('minislideshow')->__('Disable'),
                    ),
                ),
            ));

        $fieldset->addField('progress_bar', 'select', array(
            'label' => Mage::helper('minislideshow')->__('Progress Bar'),
            'name' => 'appearance[progress_bar]',
            'note' => Mage::helper('minislideshow')->__('Progress Bar'),
            'values' => array(
                array(
                    'value' => 'true',
                    'label' => Mage::helper('minislideshow')->__('Enable'),
                    ),
                array(
                    'value' => 'false',
                    'label' => Mage::helper('minislideshow')->__('Disable'),
                    ),
                ),
            ));

        $fieldset->addField('lazy_load', 'select', array(
            'label' => Mage::helper('minislideshow')->__('Lazy Load'),
            'name' => 'appearance[lazy_load]',
            'note' => Mage::helper('minislideshow')->__('Lazy Load'),
            'values' => array(
                array(
                    'value' => 'true',
                    'label' => Mage::helper('minislideshow')->__('Enable'),
                    ),
                array(
                    'value' => 'false',
                    'label' => Mage::helper('minislideshow')->__('Disable'),
                    ),
                ),
            ));

        if (Mage::getSingleton('adminhtml/session')->getMinislideshowData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMinislideshowData());
            Mage::getSingleton('adminhtml/session')->setMinislideshowData(null);
        } elseif (Mage::registry('minislideshow_data')) {
            $form->setValues(Mage::registry('minislideshow_data')->getData());
        }
        return parent::_prepareForm();
    }
}
