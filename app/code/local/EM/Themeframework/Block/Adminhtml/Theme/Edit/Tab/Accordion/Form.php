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
class EM_Themeframework_Block_Adminhtml_Theme_Edit_Tab_Accordion_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $reset = Mage::app()->getFrontController()->getRequest()->getParam('reset',0);
        $configDataAdditionalGroups = array();
        //$model = Mage::getModel('themeframework/theme')->load(Mage::app()->getFrontController()->getRequest()->getParam('theme_id',0));
        $model = Mage::registry('theme_data');
		if($model->getId())
        {
            $parent_theme = $model->getBaseTheme();
            $conf_clone_theme = (array)json_decode($model->getConfigJson());
        }

        $mediaPath = $imagePath = Mage::getBaseDir('media') . DS . $model->getBaseTheme().DS.'variations';                        
        $fields = $this->getField();
        $collectionFields = $this->getData($fields);
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('settings');
        $fieldset = $form->addFieldset('themeframework_theme_form_'.$fields,
            array());
        $fieldset->setComment($this->getComment());
        foreach($collectionFields as $key => $value)
        {            
            $required = false;
            $label = '';
            $comment = '';
            $frontend_class = '';
            $fieldType = 'label';
            if(isset($value['frontend_type']) && !empty($value['frontend_type']))
                $fieldType  = (string)$value['frontend_type'] ? (string)$value['frontend_type'] : 'text';
            if(isset($value['label']))
                $label = $value['label'];

            if(isset($value['frontend_class']))
                $frontend_class = $value['frontend_class'];
            if(isset($value['config_path'])){
                $path = (string)$value['config_path'];
                if (empty($path)) {
                    $path = $parent_theme . '/' . $fields . '/' . $key;
                } elseif (strrpos($path, '/') > 0) {
                    // Extend config data with new section group
                    $groupPath = substr($path, 0, strrpos($path, '/'));
                    if (!isset($configDataAdditionalGroups[$groupPath])) {
                        $this->_configData = $this->_configDataObject->extendConfig(
                            $groupPath,
                            false,
                            $this->_configData
                        );
                        $configDataAdditionalGroups[$groupPath] = true;
                    }
                }
            }			
            if(isset($value['required']))
                $required = (bool)$value['required'];

            $field = $fieldset->addField($fields.'_'.$key, $fieldType, array(
                'name'                  => $fields.'_'.$key,
                'label'                 => $label,
                'class'                 => $frontend_class,
                'required'              => $required,                
            ));


            if (isset($value['frontend_model'])) {
                $fieldRenderer = Mage::getBlockSingleton((string)$value['frontend_model']);
                $fieldRenderer->setForm($this);
                $fieldRenderer->setConfigData();
                $field->setRenderer($fieldRenderer);
            }
            if(isset($value['comment']))
                $comment = "<p class='note'>".$value['comment']."</p>";
            $field->setAfterElementHtml($comment);

            if (isset($value['backend_model'])) {
                $modelElement = Mage::getModel((string)$value['backend_model']);
                if (!$modelElement instanceof Mage_Core_Model_Config_Data) {
                    Mage::throwException('Invalid config field backend model: '.(string)$value['backend_model']);
                }
                $data = $modelElement->getValue();
                $field->setValues($data);
            }



            if (isset($value['frontend_type'])
                && 'multiselect' === (string)$value['frontend_type']
                && isset($value['can_be_empty'])
            ) {
                $field->setCanBeEmpty(true);
            }



            if (isset($value['source_model'])) {
                $factoryName = (string)$value['source_model'];
                $method = false;
                if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
                    array_shift($matches);
                    list($factoryName, $method) = array_values($matches);
                }
				
                $sourceModel = Mage::getSingleton($factoryName);
				
                if ($method) {
                    if ($fieldType == 'multiselect') {
                        $optionArray = $sourceModel->$method();
                    } else {
                        $optionArray = array();
                        foreach ($sourceModel->$method() as $value => $label) {
                            $optionArray[] = array('label' => $label, 'value' => $value);
                        }
                    }
                } else {
                    $optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
                }
                $field->setValues($optionArray);

            }

            if($model->getData($fields.'_'.$key)!='')
            {
                if($fieldType=='image')
                    $field->setValue($model->getBaseTheme().'/'.'variations'.'/'.$model->getData($fields.'_'.$key));                        
                else
                    $field->setValue($model->getData($fields.'_'.$key));
            }    
            elseif(isset($value['value']))
                $field->setValue($value['value']);
            

        }
		//$form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();

    }

    public function getNotice($text){
        $msg =  "<ul class='messages'><li class='notice-msg' style='font-size:12px !important'><ul><li>";
        $msg .= Mage::helper('themeframework')->__($text);
        $msg .= "</li></ul></li></ul>";
        return $msg;
    }

}