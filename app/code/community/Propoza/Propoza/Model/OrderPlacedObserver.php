<?php


class Propoza_Propoza_Model_OrderPlacedObserver
{
    public function checkProposalQuoteCompleted($observer)
    {
        $couponCode = $observer->getEvent()->getOrder()->getCouponCode();
        $rule = Mage::getModel('salesrule/rule')->getSalesRuleByCouponCode($couponCode);
        if ($rule->isPropozaProposalRule($couponCode)) {
            $client = new Varien_Http_Client(Mage::helper('propoza/request')->getQuoteOrderedUrl());
            $api_key = explode(':', base64_decode(Mage::getStoreConfig('propoza/general/api_key')), 2);
            $client->setAuth($api_key[0], $api_key[1], Varien_Http_Client::AUTH_BASIC);
            $client->setMethod(Varien_Http_Client::POST);
            $client->setHeaders('Content-Type', 'application/json');
            $client->setRawData(json_encode(array('Quote' => array('id' => $rule->getPropozaQuoteId(), 'main_status_id' => 2, 'sub_status_id' => 5))), 'application/json');
            $response = $client->request();
            if (!$response->isSuccessful()) {
                $body = Mage::helper('core')->jsonDecode($response->getBody());
                Mage::logException('Set quote ordered request failed' . $body['message']);
            } else {
                Mage::getModel('sales/quote')->deleteAllProposalQuotesClones($couponCode);
                $rule->delete();
            }
        }
    }
}