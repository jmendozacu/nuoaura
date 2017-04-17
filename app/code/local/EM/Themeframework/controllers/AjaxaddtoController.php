<?php
class EM_Themeframework_AjaxaddtoController extends Mage_Core_Controller_Front_Action
{   
	public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

	protected function _getWishlist()
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }
 
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
            ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
            Mage::helper('wishlist')->__('Can not create wishlist.')
            );
            return false;
        }
 
        return $wishlist;
    }

	
    public function wishlistAction()
    {
		$id = $this->getRequest()->getParam('product');
        $response = array();
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $response['status'] = 0;
            $response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
        }
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $response['status'] = 0;
            $response['message'] = $this->__('Please Login First');
        }
 
        if(empty($response)){
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $response['status'] = 0;
                $response['message'] = $this->__('Unable to Create Wishlist');
            }else{
 
                $productId = (int) $this->getRequest()->getParam('product');
                if (!$productId) {
                    $response['status'] = 0;
                    $response['message'] = $this->__('Product Not Found');
                }else{
 
                    $product = Mage::getModel('catalog/product')->load($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $response['status'] = 0;
                        $response['message'] = $this->__('Cannot specify product.');
                    }else{
 
                        try {
                            $requestParams = $this->getRequest()->getParams();
                            if ($session->getBeforeWishlistRequest()) {
                                $requestParams = $session->getBeforeWishlistRequest();
                                $session->unsBeforeWishlistRequest();
                            }
                            $buyRequest = new Varien_Object($requestParams);
 
                            $result = $wishlist->addNewItem($product, $buyRequest);
                            if (is_string($result)) {
                                Mage::throwException($result);
                            }
                            $wishlist->save();
 
                            Mage::dispatchEvent(
                                'wishlist_add_product',
                            array(
                                'wishlist'  => $wishlist,
                                'product'   => $product,
                                'item'      => $result
                            )
                            );
 
                            Mage::helper('wishlist')->calculate();
 
                            $message = $this->__('%1$s has been added to your wishlist.', $product->getName());
                            $response['status'] = 1;
                            $response['message'] = $message;
 
                            Mage::unregister('wishlist');
 
                            $this->loadLayout();
                            $toplink = $this->getLayout()->getBlock('top.links.wishlist')->toHtml();
                            $sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
                            $sidebar = $sidebar_block->toHtml();
                            $response['toplink'] = $toplink;
                            $response['sidebar'] = $sidebar;
                        }
                        catch (Mage_Core_Exception $e) {
                            $response['status'] = 0;
                            $response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
                        }
                        catch (Exception $e) {
                            Mage::log($e->getMessage());
                            $response['status'] = 0;
                            $response['message'] = $this->__('An error occurred while adding item to wishlist.');
                        }
                    }
                }
            }
 
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    public function compareAction(){
        $response = array(); 
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
 
            if ($product->getId()) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                $response['status'] = 1;
                $response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
                Mage::register('referrer_url', $this->_getRefererUrl());
                Mage::helper('catalog/product_compare')->calculate();
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
                $this->loadLayout();
                $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');                
                $sidebar = $sidebar_block->toHtml();
                $response['sidebar'] = $sidebar;
            }
            else{
                $response['status'] = 0;
                $response['message'] = $this->__('Product Not Found');
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    /**
     * Remove item from compare list
     */
    public function removeCompareAction()
    {
        $response = array();
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    $response['status'] = 1;
                    $response['message'] = $this->__('The product %s has been removed from comparison list.', $product->getName());

                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();

                    $this->loadLayout();
                    $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');                
                    $sidebar = $sidebar_block->toHtml();
                    $response['sidebar'] = $sidebar;
                }

            }
            else{
                $response['status'] = 0;
                $response['message'] = $this->__('Product Not Found');
            }
        }

        /*if (!$this->getRequest()->getParam('isAjax', false)) {
            $this->_redirectReferer();
        }*/
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    /**
     * Remove all items from comparison list
     */
    public function clearCompareAction()
    {
        $response = array();
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $response['status'] = 1;
            $response['message'] = $this->__('The comparison list was cleared.');            
            $this->loadLayout();
            $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');                
            $sidebar = $sidebar_block->toHtml();
            $response['sidebar'] = $sidebar;
            Mage::helper('catalog/product_compare')->calculate();
        } catch (Mage_Core_Exception $e) {
            $response['status'] = 0;
            $response['message'] = $this->__('An error occurred while clearing comparison list.');            
            
        } catch (Exception $e) {
            $response['status'] = 0;
            $response['message'] = $this->__('An error occurred while clearing comparison list.');                                    
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
        //$this->_redirectReferer();
    }
}