<?php
class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {                
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'class' => 'em_settings',
                'action' => $this->getUrl('*/*/importSampleData', array(
                    'theme_id' => $this->getRequest()->getParam('theme_id'),
                    'import_type' => $this->getRequest()->getParam('import_type'),
                    'store_id'  => $this->getRequest()->getParam('store_id')
                    )),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}



?>