<?php
class EM_Minislideshow_Block_Adminhtml_Slider_Edit_Tab_Appearance extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('minislideshow_appearance', array('legend'=>Mage::helper('minislideshow')->__('Animation')));
     
		$fieldset->addField('animation_type', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Animation Type'),
		  'name'      => 'appearance[animation_type]',
		  'note'	  => Mage::helper('minislideshow')->__('The Shadow displays underneath the banner. The shadow applies to fixed and responsive modes only, the full width slider don\'t have a shadow'),
		  'values'    => Mage::getModel('minislideshow/animationtype ')->getOptionArray()
		));
		
		$fieldset->addField('speed', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Speed'),
		  'name'      => 'appearance[speed]',
		  'note'	  => Mage::helper('minislideshow')->__('Speed'),
		  'values'    => Mage::getModel('minislideshow/speed ')->getOptionArray()
		));
		
		$fieldset->addField('pause_time', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Pause Time'),
		  'name'      => 'appearance[pause_time]',
		  'note'	  => Mage::helper('minislideshow')->__('Pause Time'),
		  'values'    => Mage::getModel('minislideshow/times')->getOptionArray()
		));
		
		$fieldset->addField('hover_pause', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Hover Pause'),
		  'name'      => 'appearance[hover_pause]',
		  'note'	  => Mage::helper('minislideshow')->__('Hover Pause'),
		  'values'    => array(
                array(
    				  'value'     => 'true',
    				  'label'     => Mage::helper('minislideshow')->__('Enable'),
    			  ),
    			  array(
    				  'value'     => 'false',
    				  'label'     => Mage::helper('minislideshow')->__('Disable'),
    			  ),
		  ),
		));
        
        $fieldset->addField('slices', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Slices'),
		  'name'      => 'appearance[slices]',
		  'note'	  => Mage::helper('minislideshow')->__('Slices'),
		  'values'    => Mage::getModel('minislideshow/pieces')->getOptionArray()
		));
		
		$fieldset->addField('box_columns', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Box Columns'),
		  'name'      => 'appearance[box_columns]',
		  'note'	  => Mage::helper('minislideshow')->__('Box Columns'),
		  'values'    => Mage::getModel('minislideshow/pieces')->getOptionArray()
		));
        
        $fieldset->addField('box_rows', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Box Rows'),
		  'name'      => 'appearance[box_rows]',
		  'note'	  => Mage::helper('minislideshow')->__('Box Rows'),
		  'values'    => Mage::getModel('minislideshow/pieces')->getOptionArray()
		));
     
      if ( Mage::getSingleton('adminhtml/session')->getMinislideshowData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMinislideshowData());
          Mage::getSingleton('adminhtml/session')->setMinislideshowData(null);
      } elseif ( Mage::registry('minislideshow_data') ) {
          $form->setValues(Mage::registry('minislideshow_data')->getData());
      }
      return parent::_prepareForm();
  }
}