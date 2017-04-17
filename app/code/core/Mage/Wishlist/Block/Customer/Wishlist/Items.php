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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist block customer items
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Wishlist_Items extends Mage_Core_Block_Template
{
    /**
     * Retreive table column object list
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = array();
        foreach ($this->getSortedChildren() as $code) {
            $child = $this->getChild($code);
            if ($child->isEnabled()){
                $columns[] = $child;
            }
        }
        return $columns;
    }


    public function getCategory(){
         
         $customer = Mage::getSingleton('customer/session')->getCustomer();
         $catArray= array();
         if($customer->getId())
           {
                   $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
                   $wishListItemCollection = $wishlist->getItemCollection();
                   $i= 0;
                   foreach ($wishListItemCollection as $item)
                        {
                            
                            
                            //print_r(get_class_methods($item));
                            //echo $id= $item->getId();
                            //$product = Mage::getModel('catalog/product')->load($_item['product_id']);
                            $test= $item->getProduct();
                            $category_id = $test->getCategoryIds()[0];
                           
                            
                            $_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($category_id);
                            $catArray[$i]= array($category_id,$_cat->getName());  
                              
                            $i++;       
                            
                           
                            
                         
                            

                        }
                  
           }
        
        return $catArray;

       }
}
