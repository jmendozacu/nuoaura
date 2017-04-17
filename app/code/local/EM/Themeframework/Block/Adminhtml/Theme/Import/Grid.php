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

class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid extends  Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {     
        $this->_objectId    = 'theme_id';
        $this->_controller  = 'adminhtml_theme';
        $this->_mode = 'import_grid';
        $this->_blockGroup  = 'themeframework';        
        
        parent::__construct();        
        $this->removeButton('reset')
            ->removeButton('delete')
            ->_updateButton('save', 'label', $this->__('Import'))
            ->_updateButton('save', 'id', 'upload_button')
            ->_updateButton('save', 'onclick', 'submitImportData()');
        $this->_formScripts[] = "
            function submitImportData() {
                if (editForm.submit()) {
                    var loaderArea = $$('#html-body .wrapper')[0]; // Blocks all page
                    Position.clone($(loaderArea), $('loading-mask'), {offsetLeft:-2});
                    toggleSelectsUnderBlock($('loading-mask'), false);
                    Element.show('loading-mask');
                };
            };
          
        ";
    
    }
    public function getBackUrl()
    {
        return  $this->getUrl('*/*/import', array('theme_id' => $this->getRequest()->getParam('theme_id')));      
    }

    public function getHeaderText()
    {        
        return Mage::helper('themeframework')->__('Filter Import Data');        
    }
    
   public function getHeaderHtml()
    {
        $fBase = Mage::getSingleton('admin/session')->getData('basetheme');        
        if(!$fBase)
            $msg = Mage::helper('themeframework')->__('Unfortunately, the theme information in chosen file which you want to import does not match with this theme, you should make sure that you chose correct file.');
        else
            $msg = Mage::helper('themeframework')->__('Congratulations, the theme information in chosen file which you want to import matchs with this theme, please continue selecting data which you want to import.');
        $msgHtml = '<ul class="messages">
                    <li class="notice-msg" style="font-size:12px !important">
                        <ul>
                            <li>'.$msg.'</li>
                        </ul>
                    </li>
                </ul>';
        

        return $msgHtml.'<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }
    
    public function getValidateUrl(){
        return  $this->getUrl('*/*/validateImportPost', array('theme_id' => $this->getRequest()->getParam('theme_id')));   
    }

}
