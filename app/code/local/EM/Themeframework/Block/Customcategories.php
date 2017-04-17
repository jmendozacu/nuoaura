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
 * @package     EM_Embasethemesettings
 * @copyright   Copyright (c) 2014 EMThemes
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



class EM_Themeframework_Block_Customcategories extends Mage_Core_Block_Template
{
    
    protected function _construct()
    {
        $cacheTags[] = 'catalog_category';
        $this->addData(array('cache_lifetime' => 86400));
        $this->addData(array(        
            'cache_tags'        => $cacheTags
        ));        
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return $customCateId = array(
            'CUSTOM_CATEGORIES',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getCurrenSearchCategoryKey()
        );

    }



    // For search by category
    public function getCategoriesCustomSearch($parent,$level){
        $result = '';
        if($parent->getLevel() == 1){
            $result = "<option value='0'>".$this->getCatNameCustom($parent)."</option>";
        }           
        elseif($parent->getLevel() <= $level+1 || $level==0){
            $result = "<option value='".$parent->getId()."' ";

            $curId = $this->getCurrenSearchCategoryKey();
            if($curId){
                if($curId   ==  $parent->getId()) $result .= " selected='selected'";
            }
            $result .= ">".$this->getCatNameCustom($parent)."</option>";            
        }
        
        try{
            $children = $parent->getChildrenCategories();
            
            if(count($children) > 0){
                foreach($children as $cat){
                    $result .= $this->getCategoriesCustomSearch($cat,$level);
                }
            }
        }
        catch(Exception $e){
            return '';
        }        
        return $result;
    }
    
    public function getCatNameCustom($category){
        $level = $category->getLevel();
        $html = '';
        for($i = 0;$i < $level;$i++){
            $html .= '&nbsp;&nbsp;&nbsp;';
        }
        if($level == 1) return $html.' '.Mage::helper('themeframework')->__('All Categories');
        else return $html.' '.$category->getName();
    }

    /**
     * Get current search category
     *
     * @return category_id
     */    

    public function getCurrenSearchCategoryKey()
    {
        $curId = $this->getRequest()->getParam('cat'); 
        if (!$curId) {
            $curId = 0;            
        }        
        return $curId;
    }


    public function renderCategoriesSearchForm()
    {
        $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
        $model  =   Mage::getModel('catalog/category');
        $category = $model->load($rootCategoryId);  
        $setting = Mage::helper('themeframework/settings');
        $level = $setting->getGeneral_CatSearch();

        return $this->getCategoriesCustomSearch($category,$level);
    }




}
