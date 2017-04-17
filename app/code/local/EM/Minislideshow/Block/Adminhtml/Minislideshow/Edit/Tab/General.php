<?php
class EM_Minislideshow_Block_Adminhtml_Minislideshow_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('minislideshow_general', array('legend'=>Mage::helper('minislideshow')->__('General')));

		$fieldset->addField('name', 'text', array(
		  'label'     => Mage::helper('minislideshow')->__('Name'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'name',
		));

		$fieldset->addField('identifier', 'text', array(
		  'label'     => Mage::helper('minislideshow')->__('Identifier'),
		  'title'     => Mage::helper('minislideshow')->__('Identifier'),
		  'class'     => 'validate-xml-identifier',
		  'required'  => true,
		  'name'      => 'identifier',
		));
        
		// status field
		$fieldset->addField('status_slideshow', 'select', array(
			'label'     => Mage::helper('minislideshow')->__('Status'),
			'title'     => Mage::helper('minislideshow')->__('Status'),
			'name'      => 'status_slideshow',
			'options'   => array(
				'1' => Mage::helper('minislideshow')->__('Enabled'),
				'2' => Mage::helper('minislideshow')->__('Disabled'),
			),
		));
		
		$fieldset2 = $form->addFieldset('minislideshow_general2', array('legend'=>Mage::helper('minislideshow')->__('Slider Size')));

		$fieldset2->addField('size_width', 'text', array(
		  'label'     => Mage::helper('minislideshow')->__('Slider Width'),
		  'name'      => 'slider_params[size_width]',
		));
		
		$fieldset2->addField('size_height', 'text', array(
		  'label'     => Mage::helper('minislideshow')->__('Slider Height'),
		  'name'      => 'slider_params[size_height]',
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