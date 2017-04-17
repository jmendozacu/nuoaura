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

class EM_Themeframework_Block_Adminhtml_Theme_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    const TYPE_IMAGE_THUMBNAIL = 'jpg';
    protected $_defaultLimit    = 20;
    protected $_columnCount    = 4;
    protected $_parentTheme = '';
	protected $_activeTheme = '';
    protected $_scope;
    protected $_scopeId;
    protected $_useDefault;
    protected $_themeActive = false;
    public function __construct()
    {
        parent::__construct();
        $this->setId('themeframeworkThemeGrid');
        $this->setTemplate('em_themeframework/theme/grid.phtml');
    }

    protected function _prepareCollection()
    {
        $this->_initActiveTheme();
        if(Mage::app()->getFrontController()->getRequest()->getParam('theme',0))
            $this->_parentTheme = Mage::app()->getFrontController()->getRequest()->getParam('theme',0);
        $collection = Mage::getModel('themeframework/theme')->getCollection()  			
					->addFieldToFilter('base_theme',$this->_parentTheme);
        if($this->_themeActive != false){
            $collection->addFieldToFilter('theme_id',array('neq' => $this->_themeActive->getId()));
        }
        /* @var $collection EM_Themeframework_Model_Resource_Theme_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }



    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }


    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('theme_id' => $row->getId()));
    }

    public function getParentTheme()
    {
        return $this->_parentTheme;
    }

    public function getColumnCount(){
        return $this->_columnCount;
    }
    public function getThemes(){
        return $this->_prepareCollection()->getData();
    }

 /*   public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }*/
	
	protected function _initActiveTheme()
    {
		if($this->getRequest()->getParam('store') || !$this->getRequest()->getParam('website')){
            $configObject = Mage::app()->getStore($this->getRequest()->getParam('store',Mage_Core_Model_App::ADMIN_STORE_ID));
            $this->_scope = $this->getRequest()->getParam('store') ? 'stores' : 'default';
        } else {
            $configObject = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
            $this->_scope = 'websites';
        }
        $this->_scopeId = $configObject->getId();
        $theme = Mage::getModel('themeframework/theme');
        $activeId = $theme->getActiveConfigData($this->_scope, $this->_scopeId);

        if($activeId && $activeId != 'NULL'){
            $this->_useDefault = false;
        } else {
            $this->_useDefault = true;
        }

        $themId = $configObject->getConfig('theme_framework/theme/active');
        if($themId && $themId != 'NULL')
		{            
			$this->_themeActive = Mage::getModel('themeframework/theme')->load($themId);
            if(!$this->_themeActive->getId())
            {
                $theme->deactivate($this->_scope, $this->_scopeId);
                $this->_themeActive = false;
            }
		}
        return $this;
    }

    public function getActiveTheme(){
        return $this->_themeActive;
    }

    public function getUseDefault(){
        return ($this->_useDefault) && ($this->getScope() != 'default');
    }

    public function getUseDefaultLabel(){
        if($this->getScope() == 'websites')
            return $this->__('Use Default');
        if($this->getScope() == 'stores')
            return $this->__('Use Website');
        return '';
    }

    public function getScope(){
        return $this->_scope;
    }

    public function getActiveUrl($themeId){
        return $this->getActionUrl($themeId);
    }

    public function getDeactivateUrl($themeId){
        return $this->getActionUrl($themeId, 'deactivate');
    }

	public function getActionUrl($themeID, $type="active")
	{
		$url = '';
		if($this->getRequest()->getParam('store', 0) && $this->getRequest()->getParam('website', 0)) 
			$url = Mage::helper('adminhtml')->getUrl('*/themeframework_theme/'.$type,
					array(
						'theme_id'=> $themeID,
						'website'=>$this->getRequest()->getParam('website', 0),
						'store'=>$this->getRequest()->getParam('store', 0)));
		else if($this->getRequest()->getParam('store', 0))
			$url = Mage::helper('adminhtml')->getUrl('*/themeframework_theme/'.$type,
					array(
						'theme_id' => $themeID,
						'store'=>$this->getRequest()->getParam('store', 0)));
		else if($this->getRequest()->getParam('website', 0))
			$url = Mage::helper('adminhtml')->getUrl('*/themeframework_theme/'.$type,
					array(
						'theme_id' => $themeID,
						'website'=>$this->getRequest()->getParam('website', 0)));
		else
			$url = Mage::helper('adminhtml')->getUrl('*/themeframework_theme/'.$type,
					array('theme_id' => $themeID));
		return $url;
						
	}
}
