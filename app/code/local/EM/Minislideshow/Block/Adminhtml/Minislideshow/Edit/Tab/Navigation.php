<?php
class EM_Minislideshow_Block_Adminhtml_Minislideshow_Edit_Tab_Navigation extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('minislideshow_navigation', array('legend'=>Mage::helper('minislideshow')->__('Navigation')));
        
        $_nav_enable = $fieldset->addField('nav_enable', 'select', array(
		  'label'     => Mage::helper('minislideshow')->__('Enable Navigation'),
		  'name'      => 'navigation[nav_enable]',
		  'note'	  => Mage::helper('minislideshow')->__('Next & Prev navigation'),
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
        
		$_nav_type = $fieldset->addField('nav_type', 'multiselect', array(
			'label'     => Mage::helper('minislideshow')->__('Navigation Type'),
			'name'      => 'navigation[nav_type]',
			'note'		=> Mage::helper('minislideshow')->__('1,2,3, bullet, thumb... navigation'),
			'values'    => Mage::getModel('minislideshow/navtype')->getOptionArray()
		));

		$_arrows_next = $fieldset->addField('arrows_next', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Next Button Text'),
			'name'      => 'navigation[arrows_next]',
			'note'		=> Mage::helper('minislideshow')->__('Next Button Text'),
			'values'    => 'Next'
		));

		$_arrows_pre = $fieldset->addField('arrows_pre', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Pre Button Text'),
			'name'      => 'navigation[arrows_pre]',
			'note'		=> Mage::helper('minislideshow')->__('Pre Button Text'),
			'values'    => 'Pre'
		));	
        
        $_thumb_width = $fieldset->addField('thumb_width', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Thumbnail Width'),
			'name'      => 'navigation[thumb_width]',
			'note'		=> Mage::helper('minislideshow')->__('Thumbnail Width'),
			'values'    => '100'
		));

		$_thumb_height = $fieldset->addField('thumb_height', 'text', array(
			'label'     => Mage::helper('minislideshow')->__('Thumbnail Height'),
			'name'      => 'navigation[thumb_height]',
			'note'		=> Mage::helper('minislideshow')->__('Thumbnail Height'),
			'values'    => '100'
		));
        
		if ( Mage::getSingleton('adminhtml/session')->getMinislideshowData() ){
			$_data = $form->setValues(Mage::getSingleton('adminhtml/session')->getMinislideshowData());
			Mage::getSingleton('adminhtml/session')->setMinislideshowData(null);
		} elseif ( Mage::registry('minislideshow_data') ) {
			//$_data = $form->setValues(Mage::registry('minislideshow_data')->getData());
            $_data = Mage::registry('minislideshow_data')->getData();
		}
        
        $form->setValues($_data); 
        $this->setForm($form);
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($_nav_enable->getHtmlId(), $_nav_enable->getName())
            ->addFieldMap($_nav_type->getHtmlId(), $_nav_type->getName())
            ->addFieldMap($_arrows_next->getHtmlId(), $_arrows_next->getName())
            ->addFieldMap($_arrows_pre->getHtmlId(), $_arrows_pre->getName())
            ->addFieldMap($_thumb_width->getHtmlId(), $_thumb_width->getName())            
            ->addFieldMap($_thumb_height->getHtmlId(), $_thumb_height->getName())
            ->addFieldDependence(
                $_nav_type->getName(),
                $_nav_enable->getName(),
                'true'
            )
            ->addFieldDependence(
                $_arrows_next->getName(),
                $_nav_enable->getName(),
                'true'
            )
            ->addFieldDependence(
                $_arrows_pre->getName(),
                $_nav_enable->getName(),
                'true'
            )
            ->addFieldDependence(
                $_thumb_width->getName(),
                $_nav_enable->getName(),
                'true'
            )
            ->addFieldDependence(
                $_thumb_height->getName(),
                $_nav_enable->getName(),
                'true'
            )
        );
        
		return parent::_prepareForm();
	}
}