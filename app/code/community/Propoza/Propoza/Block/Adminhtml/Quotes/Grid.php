<?php

class Propoza_Propoza_Block_Adminhtml_Quotes_Grid extends Mage_Adminhtml_Block_Abstract {
    public function getPropozaDashboardToken() {
        if (!Mage::getSingleton('admin/session')->getPropozaDashboardToken()) {
            $client  = new Varien_Http_Client(Mage::helper('propoza')->getDashboardPropozaTokenUrl());
            $api_key = explode(':', base64_decode(Mage::getStoreConfig('propoza/general/api_key')), 2);
            $client->setAuth($api_key[0], $api_key[1], Varien_Http_Client::AUTH_BASIC);
            $client->setMethod(Varien_Http_Client::GET);
            $response = Mage::helper('core')->jsonDecode($client->request()->getBody());
            if (isset($response['response']['token'])) {
                Mage::getSingleton('admin/session')->setPropozaDashboardToken($response['response']['token']);
            }
        }

        return Mage::getSingleton('admin/session')->getPropozaDashboardToken();
    }
}