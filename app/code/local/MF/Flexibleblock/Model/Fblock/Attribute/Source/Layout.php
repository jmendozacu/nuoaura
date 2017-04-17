<?php
class MF_Flexibleblock_Model_Fblock_Attribute_Source_Layout extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_layoutHandles = array();
    protected $_area;
    protected $_package;
    protected $_theme;
    protected $_ready = false;

    /**
     * layout handles wildcar patterns
     *
     * @var array
     */
    protected $_layoutHandlePatterns = array(
        '^default$',
        '^catalog_category_*',
        '^catalog_product_*',
        '^PRODUCT_*'
    );

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Add not allowed layout handle pattern
     *
     * @param string $pattern
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout
     */
    public function addLayoutHandlePattern($pattern)
    {
        $this->_layoutHandlePatterns[] = $pattern;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getLayoutHandlePatterns()
    {
        return $this->_layoutHandlePatterns;
    }

    public function setReady($value){
        $this->_ready = $value;
        return $this;
    }

    public function setArea($area){
        $this->_area = $area;
        return $this;
    }

    public function setPackage($package){
        $this->_package = $package;
        return $this;
    }

    public function setTheme($theme){
        $this->_theme = $theme;
        return $this;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getArea()
    {
        if(!$this->_area)
            $this->_area = Mage_Core_Model_Design_Package::DEFAULT_AREA;
        return $this->_area;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getPackage()
    {
        if(!$this->_package)
            $this->_package = Mage_Core_Model_Design_Package::DEFAULT_PACKAGE;
        return $this->_package;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getTheme()
    {
        if(!$this->_theme)
            $this->_theme = Mage_Core_Model_Design_Package::DEFAULT_THEME;
        return $this->_theme;
    }

    /**
     * Retrieve layout handles
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @return array
     */
    public function getLayoutHandles($area, $package, $theme)
    {
        if (empty($this->_layoutHandles)) {
            /* @var $update Mage_Core_Model_Layout_Update */
            $update = Mage::getModel('core/layout')->getUpdate();
            $this->_collectLayoutHandles($update->getFileLayoutUpdatesXml($area, $package, $theme));
        }
        return $this->_layoutHandles;
    }

    /**
     * Filter and collect layout handles into array
     *
     * @param Mage_Core_Model_Layout_Element $layoutHandles
     */
    protected function _collectLayoutHandles($layoutHandles)
    {
        if ($layoutHandlesArr = $layoutHandles->xpath('/*/*/label/..')) {
            foreach ($layoutHandlesArr as $node) {
                if ($this->_filterLayoutHandle($node->getName())) {
                    $helper = Mage::helper(Mage_Core_Model_Layout::findTranslationModuleName($node));
                    $this->_layoutHandles[$node->getName()] = Mage::helper('core')->jsQuoteEscape(
                        $helper->__((string)$node->label)
                    );
                }
            }
            asort($this->_layoutHandles, SORT_STRING);

        }
    }

    /**
     * Check if given layout handle allowed (do not match not allowed patterns)
     *
     * @param string $layoutHandle
     * @return boolean
     */
    protected function _filterLayoutHandle($layoutHandle)
    {
        $wildCard = '/('.implode(')|(', $this->getLayoutHandlePatterns()).')/';
        if (preg_match($wildCard, $layoutHandle)) {
            return false;
        }
        return true;
    }

    public function getAllOptions(){
        if(!$this->_ready)
            return array(array(
                'value' =>  '',
                'label' =>  Mage::helper('widget')->__('-- Please Select --')
            ));

        $result = array(
            array(
                'value' =>  '',
                'label' =>  Mage::helper('widget')->__('-- Please Select --')
            ),
            array(
                'label' => Mage::helper('widget')->__('Categories'),
                'value' => array(
                    array(
                        'value' => 'catalog_category_layered',
                        'label' => Mage::helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Anchor Categories'))
                    ),
                    array(
                        'value' => 'catalog_category_default',
                        'label' => Mage::helper('core')->jsQuoteEscape(Mage::helper('widget')->__('Non-Anchor Categories'))
                    )
                )
            ),
        );

        $productTypes = array();
        foreach (Mage_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $productTypes[] = array(
                'value' => 'PRODUCT_TYPE_'.$typeId,
                'label' => Mage::helper('core')->jsQuoteEscape($type['label'])
            );
        }
        array_unshift($productTypes, array(
            'value' => 'catalog_product_view',
            'label' => Mage::helper('core')->jsQuoteEscape(Mage::helper('widget')->__('All Product Types'))
        ));

        $result[] = array(
            'label' =>  Mage::helper('widget')->__('Products'),
            'value' =>  $productTypes
        );

        $pages = array(
            array(
                'value' =>  'default',
                'label' =>  Mage::helper('widget')->__('All Pages')
            )
        );

        foreach($this->getLayoutHandles($this->getArea(),$this->getPackage(),$this->getTheme()) as $value => $label){
            $pages[] = array(
                'value' =>  $value,
                'label' =>  $label
            );
        }

        $result[] = array(
            'label' =>  Mage::helper('widget')->__('Generic Pages'),
            'value' =>  $pages
        );
        return $result;
    }
}