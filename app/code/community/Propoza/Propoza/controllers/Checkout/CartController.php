<?php
require_once 'Mage/Checkout/controllers/CartController.php';

class Propoza_Propoza_Checkout_CartController extends Mage_Checkout_CartController
{
    public function addProposalAction()
    {
        if (Mage::helper('propoza')->isRequestAuthorized()) {
            $post = Mage::helper('core')->jsonDecode(file_get_contents("php://input"));
            $propozaQuoteId = $post['id'];
            $quoteId = $post['shop_quote_id'];
            $proposalTotal = $post['total_proposal_price'];
            $originalTotal = $post['total_original_price'];
            $discount = $originalTotal - $proposalTotal;
            if (isset($quoteId) && isset($proposalTotal) && isset($originalTotal) && isset($propozaQuoteId)) {
                //Load the original quote by stored id in Propoza
                $quote = $this->_loadQuoteById($quoteId);
                //Clone original so it will not have data mismatch with Propoza
                $clonedQuote = $quote->cloneQuote()->save();

                $propozaProposalRule = Mage::getModel('salesrule/rule')->load($propozaQuoteId, 'propoza_quote_id');
                //Create or edit proposal coupon
                if (!$propozaProposalRule->getId()) {
                    $couponCode = Mage::getModel('salesrule/rule')->createProposalRule($clonedQuote->getId(), $propozaQuoteId, $discount, $quote->getBaseSubtotal())->getCouponCode();
                } else {
                    //Remove inactive quotes with this proposal coupon
                    $quote->deleteAllProposalQuotesClones($propozaProposalRule->getCouponCode());
                    //Edit coupon to match new quote id and subtotal
                    $couponCode = $propozaProposalRule->updateProposalRule($clonedQuote->getId(),  $discount, $quote->getBaseSubtotal())->getCouponCode();
                }

                //Set coupon code to proposal coupon
                $clonedQuote->setCouponCode($couponCode);
                $clonedQuote->save();

                $this->getResponse()->setHeader('Content-type', 'application/json');
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($couponCode));
            } else {
                Mage::throwException(Mage::helper('propoza')->__('Incorrect parameters'));
            }
        } else {
            Mage::throwException(Mage::helper('propoza')->__('You are not authorized to access this function'));
        }
    }

    private function _loadQuoteById($id)
    {
        $quote = Mage::getModel('sales/quote')->loadByIdWithoutStore($id, Mage::getModel('sales/quote')->getIdFieldName());
        if ($quote->getId()) {
            return $quote;
        } else {
            Mage::throwException(Mage::helper('propoza')->__('Quote with %s %s does not exist', Mage::getModel('sales/quote')->getIdFieldName(), $id));
        }
    }


    public function requestCheckoutAction()
    {
        $couponCode = $this->getRequest()->getParam('coupon_code');
        try {
            $quote = Mage::getModel('sales/quote')->loadQuoteByCouponCode($couponCode);
            if ($quote->getId()) {
                $this->_replaceQuote($quote);
                if ($this->_getCart()->getCheckoutSession()->getQuote()->getItemsCount()) {
                    $rule = Mage::getModel('salesrule/rule')->getSalesRuleByCouponCode($quote->getCouponCode());
                    if ($rule->getId()) {
                        if ($rule->getTimesUsed() > 0) {
                            $notice = Mage::helper('propoza')->__('<b>Please note:</b> <i>Proposal is already used. Request a new quote for a new proposal.</i>');
                            $this->_getCart()->getCheckoutSession()->addNotice($notice);

                        } else {
                            $message = Mage::helper('propoza')->__('<b>Great:</b> <i>Your proposal discount has successfully been applied. Check the new Total.</i><br/>');
                            $notice = Mage::helper('propoza')->__('<b>Please note:</b> <i>Changing the cart contents will invalidate the proposal price.</i>');
                            $this->_getCart()->getCheckoutSession()->addSuccess($message);
                            $this->_getCart()->getCheckoutSession()->addNotice($notice);
                        }
                    }else{
                        $error = Mage::helper('propoza')->__('<b>Error:</b> <i>No proposal found for this quote.</i><br/>');
                        $this->_getCart()->getCheckoutSession()->addError($error);
                    }
                }
            } else {
                Mage::throwException(Mage::helper('propoza')->__('Quote with this proposal coupon does not exist'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $this->_redirect('*/*/');
    }

    private function _replaceQuote($quote)
    {
        $quote->setIsActive(true)->save();
        $this->_getCart()->getCheckoutSession()->replaceQuote($quote);
    }
}
