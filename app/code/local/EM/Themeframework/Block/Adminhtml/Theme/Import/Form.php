<?php
class EM_Themeframework_Block_Adminhtml_Theme_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function getActionForm(){
    	return  $this->getUrl('*/*/importPost', array('theme_id' => $this->getRequest()->getParam('theme_id')));
    }        
    protected function _prepareForm()
    {
        $maxUploadSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));              
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'class' => 'import_form',    
            'action' => $this->getActionForm(),        
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            )
        );        
        $fieldset = $form->addFieldset('import_fieldset', array('legend' => $this->__('Import Theme Settings')));
        $importType = $fieldset->addField('import_type', 'select', array(
            'label'     => Mage::helper('themeframework')->__('Type'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'import_type',          
            'value'  => '1',
            'values' => array('1' => 'Settings','2' => 'Data', '3' => 'Settings and Data'),
            'disabled' => false,
            'readonly' => false,            
            'after_element_html' => $this->__("<p class='note'>Please select type which you want to import</p>"),
            'tabindex' => 1,            
        ));
        $storeId = $fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => 'Select Store View To Import',
            'title'     => 'Store View ID',
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'after_element_html' => $this->__("<p class='note'>Select Store View to import Data</p>"),
        ));   
        
        $fieldset->addField('import_file', 'file', array(
            'name'      => 'import_file',
            'required'  => true,
            'label'     => $this->__('Upload File'),
            'title'     => $this->__('Upload File'),
            'after_element_html' => $this->__("<p class='note'>Total size of uploadable files must not exceed ".$maxUploadSize."</p>"),
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
         $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($storeId->getHtmlId(), $storeId->getName())
            ->addFieldMap($importType->getHtmlId(), $importType->getName())            
            ->addFieldDependence(
                $storeId->getName(),                
                $importType->getName(),                
                array('2','3')
            )            
        );
        return parent::_prepareForm();
    }
}



?>