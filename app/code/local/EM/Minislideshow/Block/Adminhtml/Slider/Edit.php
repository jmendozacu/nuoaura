<?php
class EM_Minislideshow_Block_Adminhtml_Slider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'minislideshow';
        $this->_controller = 'adminhtml_slider';
        
        //$this->_updateButton('save', 'label', Mage::helper('minislideshow')->__('Save slideshow'));
		$this->_removeButton('save');
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/save')){
			$this->_addButton('save', array(
				'label'     => Mage::helper('minislideshow')->__('Save slideshow'),
				'onclick'   => 'saveEdit()',
				'class'     => 'save',
			), -100);
			
			
			
			$this->_addButton('saveandcontinue', array(
				'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
				'onclick'   => 'saveAndContinueEdit()',
				'class'     => 'save',
			), -100);
		}	

		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/delete')){
			$this->_updateButton('delete', 'label', Mage::helper('minislideshow')->__('Delete slideshow'));	
		} else 
			$this->_removeButton('delete');
		

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('minislideshow_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'minislideshow_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'minislideshow_content');
                }
            }

            function saveAndContinueEdit(){
				var frm	=	jQuery('#edit_form');
				var disabled = frm.find(':input:disabled').removeAttr('disabled');
                editForm.submit($('edit_form').action+'back/edit/');
            }
			
			function saveEdit(){
				var frm	=	jQuery('#edit_form');
				var disabled = frm.find(':input:disabled').removeAttr('disabled');
                editForm.submit($('edit_form').action);
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('minislideshow_data') && Mage::registry('minislideshow_data')->getId() ) {
            return Mage::helper('minislideshow')->__("Edit slideshow '%s'", $this->htmlEscape(Mage::registry('minislideshow_data')->getName()));
        } else {
            return Mage::helper('minislideshow')->__('Add slideshow');
        }
    }
}