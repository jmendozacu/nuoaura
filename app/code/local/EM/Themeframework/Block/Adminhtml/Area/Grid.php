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

class EM_Themeframework_Block_Adminhtml_Area_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('themeframeworkAreaGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setTemplate('em_themeframework/area/grid.phtml');
    }

    protected function _prepareCollection()
    {            
        $filter = $this->getParam($this->getVarNameFilter(), null);     
        if (!is_null($filter))  {
             //decode the filter
             $filter = $this->helper('adminhtml')->prepareFilterString($filter);                             
        }
        else
            $filter['store_id'] = 0;
        //echo "<pre>";print_r($filter);
        if (!isset($filter['package_theme']) && !isset($filter['area_id']) 
            && !isset($filter['name'])&& !isset($filter['layout'])) {            
            $configObject = Mage::app()->getStore($filter['store_id']);
            $scope = ($filter['store_id']==0) ? 'default' : 'stores';        
            $scopeId = $configObject->getId();
            $theme = Mage::getModel('themeframework/theme');
            $activeId = $theme->getActiveConfigData($scope, $scopeId);
            $themId = $configObject->getConfig('theme_framework/theme/active');
            
            if($themId && $themId != 'NULL')
            {                         
                $themeActive = Mage::getModel('themeframework/theme')->load($themId);
                $theme = $themeActive->getTemplate() ? $themeActive->getTemplate() : $themeActive->getDefaultTheme();
                if(!$theme)
                    $theme = EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME;
                $package_theme = $themeActive->getPackage().'/'.$theme;
                $filter['package_theme'] = $package_theme;
                $this->_setFilterValues($filter);      
                $collection = Mage::getModel('themeframework/area')->getCollection()->addFieldToFilter('package_theme',$package_theme);
            }
        }   
        
        if(!$collection)
            $collection = Mage::getModel('themeframework/area')->getCollection();    
        
        
        /* @var $collection EM_Themeframework_Model_Resource_Area_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('area_id', array(
            'header'    => Mage::helper('themeframework')->__('ID'),
            'align'     => 'left',
            'index'     => 'area_id',
			'width' =>50
        ));

		$this->addColumn('package_theme', array(
			'header'    => Mage::helper('themeframework')->__('Package/Theme'),
			'align'     => 'left',
			'index'     => 'package_theme',
            'type'      => 'theme',
            'with_empty' => true,

		));

        $this->addColumn('name', array(
            'header'    => Mage::helper('themeframework')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

		$this->addColumn('layout', array(
			'header'    => Mage::helper('themeframework')->__('Layout'),
			'align'     => 'left',
			'index'     => 'layout',
		));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('themeframework')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('themeframework')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('themeframework')->__('Disabled'),
                1 => Mage::helper('themeframework')->__('Enabled')
            ),
        ));

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('themeframework')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('themeframework')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));


        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('area_id' => $row->getId()));
    }

}
