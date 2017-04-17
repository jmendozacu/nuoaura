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
class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Prepare form before rendering HTML
     * Setting Form Fieldsets and fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {        
        $settings = $this->getData('settings');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('setting_');
        $fieldset = $form->addFieldset('settings_fieldset', array('legend' => $this->__('Settings')));            
        if (!$settings->config_json) {
            $notice = "The data of settings is empty. Please make sure that you still continue importing settings for this theme. Click on Import button to import";
        }
        else{
            $notice = 'The settings data in this file is valid, please make sure that you still continue importing settings for this theme. Click on Import button to import';            
            $fieldset->addField('select', 'select', array(
              'label'     => Mage::helper('themeframework')->__('Type Setting'),
              'class'     => 'required-entry',
              'required'  => true,
              'name'      => 'type_setting',
              'onclick' => "",
              'onchange' => "",
              'value'  => '1',
              'values' => array('allsetting' => 'All Settings','typographysetting' => 'Typography Settings (Theme Design)'),              
              'after_element_html' => '<small>Please select type setting to import</small>',
              'tabindex' => 1
            ));
            $fieldset->addField('checkbox', 'checkbox', array(
              'label'     => Mage::helper('themeframework')->__('Import Excluded Blocks List'),
              'name'      => 'type_setting_exclude_block',
              'checked' => false,
              'onclick' => "",
              'onchange' => "",
              'value'  => 'excluded_blocks',
              'disabled' => false,             
              'tabindex' => 2
            ));


        }
        $fieldset->setComment($notice);

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
