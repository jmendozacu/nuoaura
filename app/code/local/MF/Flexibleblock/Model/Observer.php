<?php
require_once Mage::getBaseDir('lib') . DS . 'em' . DS . 'Mobile_Detect.php';
class MF_Flexibleblock_Model_Observer
{
    public function addFBlock(Varien_Event_Observer $observer){
        $update = $observer->getEvent()->getLayout()->getUpdate();
        $handles = $update->getHandles();

        /* Load all handles available in flexible block */
        $allHandles = $this->_loadAllHandles();
        $handles = array_intersect($allHandles, $handles);
        if(count($handles) == 0)
            return $this;

        $packageTheme = array(Mage::getSingleton('core/design_package')->getPackageName() . '/' . Mage::getDesign()->getTheme('template'));
        $obj = new Varien_Object(array('package_theme' => $packageTheme,'exclude_blocks' => array()));
        Mage::dispatchEvent('flexible_block_package',array('object' => $obj));

        $packageTheme = $obj->getPackageTheme();

        $cacheKey = implode('-',$packageTheme).'_'.Mage::app()->getStore()->getId();

        /* Build cache key */
        if(Mage::registry('current_category')){
            $cacheKey .= '_'.Mage::registry('current_category')->getId();
        }
        if(Mage::getSingleton('cms/page')->getPageId()){
            $cacheKey .= '_'.Mage::getSingleton('cms/page')->getPageId();
        }

        /* Mobile Detect */
		$detect = new Mobile_Detect;
		$cacheKey .= '_pc';
		$detectResult = 'pc';
		if( $detect->isMobile() ){
			if ( $detect->isTablet() ) {
				$cacheKey .= '_tablet';
				$detectResult = 'tablet';
			} else {
				$detectResult = 'mobile';
				$cacheKey .= '_mobile';
			}
		}
        $cacheData = $this->_parseXmlFromCache($cacheKey);
        if($cacheData === false || !isset($cacheData['handles']) || !in_array(implode('-',$handles),array_keys($cacheData['handles']))){
            $collection = Mage::getResourceModel('flexibleblock/fblock_collection')->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addAttributeToSort('order','ASC')
                ->addAttributeToFilter('status',1)
                ->addAttributeToFilter('package_theme',array('in' => $packageTheme))
                ->addAttributeToFilter('identifier', array(
                    'notnull' => true,
                ))
                ->addAttributeToFilter(array(
                    array(
                        'attribute' => 'layout_handle',
                        'in'        => $handles
                    ),
                    array(
                        'attribute' => 'layout_handle_2',
                        'in'        => $handles
                    ),
                    array(
                        'attribute' => 'layout_handle_3',
                        'in'        => $handles
                    ),
                    array(
                        'attribute' => 'custom_layout_handle',
                        'in'      => $handles
                    )
                ));

            $excludeBlocks = $obj->getExcludeBlocks();//echo '<pre>';print_r($excludeBlocks);exit;
            if(count($excludeBlocks) > 0){
                $collection->addAttributeToFilter('identifier',array('nin' => $excludeBlocks));
            }

            if(Mage::registry('current_category')){
                $catId = Mage::registry('current_category')->getId();
                $collection->addAttributeToFilter(array(
                    array(
                        'attribute' => 'category_ids',
                        'null'        => NULL,
                    ),
                    array(
                        'attribute' => 'category_ids',
                        'like'      => $catId.'%',
                    ),
                    array(
                        'attribute' => 'category_ids',
                        'like'      => '%'.$catId.'%',
                    ),
                    array(
                        'attribute' => 'category_ids',
                        'like'      => '%'.$catId,
                    )
                ));
            }

            /* From date and to date */
            $todayStartOfDayDate  = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $todayEndOfDayDate  = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $collection->addAttributeToFilter('from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
                ->addAttributeToFilter('to_date', array('or'=> array(
                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left');

            /* Filter by cms page */
            if(Mage::getSingleton('cms/page')->getPageId()){
                $collection->addAttributeToFilter(
                    array(
                        array(
                            'attribute' => 'cms_page',
                            'eq'        => -1
                        ),
                        array(
                            'attribute' => 'cms_page',
                            'eq'      => Mage::getSingleton('cms/page')->getPageId()
                        )
                    )
                );
            }

			if ( $detectResult == 'mobile' ) {
				$collection->addAttributeToFilter('display_mobile',1);
			} else if ( $detectResult == 'tablet' ) {
				$collection->addAttributeToFilter('display_tablet',1);
			} else {
                $collection->addAttributeToFilter('display_pc',1);
			}

            $xmlString = '';
            if($collection->getSize() > 0){
				
				$xmlArray = array();
                $positionKeyList = Mage::helper('flexibleblock')->getPositionKeyList();
                $attributeKeyList = Mage::helper('flexibleblock')->getAttributeKeyList();
                foreach($collection as $block){
					foreach($positionKeyList as $handleKey => $positionKey){
                        $handle = $block->getData($handleKey);
                        $position = $block->getData($positionKey);
                        $layoutAttribute = $block->getData($attributeKeyList[$handleKey]);
                        if($handle && in_array($handle,$handles) && $position){
                            if(!isset($xmlArray[$position])){
                                $xmlArray[$position] = '<reference name="'.$position.'">';
                            }
                            $layoutAttributeStr = (!empty($layoutAttribute)) ? ' '.$layoutAttribute : '';
                            $xmlArray[$position] .= '<block type="flexibleblock/fblock" name="fblock_'.$handleKey.'_'.$block->getIdentifier().'"'.$layoutAttributeStr.'>
                                    <action method="setId"><id>'.$block->getId().'</id></action>
                                </block>';
                        }
                    }
                }
                if(count($xmlArray) > 0){
                    $xmlString = implode('</reference>',$xmlArray).'</reference>';
                }
            }

            /* Prepare data to save in cache */
            if(is_array($cacheData)){
                if(!in_array($xmlString,$cacheData['content']))
                    $cacheData['content'][] = $xmlString;
                $key = array_search($xmlString,$cacheData['content']);
                $cacheData['handles'][implode('-',$handles)] = $key;
            } else {
                $cacheData = array(
                    'handles' => array(implode('-',$handles) => 0),
                    'content'   =>  array(0 => $xmlString)
                );
            }

            $cacheTags = array(MF_Flexibleblock_Model_Fblock::CACHE_TAG);
            $endDaySeconds = strtotime(Mage::app()->getLocale()->date()
                ->setTime('23:59:59')->toString());
            /* from now to end day */
            $daySecondsNow = Mage::app()->getLocale()->storeTimeStamp(Mage::app()->getStore());
            $cacheLifetime = $endDaySeconds - $daySecondsNow;

            $cacheData = serialize($cacheData);
            $this->_saveCache($cacheData,$cacheKey,$cacheTags,$cacheLifetime);
        } else {
            $key = $cacheData['handles'][implode('-',$handles)];
            $xmlString = $cacheData['content'][$key];
        }
        if($xmlString)
            $update->addUpdate($xmlString);
        return $this;
    }

    protected function _loadAllHandles(){
        $allHandles = $this->_loadCache('flexible_block_all_handles',true);
        if($allHandles === false){
            $collection = Mage::getModel('flexibleblock/fblock')->getCollection()->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect(array('layout_handle','layout_handle_2','layout_handle_3','custom_layout_handle'))
                ->addAttributeToFilter('status',1);
            $allHandles = array();
            foreach($collection as $block){
                if($block->getLayoutHandle()){
                    $allHandles[] = $block->getLayoutHandle();
                }
                if($block->getData('layout_handle_2')){
                    $allHandles[] = $block->getData('layout_handle_2');
                }
                if($block->getData('layout_handle_3')){
                    $allHandles[] = $block->getData('layout_handle_3');
                }
                if($block->getCustomLayoutHandle()){
                    $allHandles[] = $block->getCustomLayoutHandle();
                }
            }
            if(count($allHandles) > 0){
                $allHandles = array_unique($allHandles);
            }
            $this->_saveCache(serialize($allHandles),'flexible_block_all_handles',array(MF_Flexibleblock_Model_Fblock::CACHE_TAG),31536000, true);
        } else {
            $allHandles = unserialize($allHandles);
        }
        return $allHandles;
    }

    /**
     * Get xml string from cache by handles
     *
     * @param string $cacheKey
     * @return boolean|string
     */
    protected function _parseXmlFromCache($cacheKey){
        $cacheData = $this->_loadCache($cacheKey);
        if($cacheData === false)
            return false;
        $cacheData = unserialize($cacheData);
        return $cacheData;
    }

    /**
     * Load block html from cache storage
     *
     * @param $cacheKey
     * @param boolean $pass
     * @return string | false
     */
    protected function _loadCache($cacheKey, $pass = false)
    {
        if ((!Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP)) && ($pass == false)) {
            return false;
        }
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');
        $cacheData = Mage::app()->loadCache($cacheKey);
        if ($cacheData) {
            $cacheData = str_replace(
                $this->_getSidPlaceholder($cacheKey),
                $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId(),
                $cacheData
            );
        }
        return $cacheData;
    }

    /**
     * Save block content to cache storage
     *
     * @param string $data
     * @param string $cacheKey
     * @param array $cacheTags
     * @param int $cacheLifetime
     * @param boolean $pass
     * @return MF_Flexibleblock_Model_Observer
     */
    protected function _saveCache($data,$cacheKey,$cacheTags = array(),$cacheLifetime, $pass = false)
    {
        if ((is_null($cacheLifetime) || !Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP)) && ($pass == false)) {
            return false;
        }

        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');
        $data = str_replace(
            $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId(),
            $this->_getSidPlaceholder($cacheKey),
            $data
        );

        Mage::app()->saveCache($data, $cacheKey, $cacheTags, $cacheLifetime);
        return $this;
    }

    /**
     * Get SID placeholder for cache
     *
     * @param null|string $cacheKey
     * @return string
     */
    protected function _getSidPlaceholder($cacheKey = null)
    {
        return '<!--SID=' . $cacheKey . '-->';
    }
}