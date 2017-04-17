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

class EM_Themeframework_Block_Adminhtml_Theme_Update extends  Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {   
        $this->_objectId    = 'theme_id';
        $this->_controller  = 'adminhtml_theme';
        $this->_mode = 'update';
        $this->_blockGroup  = 'themeframework';        
        
        parent::__construct();        
        $this->removeButton('reset')
            ->removeButton('delete')
            ->_updateButton('save', 'label', $this->__('Update'))
            ->_updateButton('save', 'id', 'upload_button')
            ->_updateButton('save', 'onclick', 'checkImportData()');
        $this->_formScripts[] = "
            function checkImportData() {
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
        return  $this->getUrl('*/*/', array('theme' => $this->getRequest()->getParam('theme')));      
    }

    public function getHeaderText()
    {
        return Mage::helper('themeframework')->__("Update New Layout %s", $this->escapeHtml(Mage::registry('theme_data')->getBaseTheme()));
    }


}
