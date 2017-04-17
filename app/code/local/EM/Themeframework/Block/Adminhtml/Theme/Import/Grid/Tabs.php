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
 */
class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('themeframework_theme_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('themeframework')->__('Data Type'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function _beforeToHtml()
    {        

        $importType = $this->getRequest()->getParam('import_type');
        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $sampleData = $xmlData->sample_data;         
        if($importType != 2)
        {
            $this->addTab('settings', array(
                'label'     => Mage::helper('themeframework')->__('Settings'),
                'title'     => Mage::helper('themeframework')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tab_settings')
                            ->setData('settings',$xmlData->settings)
                            ->toHtml(),            
            ));
        }
        if($importType != 1)    
        {
            $this->addTab('page', array(
                'label'     => Mage::helper('themeframework')->__('CMS Page'),
                'title'     => Mage::helper('themeframework')->__('CMS Page'),
               /* 'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tab_cmspage')
                            ->setData('data',$sampleData->cmspage)
                            ->toHtml(),*/
                'url'       => $this->getUrl('*/*/importPage', array('_current' => true)),
                'class'     => 'ajax',            
            ));
            $this->addTab('block', array(
                'label'     => Mage::helper('themeframework')->__('Static Block'),
                'title'     => Mage::helper('themeframework')->__('Static Block'),
                /*'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tab_staticblock')
                                ->setData('data',$sampleData->staticblock)
                                ->toHtml(),*/
                'url'       => $this->getUrl('*/*/importBlock', array('_current' => true)),
                'class'     => 'ajax',
            ));
            $this->addTab('layout', array(
                'label'     => Mage::helper('themeframework')->__('Themeframework Layout'),
                'title'     => Mage::helper('themeframework')->__('Themeframework Layout'),
                /*'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tab_layout')
                                    ->setData('data',$sampleData->themeframework)
                                    ->toHtml(),*/
                'url'       => $this->getUrl('*/*/importLayout', array('_current' => true)),
                'class'     => 'ajax',
            ));
            $this->addTab('fblock', array(
                'label'     => Mage::helper('themeframework')->__('Flexible Block'),
                'title'     => Mage::helper('themeframework')->__('Flexible Block'),
                /*'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tab_flexibleblock')
                                ->setData('data',$sampleData->flexible_block)
                                ->toHtml(),*/
                'url'       => $this->getUrl('*/*/importFblock', array('_current' => true)),
                'class'     => 'ajax',
            ));
            $this->addTab('slideshow', array(
                'label'     => Mage::helper('themeframework')->__('Slideshow'),
                'title'     => Mage::helper('themeframework')->__('Slideshow'),
                /*'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tab_slideshow')
                                ->setData('data',$sampleData->slideshows)
                                ->toHtml(),*/
                'url'       => $this->getUrl('*/*/importSlideshow', array('_current' => true)),
                'class'     => 'ajax',
            ));
            $this->addTab('menu', array(
                'label'     => Mage::helper('themeframework')->__('Mega Menu'),
                'title'     => Mage::helper('themeframework')->__('Mega Menu'),
                /*'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tab_megamenu')
                                ->setData('data',$sampleData->megamenu)
                                ->toHtml(),*/
                'url'       => $this->getUrl('*/*/importMegamenu', array('_current' => true)),
                'class'     => 'ajax',
            ));
        }
        return parent::_beforeToHtml();
    }


}