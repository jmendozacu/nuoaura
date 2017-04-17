<?php

class Propoza_Propoza_Model_Sales_Quote extends Mage_Sales_Model_Quote
{
    public function loadQuoteByCouponCode($couponCode)
    {
        return $this->load($couponCode, 'coupon_code');
    }

    public function cloneQuote()
    {
        $clonedQuote = Mage::getModel('sales/quote');
        $clonedQuote->addData($this->getData());
        $clonedQuote->setProposalCoupon(null);
        $clonedQuote->setId(null);
        $clonedQuote->setStoreId(Mage::app()->getStore()->getId());
        $clonedQuote->setIsActive(true);

        foreach ($this->getAllAddresses() as $key => $address) {
            $address->setId(null)->setQuoteId(null);
            $clonedQuote->addAddress($address);
        }

        //customer
        $clonedQuote->assignCustomer($this->getCustomer());

        //items
        foreach ($this->getAllItems() as $item) {
            $item->setId(null)->setQuoteId(null);
            $options = $item->getOptions();
            foreach ($options as $option) {
                $option->setOptionId(null);
            }
            $item->setOptions($options);
            $clonedQuote->addItem($item);
        }
        //payment
        foreach ($this->getPaymentsCollection() as $payment) {
            $clonedQuote->removePayment($payment);
            $clonedQuote->addPayment($payment);
        }

        $clonedQuote->collectTotals();

        return $clonedQuote;
    }

    public function getProposalQuotesClones($couponCode)
    {
        return $this->getCollection()->addFieldToFilter('coupon_code', $couponCode);
    }

    public function deleteAllProposalQuotesClones($couponCode)
    {
        if (Mage::getModel('salesrule/rule')->isPropozaProposalRule($couponCode)) {
            foreach ($this->getProposalQuotesClones($couponCode) as $quote) {
                if ($quote->getIsActive()) {
                    $quote->delete();
                }
            }
        }
    }
}