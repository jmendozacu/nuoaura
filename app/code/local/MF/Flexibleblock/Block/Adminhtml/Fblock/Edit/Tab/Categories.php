<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product categories tab
 *
 * @category   MF
 * @package    MF_Flexibleblock
 * @author      magento freelancer <core@magentocommerce.com>
 */
class MF_Flexibleblock_Block_Adminhtml_Fblock_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    /**
     * Specify template to use
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mf_flexibleblock/fblock/edit/categories.phtml');
    }

    /**
     * Retrieve currently edited fblock
     *
     * @return MF_Flexibleblock_Model_Fblock
     */
    public function getProduct()
    {
        return Mage::registry('fblock_data');
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getCategoriesReadonly();
    }

    protected function getCategoryIds()
    {
        return $this->getProduct()->getCategoryIds();
    }

    /**
     * Check "Use default" checkbox display availability
     *
     * @return bool
     */
    public function canDisplayUseDefault()
    {
        return $this->getProduct()->getStoreId();
    }

    /**
     * Check default value usage fact
     *
     * @return bool
     */
    public function usedDefault()
    {
        $attributeCode = 'category_ids';
        $defaultValue = $this->getProduct()->getAttributeDefaultValue($attributeCode);

        if (!$this->getProduct()->getExistsStoreValueFlag($attributeCode)) {
            return true;
        } else if ($this->getProduct()->getValue() == $defaultValue &&
            $this->getProduct()->getStoreId() != $this->_getDefaultStoreId()
        ) {
            return false;
        }
        if ($defaultValue === false && $this->getProduct()->getValue()) {
            return false;
        }
        return $defaultValue === false;
    }

    /**
     * Default sore ID getter
     *
     * @return integer
     */
    protected function _getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }
}