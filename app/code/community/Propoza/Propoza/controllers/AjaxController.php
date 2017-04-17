<?php
require_once 'Mage/Core/controllers/AjaxController.php';

class Propoza_Propoza_AjaxController extends Mage_Core_AjaxController
{
    public function testConnectionAction()
    {
        $client  = new Varien_Http_Client(Mage::helper('propoza')->getConnectionTestUrl(Mage::helper('propoza')->getSubDomain()));
        $api_key = explode(':', base64_decode(Mage::helper('propoza')->getApiKey()), 2);
        $client->setAuth($api_key[0], $api_key[1], Varien_Http_Client::AUTH_BASIC);
        $client->setMethod(Varien_Http_Client::GET);
        $client->setHeaders('Content-Type', 'application/json');
        $client->request()->getBody();
        $client->setConfig(array('strictredirects' => true));
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($client->request()->getBody());
    }

    public function requestQuoteFormHtmlAction()
    {
        $clonedQuote = Mage::getSingleton('checkout/session')->getQuote()->cloneQuote();
        $clonedQuote->setIsActive(false)->save();

        $client  = new Varien_Http_Client(Mage::helper('propoza')->getQuoteRequestFormUrl());
        $api_key = explode(':', base64_decode(Mage::getStoreConfig('propoza/general/api_key')), 2);
        $client->setAuth($api_key[0], $api_key[1], Varien_Http_Client::AUTH_BASIC);
        $client->setMethod(Varien_Http_Client::POST);
        $client->setHeaders('Content-Type', 'application/json');
        $client->setRawData(json_encode(Mage::helper('propoza/request')->prepareQuoteRequest($clonedQuote)), 'application/json');
        $client->setConfig(array('strictredirects' => true));
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody($client->request()->getBody());
    }

    public function requestQuoteFormSubmitAction()
    {
        $client  = new Varien_Http_Client($this->getRequest()->getPost('form-action'));
        $api_key = explode(':', base64_decode(Mage::getStoreConfig('propoza/general/api_key')), 2);
        $client->setAuth($api_key[0], $api_key[1], Varien_Http_Client::AUTH_BASIC);
        $client->setMethod(Varien_Http_Client::POST);
        $client->setHeaders('Content-Type', 'application/json');
        $client->setRawData(json_encode($this->getRequest()->getPost('data')), 'application/json');
        $client->setConfig(array('strictredirects' => true));
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody($client->request()->getBody());
    }
}
