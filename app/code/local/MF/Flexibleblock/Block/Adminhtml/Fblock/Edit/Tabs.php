<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('fblock_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('flexibleblock')->__('Block Information'));
  }
  


  protected function _beforeToHtml()
  {
      $this->addTab('general', array(
          'label'     => Mage::helper('flexibleblock')->__('General Information'),
          'title'     => Mage::helper('flexibleblock')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_general')->toHtml(),
      ));

      $this->addTab('content', array(
          'label'     => Mage::helper('flexibleblock')->__('Content'),
          'title'     => Mage::helper('flexibleblock')->__('Content'),
          'content'   => $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_content')->toHtml(),
      ));

      $this->addTab('categories', array(
          'label'     => Mage::helper('catalog')->__('Categories'),
          'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
          'class'     => 'ajax',
      ));

      $this->addTab('schedule', array(
          'label'     => Mage::helper('flexibleblock')->__('Schedule'),
          'title'     => Mage::helper('flexibleblock')->__('Schedule'),
          'content'   => $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_schedule')->toHtml(),
      ));

      $this->addTab('actions', array(
          'label'     => Mage::helper('flexibleblock')->__('Filter Products'),
          'title'     => Mage::helper('flexibleblock')->__('Filter Products'),
          'content'   => $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_conditions')->toHtml(),
      ));
	  
	  $this->addTab('mobile_detect', array(
          'label'     => Mage::helper('flexibleblock')->__('Mobile Detect'),
          'title'     => Mage::helper('flexibleblock')->__('Mobile Detect'),
          'content'   => $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_detect')->toHtml(),
      ));

      /*$this->addTab('categories', array(
          'label'     => Mage::helper('blog')->__('Categories'),
          'url'       => $this->getUrl('*//*///categories', array('_current' => true)),
          'class'     => 'ajax',
      ));*/

      /*$this->addTab('categories', array(
          'label'     => Mage::helper('blog')->__('Categories'),
          'title'     => Mage::helper('blog')->__('Categories'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_post_edit_tab_categories')->toHtml(),
      ));*/

      /*$this->addTab('tabs', array(
          'label'     => Mage::helper('blog')->__('Tags'),
          'title'     => Mage::helper('blog')->__('Tags'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_post_edit_tab_Tag')->toHtml(),
      ));*/
	  
      /*$this->addTab('design', array(
          'label'     => Mage::helper('blog')->__('Design'),
          'title'     => Mage::helper('blog')->__('Design'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_post_edit_tab_design')->toHtml(),
      ));
      $this->addTab('description', array(
          'label'     => Mage::helper('blog')->__('Meta Information'),
          'title'     => Mage::helper('blog')->__('Meta Information'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_post_edit_tab_description')->toHtml(),
      ));
      
      /*$this->addTab('tag', array(
          'label'     => Mage::helper('blog')->__('Tags'),
          'title'     => Mage::helper('blog')->__('Tags'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_post_edit_tab_form')->toHtml(),
      ));*/
     
      return parent::_beforeToHtml();
  }
}