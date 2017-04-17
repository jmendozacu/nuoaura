<?php
class EM_Themeframework_Block_Adminhtml_Theme_Update_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function getActionForm(){
    	return  $this->getUrl('*/*/updatePost', array('theme' => $this->getRequest()->getParam('theme')));
    }        
    protected function _prepareForm()
    {
        $maxUploadSize = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));              
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'class' => 'update_form',    
            'action' => $this->getActionForm(),        
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            )
        );        
        $fieldset = $form->addFieldset('update_fieldset', array('legend' => $this->__('Update New Layout')));
        $fieldset->setComment($this->__("If you want to update a new layout for this theme, you need to extract and copy all files in new layout package  to your Magento folder, then finding init.xml file and upload it here to update"));
        $fieldset->addField('update_file', 'file', array(
            'name'      => 'update_file',
            'required'  => true,
            'label'     => $this->__('Upload File'),
            'title'     => $this->__('Upload File'),
            'after_element_html' => $this->__("<p class='note'>Total size of uploadable files must not exceed ".$maxUploadSize."</p>"),
        ));

        $form->setUseContainer(true);
        $this->setForm($form);         
        return parent::_prepareForm();
    }
}



?>