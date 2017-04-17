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
 * @package     Mage_Page
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class EM_Em0131settings_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    public function addEMResposiveRtlCss()
    {
        if (Mage::helper('themeframework/settings')->getGeneral_DisableResponsive() != 1) {
            if (Mage::helper('themeframework/settings')->getGeneral_RightToLeft() != 1) {
                $this->addItem('skin_css', 'css/bootstrap/noneresponsive.css');
            } else {
                $this->addItem('skin_css', 'css/bootstrap/noneresponsive-rtl.css');
                $this->addItem('skin_css', 'css/style-rtl.css');
            }
        } else {
            $this->addItem('skin_css', 'css/responsive.css');
            if (Mage::helper('themeframework/settings')->getGeneral_RightToLeft() != 1) {
                $this->addItem('skin_css', 'css/bootstrap/bootstrap.css');
            } else {
                $this->addItem('skin_css', 'css/bootstrap/bootstrap-rtl.css');
                $this->addItem('skin_css', 'css/style-rtl.css');
            }
        }
    }
    public function addEMItem($type, $name, $params = null, $if = null, $cond = null)
    {
        if ($type == 'lesscss') {
			if(Mage::app()->getStore()->isCurrentlySecure() != 1){
				$_lessCacheId = 'em0131_less_cache_store_'.Mage::app()->getStore()->getCode();
				$_newLessConfig = Mage::helper('em0131settings')->getAllCssConfig();
				$_hasChange = 1;
				$pathFile = Mage::getBaseDir('media') . DS . 'em0131' . DS . 'css' . DS . 'less' . DS . 'variables_store_'.Mage::app()->getStore()->getCode().'.less';									
				if (false !== ($data = Mage::app()->getCache()->load($_lessCacheId))) {
					$_oldLessConfig = unserialize($data);	
					if (count(array_diff_assoc($_oldLessConfig,$_newLessConfig)) == 0 && count(array_diff_assoc($_newLessConfig,$_oldLessConfig)) == 0) {
						$_hasChange = 0; 
					}else{
						$_hasChange = 1;				
						Mage::app()->getCache()->remove($_lessCacheId);
						Mage::app()->getCache()->save(serialize($_newLessConfig), $_lessCacheId, array('EM0131_LESS_CSS_CACHE'), 86400);		
					}
				} else {
					$_hasChange = 1;	
					Mage::app()->getCache()->save(serialize($_newLessConfig), $_lessCacheId, array('EM0131_LESS_CSS_CACHE'), 86400);
				}
				
				if ($_hasChange == 1 || !file_exists($pathFile)) {	
					$stringless = '';
					if (isset($_newLessConfig['@function_url'])) {
						foreach (explode(',', $_newLessConfig['@function_url']) as $file) {						
							$stringless .= '@import ' . $file . ';';
						}
					}
					if (isset($_newLessConfig['@variables_url'])) {
						foreach (explode(',', $_newLessConfig['@variables_url']) as $file) {
							$stringless .= '@import ' . $file . ';';
						}
					}
					if (isset($_newLessConfig['additional_css_file'])) {
						foreach ($_newLessConfig['additional_css_file'] as $file) {
							$stringless .= '@import ' . $file . ';';
						}
					}
		
					foreach ($_newLessConfig as $typo => $value) {
						if ($typo != 'additional_css_file') {
							$stringless .= $typo . ":" . $value . ';';
						}
		
					}
					file_put_contents($pathFile, $stringless);
				}
				$href = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/em0131/css/less/variables_store_'.Mage::app()->getStore()->getCode().'.less';
				$this->addItem('link_rel', $href, 'rel="stylesheet/less" type="text/css"');
			}else{
				$_htpps_lessCacheId = 'em0131_https_less_cache_store_'.Mage::app()->getStore()->getCode();
				$_htpps_newLessConfig = Mage::helper('em0131settings')->getAllCssConfig();
				$_htpps_hasChange = 1;
				$_htpps_pathFile = Mage::getBaseDir('media') . DS . 'em0131' . DS . 'css' . DS . 'less' . DS . 'https_variables_store_'.Mage::app()->getStore()->getCode().'.less';					
				if (false !== ($_https_data = Mage::app()->getCache()->load($_htpps_lessCacheId))) {
					$_https_oldLessConfig = unserialize($_https_data);	
					if (count(array_diff_assoc($_https_oldLessConfig,$_htpps_newLessConfig)) == 0 && count(array_diff_assoc($_htpps_newLessConfig,$_https_oldLessConfig)) == 0) {
						$_htpps_hasChange = 0; 
					}else{
						$_htpps_hasChange = 1;				
						Mage::app()->getCache()->remove($_htpps_lessCacheId);
						Mage::app()->getCache()->save(serialize($_htpps_newLessConfig), $_htpps_lessCacheId, array('EM0131_HTTPS_LESS_CSS_CACHE'), 86400);		
					}
				} else {
					$_htpps_hasChange = 1;	
					Mage::app()->getCache()->save(serialize($_htpps_newLessConfig), $_htpps_lessCacheId, array('EM0131_HTTPS_LESS_CSS_CACHE'), 86400);
				}
				
				if ($_htpps_hasChange == 1 || !file_exists($_htpps_pathFile)) {	
					$_https_stringless = '';
					if (isset($_htpps_newLessConfig['@function_url'])) {
						foreach (explode(',', $_htpps_newLessConfig['@function_url']) as $_https_file) {						
							$_https_stringless .= '@import ' . $_https_file . ';';
						}
					}
					if (isset($_htpps_newLessConfig['@variables_url'])) {
						foreach (explode(',', $_htpps_newLessConfig['@variables_url']) as $_https_file) {
							$_https_stringless .= '@import ' . $_https_file . ';';
						}
					}
					if (isset($_htpps_newLessConfig['additional_css_file'])) {
						foreach ($_htpps_newLessConfig['additional_css_file'] as $_https_file) {
							$_https_stringless .= '@import ' . $_https_file . ';';
						}
					}
		
					foreach ($_htpps_newLessConfig as $typo => $_https_value) {
						if ($typo != 'additional_css_file') {
							$_https_stringless .= $typo . ":" . $_https_value . ';';
						}
		
					}
					file_put_contents($_htpps_pathFile, $_https_stringless);
				}
				
				$_https_href = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/em0131/css/less/https_variables_store_'.Mage::app()->getStore()->getCode().'.less';
				$this->addItem('link_rel', $_https_href, 'rel="stylesheet/less" type="text/css"');
			}

        } else {
            if ($type == 'em_js') {
                $href = $this->getSkinUrl($name);
                $this->addItem($type, $href, $params, $if, $cond);
            } else {
                if ($if != null) {
                    if (strcmp('configswatches_general_enabled', $if) == 0) {
                        if (Mage::getStoreConfig('configswatches/general/enabled') == $cond) {
                            $this->addItem($type, $name);
                        }
                    } else {
                        if (Mage::helper('themeframework/managetheme')->getConfigTheme($if) == $cond) {
                                $this->addItem($type, $name);
                            }
                    }

                } else
                    $this->addItem($type, $name, $params, $if, $cond);
            }

        }
    }

    protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams,
        $itemName, $itemThe)
    {
        $params = $itemParams ? ' ' . $itemParams : '';
        $href = $itemName;
        switch ($itemType) {
            case 'rss':
                $lines[$itemIf]['other'][] = sprintf('<link href="%s"%s rel="alternate" type="application/rss+xml" />',
                    $href, $params);
                break;
            case 'link_rel':
                $lines[$itemIf]['other'][] = sprintf('<link%s href="%s" />', $params, $href);
                break;

            case 'em_js':
                $lines[$itemIf]['other'][] = sprintf('<script type="text/javascript" src="%s" %s></script>',
                    $href, $params);
                break;

        }
    }


}
