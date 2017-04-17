<?php
class MF_Flexibleblock_Model_Fblock extends MF_Flexibleblock_Model_Abstract
{
    /**
     * Used in saving cache
     */
    const CACHE_TAG              = 'flexibleblock_fblock';

    /**
	* Maps to the array key from Setup.php::getDefaultEntities()
	*/
    const ENTITY = 'flexibleblock_fblock';

    /**
     * Model fblock prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'flexibleblock';

    protected $_cacheTag = 'flexibleblock_fblock';

    /**
     * Name of the fblock object
     *
     * @var string
     */
    protected $_eventObject = '';

    /**
     * Initialize fblock model
     */
    function _construct()
    {
        $this->_init('flexibleblock/fblock');
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    /*public function getCacheTags(){
        if (!$this->hasData('cache_tags')) {
            $tags = array(self::CACHE_TAG);
            if($this->getOldPosition())
                $tags[] = self::CACHE_TAG. "_" . $this->getOldPosition();
            $tags[] = self::CACHE_TAG. "_" . $this->getPosition();
            $this->setData('cache_tags',$tags);
        }
        return $this->getData('cache_tags');
    }*/
	
	/**
     * Return store id.
     *
     * If store id is underfined for category return current active store id
     *
     * @return integer
     */
    public function getStoreId()
    {
		if($this->_getData('store_id'))
            return $this->_getData('store_id');
        return Mage::app()->getRequest()->getParam('store',Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * Set assigned category IDs array to product
     *
     * @param array|string $ids
     * @return MF_Flexibleblock_Model_Fblock
     */
    public function setCategoryIds($ids)
    {
        if(!is_null($ids)){
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            } elseif (!is_array($ids)) {
                Mage::throwException(Mage::helper('catalog')->__('Invalid category IDs.'));
            }
            foreach ($ids as $i => $v) {
                if (empty($v)) {
                    unset($ids[$i]);
                }
            }
        }

        $this->setData('category_ids', $ids);
        return $this;
    }

    /**
     * Retrieve assigned category Ids
     *
     * @return array
     */
    public function getCategoryIds(){
        if($catIds = $this->getData('category_ids')){
            if(is_string($catIds))
                return explode(',',$catIds);
            return $catIds;
        }
        return array();
    }

    /**
     * Check schedule
     *
     * @param MF_Flexibleblock_Block_Fblock $block
     * @return boolean
     */
    public function validate($block = null){
        /* Check schedule */
        $time = getdate(Mage::app()->getLocale()->storeTimeStamp());
        $helper = Mage::helper('flexibleblock');
        $daySecondsNow = $helper->_getDaySeconds($time['hours'], $time['minutes'], $time['seconds']);
        $schedulePattern = $this->getData('schedule_pattern');
        switch ($schedulePattern)
        {
            case 'every day'   : break;
            case 'odd days'    : if(!($time['mday']%2)) return false; break;
            case 'even days'   : if($time['mday']%2) return false; break;
            case '1'           :
            case '2'           :
            case '3'           :
            case '4'           :
            case '5'           :
            case '6'           :
            case '7'           :
            case '8'           :
            case '9'           :
            case '10'          :
            case '11'          :
            case '12'          :
            case '13'          :
            case '14'          :
            case '15'          :
            case '16'          :
            case '17'          :
            case '18'          :
            case '19'          :
            case '20'          :
            case '21'          :
            case '22'          :
            case '23'          :
            case '24'          :
            case '25'          :
            case '26'          :
            case '27'          :
            case '28'          :
            case '29'          :
            case '30'          :
            case '31'          : if((string)$time['mday'] !== $schedulePattern) return false; break;
            case '1,11,21'     : if(($time['mday']%10)-1 || $time['mday']=='31') return false; break;
            case '1,11,21,31'  : if(($time['mday']%10)-1) return false; break;
            case '10,20,30'    : if($time['mday']%10) return false; break;
            case 'mo'          :
            case 'tu'          :
            case 'we'          :
            case 'th'          :
            case 'fr'          :
            case 'sa'          :
            case 'su'          : if(strtolower(substr($time['weekday'], 0, 2)) !== $schedulePattern) return false; break;
            case 'mon-fri'     : if(!$time['wday'] || $time['wday'] == 6) return false; break;
            case 'sat-sun'     : if($time['wday'] || $time['wday'] !== 6) return false; break;
            case 'tue-fri'     : if($time['wday'] < 2 || $time['wday'] > 5) return false; break;
            case 'mon-sat'     : if(!$time['wday']) return false; break;
            case 'mon,wed,fri' : if($time['wday'] !== 1 && $time['wday'] !== 3 && $time['wday'] !== 5) return false; break;
            case 'tue,thu,sat' : if($time['wday'] !== 2 && $time['wday'] !== 4 && $time['wday'] !== 6) return false; break;
            default : return false; // unknown pattern? that's incredible!!!
        }
        if($scheduleFromTime = $this->getData('schedule_from_time'))
        {
            $time = date_parse($scheduleFromTime);
            $timeFrom = $helper->_getDaySeconds($time['hour'], $time['minute'], $time['second']);
        }
        else $timeFrom = 0;

        if($scheduleToTime = $this->getData('schedule_to_time'))
        {
            $time = date_parse($scheduleToTime);
            $timeTo = $helper->_getDaySeconds($time['hour'], $time['minute'], $time['second']);
        }
        else $timeTo = 0;

        if ($timeFrom && $timeTo)
            if($timeFrom < $timeTo) {
                if ($daySecondsNow < $timeFrom || $daySecondsNow > $timeTo){
                    /* Set cache lifetime for block */
                    if(!is_null($block) && ($daySecondsNow < $timeFrom) && ($timeFrom - $daySecondsNow < $block->getCacheLifetime())){
                        $block->setCacheLifeTime($timeFrom - $daySecondsNow);
                    }
                    return false;
                } else {
                    if(!is_null($block) && ($timeTo - $daySecondsNow < $block->getCacheLifetime())){
                        $block->setCacheLifeTime($timeTo - $daySecondsNow);
                    }
                }
            }
            else {
                if (!($daySecondsNow > $timeFrom || $daySecondsNow < $timeTo)) return false;
            }
        else {
            if ($timeFrom && $daySecondsNow < $timeFrom){
                /* Set cache lifetime for block */
                if(!is_null($block) && ($timeFrom - $daySecondsNow < $block->getCacheLifetime())){
                    $block->setCacheLifeTime($timeFrom - $daySecondsNow);
                }
                return false;
            }
            if ($timeTo && $daySecondsNow > $timeTo) return false;
            else if ($timeTo && (!is_null($block)) && ($timeTo - $daySecondsNow < $block->getCacheLifetime())){
                $block->setCacheLifeTime($timeTo - $daySecondsNow);
            }
        }

        /* Check conditions */
        if($_product = Mage::registry('current_product')){
            $conditionsArr = unserialize($this->getConditions());
            if (!empty($conditionsArr) && is_array($conditionsArr)) {
                $catalogRule = Mage::getModel('catalogrule/rule');
                $catalogRule->getConditions()->loadArray($conditionsArr);
                if(!$catalogRule->validate($_product))
                    return false;
            }
        }



        return true;
    }

    public function getPackage(){
        if($this->getId()){
            $tmp = explode('/',$this->getPackageTheme());
            return $tmp[0];
        }
        return '';
    }

    public function getTheme(){
        if($this->getId()){
            $tmp = explode('/',$this->getPackageTheme());
            return $tmp[1];
        }
        return '';
    }

    public function getFinalLayoutHandles(){
        $result = array();
        if($this->getLayoutHandle()){
            $result[] = $this->getLayoutHandle();
        }
        if($this->getAdditionalLayoutHandle()){
            $result[] = $this->getAdditionalLayoutHandle();
        }
        return $result;
    }
	
	protected function _beforeSave(){
		return parent::_beforeSave();
	}
}
?>