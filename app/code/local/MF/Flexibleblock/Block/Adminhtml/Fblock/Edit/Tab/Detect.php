<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock_Edit_Tab_Detect extends MF_Flexibleblock_Block_Adminhtml_Element_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$form->setDataObject(Mage::registry('fblock_data'));
		$fieldset = $form->addFieldset('detect_form', array('legend'=>Mage::helper('flexibleblock')->__('Mobile Detect')));
        $form->setHtmlIdPrefix('fblock_');
        $form->setHtmlNamePrefix('fblock_');

        $attributes = $this->getAttributes(Mage::registry('fblock_data'));

		$this->_setFieldset($attributes,$fieldset, array_diff($this->getAttrCodeArray('fblock_attr_code_array'), array(
			'display_pc','display_tablet','display_mobile'
		)));
		
		if ( Mage::getSingleton('adminhtml/session')->getFblockData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getFblockData());
			Mage::getSingleton('adminhtml/session')->setFblockData(null);
		} elseif ( Mage::registry('fblock_data') ) {
			$form->setValues(Mage::registry('fblock_data')->getData());
		}
		return parent::_prepareForm();
	}
}