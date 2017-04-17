<?php
class EM_Minifilterproducts_IndexController extends Mage_Core_Controller_Front_Action
{
    public function ajaxAction()
    {
		$params = unserialize(base64_decode($this->getRequest()->getParam('params')));
		$_ajaxFilterProduct = $this->getLayout()->createBlock('minifilterproducts/listajax')->setData($params);
		$this->getResponse()->setBody($_ajaxFilterProduct->toHtml());
    }
}