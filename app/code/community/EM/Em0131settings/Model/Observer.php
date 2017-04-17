<?php

class EM_Em0131settings_Model_Observer
{
    public function beforeGenerateBlocks(Varien_Event_Observer $observer)
    {
        if ((Mage::getSingleton('core/design_package')->getPackageName() == 'em0131') &&
            (Mage::getDesign()->getTheme('frontend') == 'default')) {
            # Disable default magento navigation
            if (Mage::helper('themeframework/settings')->getGeneral_DisableDefaultNav()!=1 && (Mage::
                getConfig()->getModuleConfig('EM_Megamenupro')->is('active', 'true'))) {
                $blocks = $observer->getLayout()->getXpath('//block[@name="em0131.catalog.topnav"]');
                if (!empty($blocks))
                    $blocks[0]->addAttribute('ignore', true);
            }

            # Disable EM variation module on frontend
            if (Mage::helper('themeframework/settings')->getGeneral_DisableFrontendVariation()!=1 ||
                Mage::helper('themeframework/settings')->checkMobile() == 'true') {
                $blocks = $observer->getLayout()->getXpath('//block[@name="em_variation_tpl" or @name="color_variation"]');
                foreach ($blocks as $block)
                    $block->addAttribute('ignore', true);
            }
            
            # Disable Admin Toolbar
            if (Mage::helper('themeframework/settings')->getGeneral_AdminToolbar()!=1) {
                $blocks = $observer->getLayout()->getXpath('//block[@name="em_admin_toolbar"]');
                foreach ($blocks as $block)
                    $block->addAttribute('ignore', true);
            }
        }
    }

    public function beforeCatalogProductCollectionLoad(Varien_Event_Observer $observer)
    {
        if ((Mage::getSingleton('core/design_package')->getPackageName() == 'em0131') &&
            (Mage::getDesign()->getTheme('frontend') == 'default')) {
            $collection = $observer->getEvent()->getCollection();
            if (!($collection instanceof Mage_Reports_Model_Resource_Product_Collection))
                $observer->getEvent()->getCollection()->addAttributeToSelect('image');
        }
    }

    public function addItemEvent($observer)
    {
        $observer->getHead()->addCSS('em0131/em0131.css');
        $observer->getHead()->addItem('skin_js', 'em0131/em0131.js');
        return $this;
    }
}
