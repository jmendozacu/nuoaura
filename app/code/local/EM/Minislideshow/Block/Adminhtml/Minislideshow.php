<?php
class EM_Minislideshow_Block_Adminhtml_Minislideshow extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_minislideshow';
    $this->_blockGroup = 'minislideshow';
    $this->_headerText = Mage::helper('minislideshow')->__('Slide Manager');
    $this->_addButtonLabel = Mage::helper('minislideshow')->__('Add new slideshow');
    parent::__construct();
  }
}