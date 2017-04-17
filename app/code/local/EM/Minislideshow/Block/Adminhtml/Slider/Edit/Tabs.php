<?php

class EM_Minislideshow_Block_Adminhtml_Slider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('minislideshow_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('minislideshow')->__('Slideshow Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_general', array(
          'label'     => Mage::helper('minislideshow')->__('General'),
          'title'     => Mage::helper('minislideshow')->__('General'),
          'content'   => $this->getLayout()->createBlock('minislideshow/adminhtml_slider_edit_tab_general')->toHtml(),
      ));
	  $this->addTab('form_images', array(
          'label'     => Mage::helper('minislideshow')->__('Images'),
          'title'     => Mage::helper('minislideshow')->__('Images'),
          'content'   => $this->getLayout()->createBlock('minislideshow/adminhtml_slider_edit_tab_images')->toHtml(),
      ));
	  $this->addTab('form_appearance', array(
          'label'     => Mage::helper('minislideshow')->__('Animation'),
          'title'     => Mage::helper('minislideshow')->__('Animation'),
          'content'   => $this->getLayout()->createBlock('minislideshow/adminhtml_slider_edit_tab_appearance')->toHtml(),
      ));
	  $this->addTab('form_navigation', array(
          'label'     => Mage::helper('minislideshow')->__('Navigation'),
          'title'     => Mage::helper('minislideshow')->__('Navigation'),
          'content'   => $this->getLayout()->createBlock('minislideshow/adminhtml_slider_edit_tab_navigation')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}