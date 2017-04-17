<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock_Edit_Tab_General extends MF_Flexibleblock_Block_Adminhtml_Element_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$form->setDataObject(Mage::registry('fblock_data'));
		$fieldset = $form->addFieldset('general_form', array('legend'=>Mage::helper('flexibleblock')->__('General information')));
        $form->setHtmlIdPrefix('fblock_');
        $form->setHtmlNamePrefix('fblock_');

        $attributes = $this->getAttributes(Mage::registry('fblock_data'));

		$this->_setFieldset($attributes,$fieldset, array_diff($this->getAttrCodeArray('fblock_attr_code_array'), array(
			'title','identifier','cms_page','order','additional_cache_tags','status','package_theme'
		)));
		$fieldset->addField('store_id', 'hidden', array(
			  'name'    => 'store',
		  ));
		$fieldset->addField('entity_id', 'hidden', array(
			  'name'    => 'entity_id',
		  ));

        $fieldSetLayout1 = $form->addFieldset('layout_update_form_1', array('legend'=>Mage::helper('flexibleblock')->__('Layout Update 1')));
        $this->_setFieldset($attributes,$fieldSetLayout1, array_diff($this->getAttrCodeArray('fblock_attr_code_array'), array(
            'layout_handle','position','layout_attribute'
        )));

        $fieldSetLayout2 = $form->addFieldset('layout_update_form_2', array('legend'=>Mage::helper('flexibleblock')->__('Layout Update 2')));
        $this->_setFieldset($attributes,$fieldSetLayout2, array_diff($this->getAttrCodeArray('fblock_attr_code_array'), array(
            'layout_handle_2','position_2','layout_attribute_2'
        )));

        $fieldSetLayout3 = $form->addFieldset('layout_update_form_3', array('legend'=>Mage::helper('flexibleblock')->__('Layout Update 3')));
        $this->_setFieldset($attributes,$fieldSetLayout3, array_diff($this->getAttrCodeArray('fblock_attr_code_array'), array(
            'layout_handle_3','position_3','layout_attribute_3'
        )));

        $fieldSetLayout4 = $form->addFieldset('custom_layout_update_form', array('legend'=>Mage::helper('flexibleblock')->__('Custom Layout Update')));
        $this->_setFieldset($attributes,$fieldSetLayout4, array_diff($this->getAttrCodeArray('fblock_attr_code_array'), array(
            'custom_layout_handle','custom_position','layout_attribute_custom'
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