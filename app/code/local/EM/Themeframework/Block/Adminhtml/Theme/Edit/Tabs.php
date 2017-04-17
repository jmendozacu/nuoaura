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
class EM_Themeframework_Block_Adminhtml_Theme_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('themeframework_theme_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('themeframework')->__('Customize Theme'));
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
        $model = Mage::registry('theme_data');
        
        $parent_theme = $model->getPackage();

        $xml = Mage::helper('themeframework/managetheme')->loadTheme($model->getTemplate(),$parent_theme);
        $formData = $xml->getFormData();


        //$confs = Mage::helper('themeframework/managetheme')->readJsonConfiguration($confs);
        $this->addTab('information', array(
            'label'     => Mage::helper('themeframework')->__('Theme Information'),
            'title'     => Mage::helper('themeframework')->__('Theme Information'),
            'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_edit_tab_information')->toHtml(),
        ));


        foreach($formData as $key => $value)
        {
            $comment = null;
            if(isset($value['comment']))            
                $comment = $value['comment'];
            $this->addTab($key, array(
                'label'     => $value['label'],
                'title'     => $value['label'],
                'content'   => $this->getLayout()->createBlock('themeframework/adminhtml_theme_edit_tab_accordion')
                        ->setData($key,$value['fieldset'])
                        ->setData('comment',$comment)
                        ->setData('fieldset',$key)
                        ->toHtml(),
            ));

        }

        $this->addTab('block', array(
            'label'     => Mage::helper('themeframework')->__('Exclude Fleixble Blocks from BaseTheme and itself'),
            'title'     => Mage::helper('themeframework')->__('Exclude Fleixble Blocks from BaseTheme and itself'),
            'url'       => $this->getUrl('*/*/block', array('_current' => true)),
            'class'     => 'ajax',
        ));

    

        return parent::_beforeToHtml();
    }


}