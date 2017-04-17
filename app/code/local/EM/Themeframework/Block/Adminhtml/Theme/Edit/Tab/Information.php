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
class EM_Themeframework_Block_Adminhtml_Theme_Edit_Tab_Information
    extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Prepare form before rendering HTML
     * Setting Form Fieldsets and fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('theme_data');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('info_');
        $model = Mage::getModel('themeframework/theme')->load(Mage::app()->getFrontController()->getRequest()->getParam('theme_id',0));
        $id = $model->getId();
        $type = Mage::app()->getFrontController()->getRequest()->getParam('type',0);


        $fieldset = $form->addFieldset('information_fieldset', array('legend' => $this->__('Theme Information')));
        $fieldset->setComment('Details information about theme');
        if ($model->getId() && !$type) {
            $fieldset->addField('theme_id', 'hidden', array(
                'name'  => 'theme_id'
            ));
        }
        $fieldset->addField('package', 'hidden', array(
            'name'  => 'package'
        ));
        $fieldset->addField('template', 'hidden', array(
            'name'  => 'template'
        ));
        $fieldset->addField('layout', 'hidden', array(
            'name'  => 'layout'
        ));
        $fieldset->addField('skin', 'hidden', array(
            'name'  => 'skin'
        ));
        $fieldset->addField('default_theme', 'hidden', array(
            'name'  => 'default_theme'
        ));
		 $fieldset->addField('is_clone', 'hidden', array(
            'name'  => 'is_clone'
        ));
        $fieldset->addField('is_import', 'hidden', array(
            'name'  => 'is_import'
        ));
        $fieldset->addField('base_theme', 'hidden', array(
            'name'  => 'base_theme'
        ));

        $fieldset->addField('theme_name', 'text', array(
            'name'      => 'theme_name',
            'label'     => $this->__('Theme Name'),
            'title'     => $this->__('Theme Name'),
            'maxlength' => '250',
            'required'  => true,
        ));

        
		if($type && $type == 'clone'){
            //$model->setId();			
            $model->setThemeName('');
            $model->setIdentifier('');
			$model->setIsClone(1);	
            $model->setPath('');            	
        }
        if($model->getIsClone()==0)        
        {    
            $identifierField = $fieldset->addField('identifier', 'note', array(
                'name'      => 'identifier',
                'label'     => $this->__('Identifier'),
                'title'     => $this->__('Identifier'),         
                'text'      => $model->getIdentifier(),
            ));
        }
        else
        {
            $identifierField = $fieldset->addField('identifier', 'text', array(
                'name'      => 'identifier',
                'label'     => $this->__('Identifier'),
                'title'     => $this->__('Identifier'),
                'maxlength' => '250',
                'required'  => true,
                'class'     => 'validate-xml-identifier',            
            ));
        }
		
        $fieldset->addField('parent_theme', 'note', array(            
            'label'     => $this->__('Base Theme'),
            'title'     => $this->__('Base Theme'),
            'text'      => $model->getBaseTheme(),
            
        ));

        // show package theme
        $pack = $model->getPackage();
        $template = $model->getTemplate();
        if($template != '')
        {
            //echo $model->getBaseTheme();exit;
            $text = $pack.' / '.$template;              
            $pkParent = Mage::getModel('themeframework/theme')->load($model->getBaseTheme(),'identifier');                        
            $text2 = $pkParent->getTemplate() ? $pkParent->getTemplate() : $pkParent->getDefaultTheme();
            if(!$text2)
                $text2 = EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME;
            $text2 = $pkParent->getPackage().' / '.$text2;
            $packageText = $text2.', </br>'.$text;

        }
        else
            $packageText = $pack.' / '. ($model->getDefaultTheme() ? $model->getDefaultTheme() : EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME);                   
        $fieldset->addField('package_theme', 'note', array(
            'text' =>  $packageText,            
            'label'     => $this->__('Package'),
            'title'     => $this->__('Package'),

        ));
        $thumb = $model->getPath();
		
        $fieldImage = $fieldset->addField('thumbnail', 'image', array(
            'name'  => 'thumbnail',
            'label'     => $this->__('Preview'),
        ));



        
        $form->setValues($model->getData());
        if($thumb && $thumb!='')                        
        {
            $model->setPath($thumb);
            $fieldImage->setValue(Mage::helper("themeframework/managetheme")->resizeImage($model->getBaseTheme(),NULL,NULL,$model->getPath(),'thumbnail'));
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
