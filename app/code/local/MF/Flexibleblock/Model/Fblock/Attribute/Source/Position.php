<?php
class MF_Flexibleblock_Model_Fblock_Attribute_Source_Position extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_layoutHandlesXml = null;

    protected $_layoutHandleUpdates = array();

    protected $_layoutHandleUpdatesXml = null;

    protected $_layoutHandle = array();

    protected $_blocks = array();

    protected $_allowedBlocks = array();

    protected $_package = array();

    protected $_theme = array();

    protected $_ready = false;

	/*
     * Needed to display block position select in backend
     * @return array Returns multi-level array containin block groups and key => value pairs with 'label' and 'value' keys
     */
    public function getAllOptions()
    {
        if($this->_ready){
            return $this->getBlocks();
        } else {
            return array(
                array('value' => '','label' => Mage::helper('widget')->__('-- Please Select --'))
            );
        }
    }

    /**
     * Setter
     *
     * @param array $allowedBlocks
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Block
     */
    public function setAllowedBlocks($allowedBlocks)
    {
        $this->_allowedBlocks = $allowedBlocks;
        return $this;
    }

    /**
     * Add allowed block
     *
     * @param string $block
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Block
     */
    public function addAllowedBlock($block)
    {
        $this->_allowedBlocks[] = $block;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getAllowedBlocks()
    {
        return $this->_allowedBlocks;
    }

    /**
     * Setter
     * If string given exlopde to array by ',' delimiter
     *
     * @param string|array $layoutHandle
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Block
     */
    public function setLayoutHandle($layoutHandle)
    {
        if (is_string($layoutHandle)) {
            $layoutHandle = explode(',', $layoutHandle);
        }
        $this->_layoutHandle = array_merge(array('default'), (array)$layoutHandle);
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getLayoutHandle()
    {
        return $this->_layoutHandle;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getArea()
    {
        return Mage_Core_Model_Design_Package::DEFAULT_AREA;
    }

    public function setPackage($package){
        $this->_package = $package;
        return $this;
    }

    public function setTheme($theme){
        $this->_theme = $theme;
        return $this;
    }

    public function setReady($value){
        $this->_ready = $value;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getPackage()
    {
        return !is_null($this->_package) ? $this->_package : Mage_Core_Model_Design_Package::DEFAULT_PACKAGE;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getTheme()
    {
        return !is_null($this->_theme) ? $this->_theme : Mage_Core_Model_Design_Package::DEFAULT_THEME;
    }

    /**
     * Retrieve blocks array
     *
     * @return array
     */
    public function getBlocks()
    {
        if (empty($this->_blocks)) {
            /* @var $update Mage_Core_Model_Layout_Update */
            $update = Mage::getModel('core/layout')->getUpdate();
            /* @var $layoutHandles Mage_Core_Model_Layout_Element */
            $this->_layoutHandlesXml = $update->getFileLayoutUpdatesXml(
                $this->getArea(),
                $this->getPackage(),
                $this->getTheme());
            $this->_collectLayoutHandles();
            $this->_collectBlocks();
            array_unshift($this->_blocks, array(
                'value' => '',
                'label' => Mage::helper('widget')->__('-- Please Select --')
            ));
        }
        return $this->_blocks;
    }

    /**
     * Merging layout handles and create xml of merged layout handles
     *
     */
    protected function _collectLayoutHandles()
    {
        foreach ($this->getLayoutHandle() as $handle) {
            $this->_mergeLayoutHandles($handle);
        }
        $updatesStr = '<'.'?xml version="1.0"?'.'><layout>'.implode('', $this->_layoutHandleUpdates).'</layout>';
        $this->_layoutHandleUpdatesXml = simplexml_load_string($updatesStr, 'Varien_Simplexml_Element');
    }

    /**
     * Adding layout handle that specified in node 'update' to general layout handles
     *
     * @param string $handle
     */
    public function _mergeLayoutHandles($handle)
    {
        foreach ($this->_layoutHandlesXml->{$handle} as $updateXml) {
            foreach ($updateXml->children() as $child) {
                if (strtolower($child->getName()) == 'update' && isset($child['handle'])) {
                    $this->_mergeLayoutHandles((string)$child['handle']);
                }
            }
            $this->_layoutHandleUpdates[] = $updateXml->asNiceXml();
        }
    }


    /**
     * Filter and collect blocks into array
     */
    protected function _collectBlocks()
    {
        if ($blocks = $this->_layoutHandleUpdatesXml->xpath('//block/label/..')) {
            /* @var $block Mage_Core_Model_Layout_Element */
            foreach ($blocks as $block) {
                if ((string)$block->getAttribute('name') && $this->_filterBlock($block)) {
                    $helper = Mage::helper(Mage_Core_Model_Layout::findTranslationModuleName($block));
                    $this->_blocks[(string)$block->getAttribute('name')] = $helper->__((string)$block->label);
                }
            }
        }
        asort($this->_blocks, SORT_STRING);
    }

    /**
     * Check whether given block match allowed block types
     *
     * @param Mage_Core_Model_Layout_Element $block
     * @return boolean
     */
    protected function _filterBlock($block)
    {
        if (!$this->getAllowedBlocks()) {
            return true;
        }
        if (in_array((string)$block->getAttribute('name'), $this->getAllowedBlocks())) {
            return true;
        }
        return false;
    }
}