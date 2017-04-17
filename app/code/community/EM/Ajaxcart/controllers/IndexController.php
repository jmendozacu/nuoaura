<?php
class EM_Ajaxcart_IndexController extends Mage_Core_Controller_Front_Action
{   
	public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

	/**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

	/**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	
    public function deleteAction()
    {
		$id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                Mage::getSingleton('checkout/cart')->removeItem($id)
                 ->save();
            } catch (Exception $e) {
               Mage::getSingleton('checkout/cart')->addError($this->__('Cannot remove item'));
            }
        }

		$this->loadLayout();
		$layout = $this->getLayout();
		$block_sidebar	=	$layout->getBlock('cart_sidebar');
		$cart   = $this->_getCart();
		$cartBlockLink = $layout->createBlock('checkout/links');
		$topLinkBlock = $layout->createBlock('page/template_links')->setChild('link.cart',$cartBlockLink);
		$cartBlockLink->addCartLink();
		$links = $topLinkBlock->getLinks();
		$link = array_shift($links);

		$arr['total']	= 	strip_tags(Mage::helper('checkout')->formatPrice($cart->getQuote()->getSubtotal()));
		$arr['qty']		= 	$cart->getItemsQty();
		$arr['toplink']	=	$link->getLabel();
		$arr['sidebar']	=	$block_sidebar->toHtml();

		if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT']))
		{	// if IE
			echo json_encode($arr, JSON_HEX_TAG);exit;
		}else{	// other browser
			echo json_encode($arr);exit;
		}
    }

	public function addtocartAction()
    {
        $this->indexAction();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
    }

}