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

class EM_Themeframework_Block_Adminhtml_Theme_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{


    public function __construct()
    {
        $this->_objectId    = 'theme_id';
        $this->_controller  = 'adminhtml_theme';
        $this->_blockGroup  = 'themeframework';        
        parent::__construct();
        $theme = Mage::registry('theme_data');

        $this->_addButton('save_and_continue', array(
            'label'     => $this->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
            'class'     => 'save',
        ), -5);
		
		$type =Mage::app()->getFrontController()->getRequest()->getParam('type',0);
        if ($type && $type == 'clone') {
            $id = null;
        }
        else
            $id = Mage::app()->getFrontController()->getRequest()->getParam($this->_objectId,0);

        $this->setValidationUrl($this->getUrl('*/*/validate',array($this->_objectId=>$id)));

        $tabsBlockJsObject = 'themeframework_theme_tabsJsTabs';
        $tabsBlockPrefix   = 'themeframework_theme_tabs_';
        $this->_formScripts[] = "
            function saveAsNew() {
                if (editForm.submit($('edit_form').action + 'saveasnew/1/back/edit/')) {};};

            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }

                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});                
                editForm.submit(url);
            };
				
			editForm._processValidationResult = function(transport) {
                var response = transport.responseText.evalJSON();
                if (response.error){
                    if (response.attribute && $(response.attribute)) {
                        $(response.attribute).setHasError(true, editForm);
                        Validation.ajaxError($(response.attribute), response.message);
                        if (!Prototype.Browser.IE){
                            $(response.attribute).focus();
                        }
                    }
                    else if ($('messages')) {
                        $('messages').innerHTML = '<ul class=\"messages\"><li class=\"error-msg\"><ul><li>' + response.message + '</li></ul></li></ul>';
                    }
                }
                else{
                    editForm._submit();
                }
            };
        ";
        $this->_removeButton('reset');
        if (!($this->getRequest()->getParam('type',0)) && isset($theme) && $theme->getId()) {
            if($theme->getIsClone()==1)
            {
                $this->_updateButton('delete', 'label', Mage::helper('themeframework')->__('Delete Theme'));                                

            }
            else
                $this->removeButton('delete');
            $this->_addButton('saveasnew', array(
                'label'     => Mage::helper('themeframework')->__('Clone'),
                'onclick'   => 'setLocation(\'' . $this->getCloneUrl() . '\')',
                'class'     => 'add',
            ), -1);
			
			/* Get current scope, scope id */

            if($this->getRequest()->getParam('store') || !$this->getRequest()->getParam('website')){                
                $scope = $this->getRequest()->getParam('store') ? 'stores' : 'default';
            } else {                
                $scope = 'websites';
            }
            if($scope == 'stores' || $scope == 'default'){
				$configObject = Mage::app()->getStore($this->getRequest()->getParam('store',0));
			} else {
				$configObject = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
			}
			$scopeId = $configObject->getId();
			
			if($theme->isActive($scope, $scopeId, $configObject)){
				$this->_addButton('deactivate', array(
					'label'     => Mage::helper('themeframework')->__('Deactivate'),
					'onclick' => 'setLocation(\'' . $this->getDeactivateUrl() . '\')',
					'class'     => 'save',
				),-50);
			} else {
				$this->_addButton('active', array(
					'label'     => Mage::helper('themeframework')->__('Activate'),
					'onclick' => 'setLocation(\'' . $this->getActiveUrl() . '\')',
					'class'     => 'save',
				),-50);
			}
            $this->_addButton('export', array(
                'label'     => Mage::helper('themeframework')->__('Export'),
                'onclick' => 'setLocation(\'' . $this->getExportUrl() . '\')',
            ), -100);
            $this->_addButton('import', array(
                'label'     => Mage::helper('themeframework')->__('Import'),
                'onclick' => 'setLocation(\'' . $this->getImportUrl() . '\')',
            ), -100);
            /*
            $this->_addButton('resetVariations', array(
                                'label'     => Mage::helper('adminhtml')->__('Reset'),
                                'onclick'   => 'confirmSetLocation(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this? All variations will be loaded default value.')
                    .'\', \'' . $this->getResetVariationsUrl() . '\')',
            ),-100);*/
            /*$this->_addButton('exportSampleData', array(
                                'label'     => Mage::helper('adminhtml')->__('Export Sample Data'),
                                'onclick' => 'setLocation(\'' . $this->getExportSampleDataUrl() . '\')',         
            ),-1);*/

        }
        else
        {
            $this->_removeButton('delete');
            $this->_removeButton('reset');
        }
        
       
    }


	public function getHeaderText()
	{
        $type = Mage::app()->getFrontController()->getRequest()->getParam('type',0);
        if ($type && $type = 'clone')
        {
            return Mage::helper('themeframework')->__('Customize New Theme');
		}
		else {
            return Mage::helper('themeframework')->__("Customize theme %s", $this->escapeHtml(Mage::registry('theme_data')->getThemeName()));
		}
	}

    public function getExportUrl()
    {
        $id = Mage::app()->getFrontController()->getRequest()->getParam($this->_objectId,0);
        return $this->getUrl('*/*/export',array($this->_objectId=>$id));
    }

    public function getImportUrl()
    {
        $id = Mage::app()->getFrontController()->getRequest()->getParam($this->_objectId,0);
        return $this->getUrl('*/*/import',array($this->_objectId=>$id));
    }

    public function getBackUrl()
    {
        $parent_theme = Mage::registry('theme_data')->getBaseTheme();
        return $this->getUrl('*/*/',array('theme'=>$parent_theme));
    }

    public function getCloneUrl()
    {
        $id = Mage::app()->getFrontController()->getRequest()->getParam($this->_objectId,0);
        return $this->getUrl('*/*/edit',array('_current'=>true,'theme_id'=>$id,'type'=>'clone'));
    }
	
	public function getDeactivateUrl(){
		return $this->getUrlActiveWithScope('deactivate');
	}

	public function getActiveUrl(){
		return $this->getUrlActiveWithScope();
	}
	
    public function getUrlActiveWithScope($type = 'active')
    {
        $id = Mage::app()->getFrontController()->getRequest()->getParam($this->_objectId,0);
        
        $url = '';
        if($this->getRequest()->getParam('store', 0) && $this->getRequest()->getParam('website', 0)) 
            $url = Mage::helper('adminhtml')->getUrl('*/adminhtml_theme/'.$type,
                    array(
                        'theme_id'=> $id,
                        'website'=>$this->getRequest()->getParam('website', 0),
                        'store'=>$this->getRequest()->getParam('store', 0)));
        else if($this->getRequest()->getParam('store', 0))
            $url = Mage::helper('adminhtml')->getUrl('*/adminhtml_theme/'.$type,
                    array(
                        'theme_id' => $id,
                        'store'=>$this->getRequest()->getParam('store', 0)));
        else if($this->getRequest()->getParam('website', 0))
            $url = Mage::helper('adminhtml')->getUrl('*/adminhtml_theme/'.$type,
                    array(
                        'theme_id' => $id,
                        'website'=>$this->getRequest()->getParam('website', 0)));
        else
            $url = Mage::helper('adminhtml')->getUrl('*/adminhtml_theme/'.$type,
                    array('theme_id' => $id));
        return $url;            
    }

    public function getResetVariationsUrl()
    {
        $id = Mage::app()->getFrontController()->getRequest()->getParam($this->_objectId,0);
        return $this->getUrl('*/*/reset',array('_current'=>true, 'theme_id'=>$id));
    }


    // export sample data (flexible block) url
    public function getExportSampleDataUrl()
    {
        $id = Mage::app()->getFrontController()->getRequest()->getParam($this->_objectId,0);
        return $this->getUrl('*/*/exportSampleData',array($this->_objectId=>$id));
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }
}