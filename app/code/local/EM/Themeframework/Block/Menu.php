<?php

/**
 * Magento
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
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml menu block
 *
 * @method Mage_Adminhtml_Block_Page_Menu setAdditionalCacheKeyInfo(array $cacheKeyInfo)
 * @method array getAdditionalCacheKeyInfo()
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class EM_Themeframework_Block_Menu extends Mage_Adminhtml_Block_Page_Menu
{
	protected $_acl;
	protected $_adminUser;
	protected $_adminFormKey;
	
	protected function _construct(){
		parent::_construct();
		$this->_url->setNoSecret(false);
	}
	
	public function getEMAdminUserId(){
        try {
            if(array_key_exists('adminhtml', $_COOKIE)){
               $sessionFilePath = Mage::getBaseDir('session').DS.'sess_'.$_COOKIE['adminhtml'];
               $sessionFile = file_get_contents($sessionFilePath);
               $oldSession = $_SESSION;
               session_decode($sessionFile);
               $adminSessionData = $_SESSION;
			   $_SESSION = $oldSession;
               if(array_key_exists('user', $adminSessionData['admin'])){
                  $adminUserObj = $adminSessionData['admin']['user'];
                  $_emAdminUserId = $adminUserObj['user_id'];
				  $this->_adminFormKey = $adminSessionData['core']['_form_key'];
               }
            }
        } catch (Exception $e) {
            return false;
        }
        return $_emAdminUserId;
    }
	
	public function loadAdminUserId(){
		return $this->getEMAdminUserId();
	}
	
    public function getEMAdminToolbarMenu()
    {
		return $this->_buildEMAdminToolbarMenuArray();
    }
	
	public function getAdminUser(){
		if(is_null($this->_adminUser))
			$this->_adminUser = Mage::getModel('admin/user')->load($this->loadAdminUserId());
		return $this->_adminUser;	
	}

	public function getAcl(){
		if(is_null($this->_acl))
			$this->_acl = Mage::getResourceModel('admin/acl')->loadAcl();
		return $this->_acl;	
	}

	public function isAllowed($resource, $privilege = null){
		$user = $this->getAdminUser();
		$acl = $this->getAcl();

		if ($user && $acl) {
			if (!preg_match('/^admin/', $resource)) {
				$resource = 'admin/' . $resource;
			}

			try {
				return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
			} catch (Exception $e) {
				try {
					if (!$acl->has($resource)) {
						return $acl->isAllowed($user->getAclRole(), null, $privilege);
					}
				} catch (Exception $e) { }
			}
		}
		return false;
	}
	
	/**
     * Generate secret key for controller and action based on form key
     *
     * @param string $controller Controller name
     * @param string $action Action name
     * @return string
     */
    public function generateSecretKey($controller = null, $action = null)
    {
        //$salt = Mage::getSingleton('core/session')->getFormKey();
        $salt = $this->_adminFormKey;

        $p = explode('/', trim($this->getRequest()->getOriginalPathInfo(), '/'));
        if (!$controller) {
            $controller = !empty($p[1]) ? $p[1] : $this->getRequest()->getControllerName();
        }
        if (!$action) {
            $action = !empty($p[2]) ? $p[2] : $this->getRequest()->getActionName();
        }

        $secret = $controller . $action . $salt;
        return Mage::helper('core')->getHash($secret);
    }
	
	public function getSecretKey($actionUrl){
		$tmp = explode('/',$actionUrl);
		$controllerName = isset($tmp[1]) ? $tmp[1] : 'index';
		$actionName = isset($tmp[2]) ? $tmp[2] : 'index';
		return $this->generateSecretKey($controllerName,$actionName);
	}
    
    public function getActiveTheme(){
        $activeId = Mage::helper('themeframework/managetheme')->getActivatedTheme();
        return Mage::getModel('themeframework/theme')->load($activeId);      
    }

    protected function _buildEMAdminToolbarMenuArray(Varien_Simplexml_Element $parent = null,$path = '', $level = 0)
    {
        if (is_null($parent)) {
            //$parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
            $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu/emthemes/children');            
        }

        $parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if (1 == $child->disabled || !$this->isAllowed($path . $childName)) {
                continue;
            }
            
            $excludeMenu = array(
                            'emblog',
                            'productlabels',
                            'emquickshop',
                            'onestepcheckout',
                            'multidealpro',
                            'layerednavigation',
                            'emthemes_manager',
                            'emthemeframework'
                            );
            
            if(in_array($childName,$excludeMenu)){
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);
            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string )$child->action, array('_cache_secret_key' => true,'key' => $this->getSecretKey((string )$child->action)));
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $menuArr['active'] = ($this->getActive() == $path . $childName) || (strpos($this->
                getActive(), $path . $childName . '/') === 0);

            $menuArr['level'] = $level;


            if ($child->children) {
                $menuArr['children'] = $this->_buildEMAdminToolbarMenuArray($child->children, $path .
                    $childName . '/', $level + 1);
            }
            $parentArr[$childName] = $menuArr;
            $sortOrder++;
        }
        
        uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }
        if (isset($last)) {
            $parentArr[$last]['last'] = true;
        }

        return $parentArr;
    }
    
    
    protected function _sortMenu($a, $b)
    {
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
    }
    
    public function getSettingsMenu(){
        $themeActive = $this->getActiveTheme();        
        if($themeActive->getThemeId()){
            $actionSettings = 'themeframework_admin/adminhtml_theme/edit/theme_id/'.$themeActive->getThemeId();
            $hrefsettings = Mage::helper('adminhtml')->getUrl(
                (string)$actionSettings,
                array('_cache_secret_key' => true,'key' => $this->getSecretKey((string )$actionSettings))
            );
            //echo $hrefsettings;exit;
        }else{
            $actionSettings = 'themeframework_admin/adminhtml_theme/new/theme/'.Mage::helper('themeframework/managetheme')->urlencode($themeActive->getBaseTheme());
            $hrefsettings = Mage::helper('adminhtml')->getUrl(
                (string)$actionSettings,
                array('_cache_secret_key' => true,'key' => $this->getSecretKey((string )$actionSettings))
            );            
        }        
        $setting = '<li class="em-admintoolbar-level0"><a class="em-admintoolbar-menu-link" href="'.$hrefsettings.'"><span>Theme Settings</span></a></li>';
        return $setting;
    }

    public function getEMAdminToolbarMenuLevel($menu, $level = 0)
    {
        $html = '<ul ' . (!$level ? 'class="em-admintoolbar-menu-list" id="nav_admin_toolbar"' : '') . '>' . PHP_EOL;
        if($level==0){
            $setting = $this->getSettingsMenu();
            $html .= $setting . PHP_EOL;
        }
        foreach ($menu as $item) {
            $html .= '<li ' . (!empty($item['children']) ?
                'onmouseover="Element.addClassName(this,\'over\')" ' .
                'onmouseout="Element.removeClassName(this,\'over\')"' : '') . ' class="' . (!$level &&
                !empty($item['active']) ? ' active' : '') . ' ' . (!empty($item['children']) ?
                ' em-admintoolbar-parent' : '') . (!empty($level) && !empty($item['last']) ? ' last' : '') .
                ' em-admintoolbar-level' . $level . '"> <a href="' . $item['url'] . '" ' . (!empty($item['title']) ?
                'title="' . $item['title'] . '"' : '') . ' ' . (!empty($item['click']) ?
                'onclick="' . $item['click'] . '"' : '') . ' class="em-admintoolbar-menu-link' . ($level === 0 && !empty
                ($item['active']) ? 'active' : '') . '"><span>' . $this->escapeHtml($item['label']) .
                '</span></a>' . PHP_EOL;

            if (!empty($item['children'])) {
                $html .= $this->getEMAdminToolbarMenuLevel($item['children'], $level + 1);
            }
            $html .= '</li>' . PHP_EOL;
        }        
        $html .= '</ul>' . PHP_EOL;

        return $html;
    }
}
