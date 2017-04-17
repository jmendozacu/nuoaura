<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock_Edit_Tab_General_Handle extends Mage_Adminhtml_Block_Widget
{
    protected $_handles = array();

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $params = array();
        if($this->getExtraParams()){
            $params[] = $this->getExtraParams();
        }
        $selectBlock = $this->getLayout()->createBlock('core/html_select')
            ->setName($this->getName())
            ->setId($this->getId())
            ->setOptions($this->getHandles())
            ->setValue($this->getSelected());
        if($this->getDisabled()){
            $params[] = 'disabled';
            $selectBlock->setClass('select disabled');
        } else {
            $selectBlock->setClass('select');
        }
        if(count($params) > 0){
            $selectBlock->setExtraParams(implode(' ',$params));
        }
        return parent::_toHtml().$selectBlock->toHtml();
    }

    /**
     * Retrieve blocks array
     *
     * @return array
     */
    public function getHandles()
    {
        if (empty($this->_handles)) {
            $this->_handles = Mage::getModel('flexibleblock/fblock_attribute_source_layout')
                ->setReady(true)
                ->setPackage($this->getPackage())
                ->setTheme($this->getTheme())
                ->getAllOptions();
        }
        return $this->_handles;
    }


}