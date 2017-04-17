<?php
class MF_Flexibleblock_Block_Fblock extends Mage_Cms_Block_Block
{
    /**
     * Cache life time
     *
     * @var integer
     */
    protected $_cacheLifeTime = 86400;

    /**
     * Flexible block model
     *
     * @var MF_Flexibleblock_Model_Fblock
     */
    protected $_fBlock;

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();
        $endDaySeconds = strtotime(Mage::app()->getLocale()->date()
            ->setTime('23:59:59')->toString());
        // from now to end day
        $daySecondsNow = Mage::app()->getLocale()->storeTimeStamp(Mage::app()->getStore());
        $this->setCacheLifeTime($endDaySeconds - $daySecondsNow);
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags(){
        if (!$this->hasData('cache_tags')) {
            $cacheTags = array(
                MF_Flexibleblock_Model_Fblock::CACHE_TAG. "_" . $this->getId(),
                Mage_Catalog_Model_Product::CACHE_TAG,
                Mage_Catalog_Model_Category::CACHE_TAG,
                Mage_Cms_Model_Block::CACHE_TAG
            );
            if($this->getBlock()->getAdditionalCacheTags()){
                $cacheTags = array_merge($cacheTags,explode(',',$this->getBlock()->getAdditionalCacheTags()));
            }
            $this->setData('cache_tags',$cacheTags);
        }
        return parent::getCacheTags();
    }

    /**
     * Get Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        if (!$this->hasData('cache_key')) {
            /* Cache by position */
            $cacheKey = 'fblock_'.Mage::app()->getStore()->getId().'_'.$this->getId();

            /* Cache block on category page */
            /*if(Mage::registry('current_category'))
                $cacheKey .= '_cat_'.Mage::registry('current_category')->getId();*/

            /* Cache block on product detail page*/
            if(Mage::registry('current_product'))
                $cacheKey .= '_p_'.Mage::registry('current_product')->getId();

            /* Cache block on cms page */
            if(in_array('cms_page',$this->getLayout()->getUpdate()->getHandles()))
                $cacheKey .= '_p_'.Mage::getSingleton('cms/page')->getPageId();
            $cacheKey .= '_'.(int)Mage::app()->getStore()->isCurrentlySecure().'_'.Mage::app()->getStore()->getCurrentCurrencyCode().'_'.Mage::getSingleton('customer/session')->getCustomerGroupId();

            $this->setCacheKey($cacheKey);
        }
        return $this->getData('cache_key');
    }

    /**
     * Set cache lifetime
     * @param int $cacheLifeTime
     * @return MF_Flexibleblock_Block_Fblock
     */
    public function setCacheLifeTime($cacheLifeTime){
        $this->_cacheLifeTime = $cacheLifeTime;
        return $this;
    }

    /**
     * Get cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime(){
        return $this->_cacheLifeTime;
    }

    /**
     * Load block html from cache storage
     *
     * @return string | false
     */
    protected function _loadCache()
    {
        if (is_null($this->getCacheLifetime()) || !Mage::app()->useCache(self::CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        $cacheData = Mage::app()->loadCache($cacheKey);

        return $cacheData;
    }

    /**
     * Save block content to cache storage
     *
     * @param string $data
     * @return Mage_Core_Block_Abstract
     */
    protected function _saveCache($data)
    {
        if (is_null($this->getCacheLifetime()) || !Mage::app()->useCache(self::CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        //print_r($this->getCacheTags());exit;
        Mage::app()->saveCache($data, $cacheKey, $this->getCacheTags(), $this->getCacheLifetime());
        return $this;
    }

    /**
     * Get position at frontend
     *
     * @return string
     */
    public function getPosition(){
        return ($this->getData('custom_position')) ? $this->getData('custom_position') : $this->getData('block_position');
    }

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $fBlock = $this->getBlock();
        $html = '';
        if($fBlock->getId()){
            $helper = Mage::helper('cms');
            $processor = $helper->getBlockTemplateProcessor();

            if($fBlock->validate($this))
                $html = $processor->filter($fBlock->getContent());
        }
        //$html .= 'cache lifetime '.$this->getCacheLifetime();
        return $html;
    }

    public function getBlock(){
        if(is_null($this->_fBlock)){
            $fBlock = Mage::getModel('flexibleblock/fblock')->setStoreId(Mage::app()->getStore()->getId());
            if($this->getId())
                $fBlock->load($this->getId());
            $this->_fBlock = $fBlock;
        }
        return $this->_fBlock;
    }
}