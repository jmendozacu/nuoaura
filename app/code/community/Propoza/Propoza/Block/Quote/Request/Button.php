<?php

class Propoza_Propoza_Block_Quote_Request_Button extends Mage_Core_Block_Template
{
    public function isDisabled()
    {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function isPossibleOnepageCheckout()
    {
        return $this->helper('checkout')->canOnepageCheckout();
    }

    public function isProposalQuote()
    {
        return Mage::getModel('salesrule/rule')->isPropozaProposalRule(Mage::getSingleton('checkout/session')->getQuote()->getCouponCode());
    }

    public function isEnabled()
    {
        return $this->isPossibleOnepageCheckout() && !$this->isProposalQuote() && Mage::helper('propoza')->isConfigured();
    }

    public function getRequestQuoteFormHtmlUrl()
    {
        return Mage::getUrl('propoza/ajax/requestQuoteFormHtml', array('_secure' => Mage::app()->getStore()->isCurrentlySecure()));
    }

    public function getRequestQuoteFormSubmitUrl()
    {
        return Mage::getUrl('propoza/ajax/requestQuoteFormSubmit', array('_secure' => Mage::app()->getStore()->isCurrentlySecure()));
    }

    public function addPropozaAssets($type, $relativePath)
    {

        if (strpos($relativePath, '/') != 0) {
            $relativePath = sprintf('%s/', $relativePath);
        }
        $path = sprintf('%s%s', Mage::helper('propoza')->getDashboardPropozaUrl(), $relativePath);

        return $this->addExternalLink($type, $path);
    }

    public function addExternalLink($type, $path, $script = '')
    {
        $head = $this->getLayout()->getBlock('head');

        switch ($type) {
            case'css':
                $tag = sprintf('<link rel="stylesheet" type="text/css" href="%s"/>', $path);
                break;
            case 'javascript':
                $tag = sprintf('<script type="text/javascript" src="%s"></script>', $path, $script);
                if (!empty($script)) {
                    $tag .= sprintf('<script>%s</script>', $script);
                }
                break;
        }

        $block = $this->getLayout()->createBlock('Mage_Core_Block_Text')->setText($tag);
        $head->append($block);

        return $this;
    }

    public function addJQuery($script, $cdn)
    {
        $fullPath = Mage::getBaseDir() . '/js/' . $script;
        if (!file_exists($fullPath)) {
            return $this->addExternalLink('javascript', $cdn, '$j = jQuery.noConflict();');
        }

        return $this->getLayout()->getBlock('head')->addJs($script);
    }
}
