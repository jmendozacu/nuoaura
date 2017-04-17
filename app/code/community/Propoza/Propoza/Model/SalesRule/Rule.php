<?php

class Propoza_Propoza_Model_Salesrule_Rule extends Mage_SalesRule_Model_Rule
{
    /**
     * Get Shopping cart rule by coupon code
     * @param $couponCode
     * @return Mage_Salerule_Rule
     */
    public function getSalesRuleByCouponCode($couponCode)
    {
        $coupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
        $rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId(), 'rule_id');

        return $rule;
    }

    /**
     * Create a shopping cart rule for proposal
     * @param $quoteId
     * @param $proposalTotal
     * @param $originalTotal
     * @return string
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function createProposalRule($quoteId, $propozaQuoteId, $proposalTotal, $originalTotal)
    {
        $couponCode = Mage_SalesRule_Model_Rule::getCouponCodeGenerator()->generateCode();
        $actionConditionSubTotal = Mage::getModel('salesrule/rule_condition_address')->setType('salesrule/rule_condition_address')->setAttribute('base_subtotal')->setOperator('==')->setValue($originalTotal);
        $actionConditionQuoteId = Mage::getModel('salesrule/rule_condition_address')->setType('salesrule/rule_condition_address')->setAttribute('quote_id')->setOperator('==')->setValue($quoteId);

        $this->setName(Mage::helper('propoza')->__('Propoza proposal for quote #%s', $propozaQuoteId));
        $this->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC);
        $this->setCouponCode($couponCode);
        $this->setPropozaQuoteId($propozaQuoteId);
        $this->setUsesPerCoupon(1);
        $this->setUsesPerCustomer(1);
        $this->setCustomerGroupIds(Mage::getModel('customer/group')->getCollection()->getAllIds());
        $this->setIsActive(1);
        $this->getActions()->addCondition($actionConditionSubTotal);
        $this->getActions()->addCondition($actionConditionQuoteId);
        $this->setDescription(null);
        $this->setFromDate(null);
        $this->setToDate(null);
        $this->setProductIds(null);
        $this->setSortOrder(null);
        $this->setSimpleAction(Mage_SalesRule_Model_Rule::CART_FIXED_ACTION);
        $this->setDiscountAmount($proposalTotal);
        $this->setDiscountQty(0);
        $this->setDiscountStep(0);
        $this->setApplyToShipping(0);
        $this->setSimpleFreeShipping(0);
        $this->setStopRulesProcessing(1);
        $this->setTimesUsed(0);
        $this->setIsRss(0);
        $this->setStoreLabels(array('Proposal'));
        $this->setWebsiteIds(Mage::getModel('core/website')->getCollection()->getAllIds());
        $validateResult = $this->validateData(new Varien_Object($this->getData()));
        if ($validateResult !== true) {
            Mage::throwException(Mage::helper('propoza')->__('Coupon rule not validated'));
        }
        $this->save();

        return $this;
    }

    public function updateProposalRule($quoteId, $proposalTotal, $originalTotal)
    {
        $actionConditionSubTotal = Mage::getModel('salesrule/rule_condition_address')->setType('salesrule/rule_condition_address')->setAttribute('base_subtotal')->setOperator('==')->setValue($originalTotal);
        $actionConditionQuoteId = Mage::getModel('salesrule/rule_condition_address')->setType('salesrule/rule_condition_address')->setAttribute('quote_id')->setOperator('==')->setValue($quoteId);
        $this->getActions()->setConditions(null);
        $this->getActions()->addCondition($actionConditionSubTotal);
        $this->getActions()->addCondition($actionConditionQuoteId);
        $this->setDiscountAmount($proposalTotal);
        $validateResult = $this->validateData(new Varien_Object($this->getData()));
        if ($validateResult !== true) {
            Mage::throwException(Mage::helper('propoza')->__('Coupon rule not validated'));
        }
        $this->save();

        return $this;
    }


    public function isPropozaProposalRule($couponCode)
    {
        return $this->getSalesRuleByCouponCode($couponCode)->getPropozaQuoteId() != null;
    }
}