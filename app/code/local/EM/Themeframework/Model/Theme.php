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

class EM_Themeframework_Model_Theme extends Mage_Core_Model_Abstract
{
    const CACHE_TAG     = 'themeframework_managethemes';
    protected $_cacheTag= 'themeframework_managethemes';
    const CONFIG_PATH_ACTIVE = 'theme_framework/theme/active';
    const DEFAULT_THEME_NAME = 'default';

    public function _construct()
    {
        $this->_init('themeframework/theme');
    }
	
	/**
     * Active theme in a scope
     *
     * @param string $scope
     * @param int $scopeId
     * @return EM_Themeframework_Model_Theme
     */
	public function activate($scope = 'default', $scopeId = 0){
		$config = Mage::getModel('core/config');
        $this->setPathActive($scope, $scopeId);

        $config->saveConfig('design/package/name', $this->getPackage(),$scope, $scopeId);

        $config->saveConfig('design/theme/skin', $this->getSkin(),$scope, $scopeId);
        $config->saveConfig('design/theme/layout', $this->getLayout(),$scope, $scopeId);
        $config->saveConfig('design/theme/template', $this->getTemplate(),$scope, $scopeId);
        $config->saveConfig('design/theme/default', $this->getDefaultTheme(),$scope, $scopeId);
        //$config->saveConfig('web/default/cms_home_page', $this->getCmsHome(),$scope, $scopeId);
        /* TODO : set cms homepage */

		return $this;
	}

    public function setPathActive($scope = 'default', $scopeId = 0){
        Mage::getModel('core/config')->saveConfig(self::CONFIG_PATH_ACTIVE, $this->getId(),$scope, $scopeId);
        return $this;
    }

    public function unsetPathActive($scope = 'default', $scopeId = 0){
        Mage::getModel('core/config')->deleteConfig(self::CONFIG_PATH_ACTIVE,$scope,$scopeId);
        return $this;
    }
	
	/**
     * Deactivate theme in a scope
     *
     * @param   string $scope
     * @param   int $scopeId
     * @return EM_Themeframework_Model_Theme
     */
	public function deactivate($scope = 'default', $scopeId = 0){
		$config = Mage::getModel('core/config');
        /* TODO : unset cms homepage */
        $this->unsetPathActive($scope,$scopeId);
        $config->deleteConfig('design/package/name',$scope,$scopeId);
        $config->deleteConfig('design/theme/skin',$scope,$scopeId);
        $config->deleteConfig('design/theme/layout',$scope,$scopeId);
        $config->deleteConfig('design/theme/template',$scope,$scopeId);
        $config->deleteConfig('design/theme/default',$scope,$scopeId);
        //$config->deleteConfig('web/default/cms_home_page',$scope,$scopeId);
		return $this;
	}
	
	public function setStoreId($storeId){
		$this->setData($storeId);
		return $this;
	}
	
	/**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }
	
	/**
     * Parse from configuration json
     *
     * @return int
     */
	public function addJsonConfigData(){
		if($this->getConfigJson()){
			$data = Zend_Json::decode($this->getConfigJson());
			$this->addData($data);
		}
		return $this;
	}

    public function getOrgTheme(){
		if(!$this->getIsClone())
			return $this;
		return Mage::getModel('themeframework/theme')->load($this->getBaseTheme(),'identifier');
	}
	
	public function importSampleData($isFirst = false){
        $orgTheme = $this->getOrgTheme();
		if(!$orgTheme->getIsImport()){     
            $isFirst = true;       
			$themeSlug = $orgTheme->getPackage().DS.$orgTheme->getTemplate();
            if(!$orgTheme->getTemplate())
                $themeSlug = $orgTheme->getPackage().DS.self::DEFAULT_THEME_NAME;
			Mage::helper('themeframework/import')->installSampleData($themeSlug,$isFirst);
            $orgTheme->setIsImport(1)->save();
		}
		return $this;
	}

    public function getActiveConfigData($scope, $scopeId){
        return $this->_getResource()->getActiveConfigData($scope, $scopeId);
    }

    public function initActivePath(){
        /* All Store View */
        $scope = 'default';
        $scopeId = Mage_Core_Model_App::ADMIN_STORE_ID;
        $config = Mage::getModel('core/config');
        $config->saveConfig(self::CONFIG_PATH_ACTIVE, 'NULL',$scope, $scopeId);

        foreach (Mage::app()->getWebsites() as $website) {
            /* For website */
            if($this->isUseOtherTheme('websites', $website->getId())){
                $config->saveConfig(self::CONFIG_PATH_ACTIVE, 'NULL','websites', $website->getId());
            }

            /* For store view */
            foreach($website->getStores() as $store){
                if($this->isUseOtherTheme('stores',$store->getId())){
                    $config->saveConfig(self::CONFIG_PATH_ACTIVE,'NULL','stores',$store->getId());
                }
            }
        }
        return $this;
    }

    public function isUseOtherTheme($scope, $scopeId){
        return (count($this->_getResource()->isUseOtherTheme($scope, $scopeId)) > 0);
    }
	
	/**
     * Check theme is activated or not
     *
     * @param $scope
     * @param $scopeId
     * @return boolean
     */
	public function isActive($scope = 'default', $scopeId = 0, $configObject = null){
		if(!$this->getId())
			return false;
		if(is_null($configObject)){
			if($scope == 'stores' || $scope == 'default'){
				$configObject = Mage::app()->getStore($scopeId);
			} else {
				$configObject = Mage::app()->getWebsite($scopeId);
			}
		}
		return ($this->getId() == $configObject->getConfig(self::CONFIG_PATH_ACTIVE,$scope, $scopeId));
	}
}