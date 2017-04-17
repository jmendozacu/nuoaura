<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_fblock';
    $this->_blockGroup = 'flexibleblock';
    $this->_headerText = Mage::helper('flexibleblock')->__('Block Manager');
    $this->_addButtonLabel = Mage::helper('flexibleblock')->__('Add Block');
    parent::__construct();
    $this->setTemplate('mf_flexibleblock/fblocks.phtml');
  }

  public function _prepareLayout()
  {
        /**
         * Display store switcher if system has more one store
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->setChild('store_switcher',
                $this->getLayout()->createBlock('adminhtml/store_switcher')
                    ->setUseConfirm(false)
                    ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
            );
        }
        parent::_prepareLayout();
  }
}