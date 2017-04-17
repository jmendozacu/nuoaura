<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock_Edit_Tab_General_Position extends Mage_Adminhtml_Block_Widget
{
    protected $_blocks = array();

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('core/html_select')
            ->setName($this->getName())
            ->setId($this->getId())
            ->setOptions($this->getBlocks())
            ->setValue($this->getSelected());
        if($this->getDisabled()){
            $selectBlock->setExtraParams('disabled')
            ->setClass('select disabled');
        } else {
            $selectBlock->setClass('select');
        }
        return parent::_toHtml().$selectBlock->toHtml();
    }

    /**
     * Retrieve blocks array
     *
     * @return array
     */
    public function getBlocks()
    {
        if (empty($this->_blocks)) {
            $this->_blocks = Mage::getModel('flexibleblock/fblock_attribute_source_position')
                ->setReady(true)
                ->setPackage($this->getPackage())
                ->setTheme($this->getTheme())
                ->setLayoutHandle($this->getLayoutHandle())->getAllOptions();
        }
        return $this->_blocks;
    }


}