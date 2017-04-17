<?php
class EM_Themeframework_Block_Adminhtml_Theme_Export_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function getActionForm(){
        return  $this->getUrl('*/*/exportPost', array('theme_id' => $this->getRequest()->getParam('theme_id')));
    }       
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'action' => $this->getActionForm(),
            'id' => 'edit_form',
            'method' => 'post',
            'class' => 'export_form', 
            'enctype' => 'multipart/form-data',            
            ));

        $fieldset = $form->addFieldset('export_fieldset', array('legend' => $this->__('Export Theme')));
        $fieldset->setComment($this->__("If you export sample data of this theme, 
            the data of CMS Pages, Static Blocks, EM MegaMenu, EM Slideshow which have same identifier with this theme will be exported"));
      

        $exportType = $fieldset->addField('export_type', 'select', array(
          'label'     => Mage::helper('themeframework')->__('Type'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'export_type',          
          'value'  => '1',
          'values' => array('1' => 'Settings','2' => 'Data', '3' => 'Settings and Data'),
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => $this->__("<p class='note'><small>Please select content which you want to export</small></p>"),
          'tabindex' => 1
        ));

        $storeId= $fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => 'Select Store View To Export',
            'title'     => 'Store View ID',
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'after_element_html' => $this->__("<p class='note'>Select Store View to export Data</p>"),
        ));  

        $filterData = $fieldset->addField('filter_export', 'multiselect', array(
            'name'      => 'filter_export',
            'label'     => 'Export Data Type',
            'title'     => 'Export Data Type',
            'required'  => true,
            'value'  => array('staticcontent','layout','fblock','menu','slideshow'),
            'values' => array(                
                array('value'=>'staticcontent' , 'label' =>'Static Content (CMS Pages, Static Blocks)'),
                array('value'=>'layout' , 'label' => 'Themeframework Layout'),
                array('value'=>'fblock' , 'label' => 'Flexible Blocks'),
                array('value'=>'menu' , 'label' => 'Mega Menu'),
                array('value'=>'slideshow' , 'label' => 'Slideshows')
                ),             
             'after_element_html' => $this->__("<p class='note'><small>Select Export Data Type To Export</small></p>"),
        ));

                 

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($storeId->getHtmlId(), $storeId->getName())
            ->addFieldMap($exportType->getHtmlId(), $exportType->getName())  
            ->addFieldMap($filterData->getHtmlId(), $filterData->getName())            
            ->addFieldDependence(
                $storeId->getName(),                
                $exportType->getName(),                
                array('2','3')
            ) 
            ->addFieldDependence(
                $filterData->getName(),                
                $exportType->getName(),                
                array('2','3')
            )           
        );
        $form->setUseContainer(true);
        $this->setForm($form);
       

       
         
        return parent::_prepareForm();
    }
}



?>