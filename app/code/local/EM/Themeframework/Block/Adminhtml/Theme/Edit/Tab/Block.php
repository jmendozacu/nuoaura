<?php
/**
 * EMThemes
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the framework to newer
 * versions in the future. If you wish to customize the framework for your
 * needs please refer to http://www.emthemes.com/ for more information.
 *
 * @category    EM
 * @package     EM_ThemeFramework
 * @copyright   Copyright (c) 2012 CodeSpot JSC. (http://www.emthemes.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Giao L. Trinh (giao.trinh@emthemes.com)
 */
class EM_Themeframework_Block_Adminhtml_Theme_Edit_Tab_Block extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_grid');
        $this->setDefaultSort('identifier');
        $this->setUseAjax(true);
        if (Mage::registry('theme_data')->getId()) {
            $this->setDefaultFilter(array('in_blocks' => 1));
        }
    }


    public function getGridHeader(){
        $msg =  "<ul class='messages'><li class='notice-msg' style='font-size:12px !important'><ul><li>";
        $msg .= Mage::helper('themeframework')->__('This theme inherits Flexible Blocks (EMThemes > EM Manage Blocks) of theme. 
                You can remove these blocks on this theme by choosing blocks below');
        $msg .= "</li></ul></li></ul>";
        return $msg;
    }
    protected function _getTheme()
    {
        return Mage::registry('theme_data');
    }

	public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/blockGrid', array('_current' => true));
    }
	
    protected function _prepareCollection()
    {

        $curTheme = Mage::registry('theme_data');
		$package = $curTheme->getPackage();		
		$defaultTheme = $curTheme->getTemplate();
		$packagegDesign1 = $package.'/'.$defaultTheme;
        $pkParent = Mage::getModel('themeframework/theme')->load($curTheme->getBaseTheme(),'identifier');
        $theme2 = $pkParent->getTemplate() ? $pkParent->getTemplate() : $pkParent->getDefaultTheme();
        if(!$theme2)
            $theme2 = EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME;
        $packagegDesign2 = $pkParent->getPackage().'/'.$theme2;
        $packageTheme = array($packagegDesign1,$packagegDesign2);

        $collection = Mage::getModel('flexibleblock/fblock')->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToSort('order','ASC')
			->addAttributeToFilter('status',1)
			->addAttributeToFilter('package_theme',array('in'=>$packageTheme))
			->addAttributeToFilter('identifier', array(
				'notnull' => true,
			));		
		//$collection->addFieldToFilter('entity_id', array('in' => $blockIds));
		
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }




    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
		$this->addColumn('in_blocks', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_blocks',
                'values'            => $this->_getExcludedBlocks(),
                'align'             => 'center',
                'index'             => 'identifier',
				'use_index'			=>	true
            ));
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('flexibleblock')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'entity_id',
        ));
		$this->addColumn('identifier_block', array(
            'header'    => Mage::helper('flexibleblock')->__('Identifier'),
            'align'     =>'left',            
            'index'     => 'identifier',
        ));
        $this->addColumn('title', array(
            'header'    => Mage::helper('flexibleblock')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
        $this->addColumn('package_theme', array(
            'header'    => Mage::helper('widget')->__('Design Package/Theme'),
            'align'     => 'left',
            'index'     => 'package_theme',
            'type'      => 'theme',
            'with_empty' => true,
        ));
        return parent::_prepareColumns();
    }

	protected function _addColumnFilterToCollection($column)
    {
        
        if ($column->getId() == 'in_blocks') {
            $blockIdentifier = $this->_getExcludedBlocks();
            if (empty($blockIdentifier)) {
                $blockIdentifier = null;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('identifier', array('in' => $blockIdentifier));
            } else {
                if($blockIdentifier) {
                    $this->getCollection()->addFieldToFilter('identifier', array('nin' => $blockIdentifier));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
	
	public function _getExcludedBlocks()
    {
		
        $arrBlocks = $this->getExcludedBlocks();
        $reload = $this->getReload();
		if (!is_array($arrBlocks) && !$reload) 
		{	
			if(Mage::registry('theme_data')->getExcludedBlocks()!='')
			{
				$arrBlocks = explode('&',Mage::registry('theme_data')->getExcludedBlocks());					
			}		
		}      		
		return $arrBlocks;
    }
	

	
}
