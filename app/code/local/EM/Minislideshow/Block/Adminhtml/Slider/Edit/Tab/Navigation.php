<?php
class EM_Minislideshow_Block_Adminhtml_Slider_Edit_Tab_Navigation extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('minislideshow_navigation', array('legend'=>Mage::helper('minislideshow')->__('Navigation')));

		$fieldset->addField('nav_type', 'select', array(
			'label'     => Mage::helper('minislideshow')->__('Navigation Type'),
			'name'      => 'navigation[nav_type]',
			'note'		=> Mage::helper('minislideshow')->__('Display type of the navigation bar (Default:none)'),
			'values'    => Mage::getModel('minislideshow/navtype')->getOptionArray()
		));

		$fieldset->addField('arrows_next', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Next Button Text'),
			'name'      => 'navigation[arrows_next]',
			'note'		=> Mage::helper('minislideshow')->__('Next Button Text'),
			'values'    => 'Next'
		));

		$fieldset->addField('arrows_pre', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Pre Button Text'),
			'name'      => 'navigation[arrows_pre]',
			'note'		=> Mage::helper('minislideshow')->__('Pre Button Text'),
			'values'    => 'Pre'
		));	
        
        $fieldset->addField('thumb_width', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Thumbnail Width'),
			'name'      => 'navigation[thumb_width]',
			'note'		=> Mage::helper('minislideshow')->__('Thumbnail Width'),
			'values'    => 100
		));

		$fieldset->addField('thumb_height', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Thumbnail Height'),
			'name'      => 'navigation[thumb_height]',
			'note'		=> Mage::helper('minislideshow')->__('Thumbnail Height'),
			'values'    => 100
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