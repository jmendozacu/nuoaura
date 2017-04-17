<?php
class EM_Minifilterproducts_Block_Listajax extends
    Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface
{
    const BEST_SELLER_CACHE_TAG = 'mini_ft_bestseller';
    protected $_pageSize = 3;
	protected $_numRow = 3;
	protected $_maxPage = null;
    
    protected function _construct()
    {
        parent::_construct();
        $cacheLifeTime = $this->getCacheLifeTime() ? $this->getCacheLifeTime() : 86400;
        $cacheTags = array(Mage_Catalog_Model_Product::CACHE_TAG, Mage_Cms_Model_Page::
                CACHE_TAG);
        if ($this->getShowFrontend('label') && Mage::helper('core')->isModuleEnabled('EM_Productlabels')) {
            $cacheTags[] = EM_Productlabels_Model_Productlabels::CACHE_TAG;
        }

        /* If get best seller product, add 'mini_ft_bestseller' cache tag */
        if ($this->getData('type_filter') == 2) {
            $cacheTags[] = self::BEST_SELLER_CACHE_TAG;
        }

        $this->addData(array('cache_lifetime' => $cacheLifeTime, 'cache_tags' => $cacheTags));
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'minifilterproducts',
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::app()->getStore()->getCurrentCurrencyCode(),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            serialize($this->getData()));
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getData('custom_theme')) {
            $this->setTemplate($this->getData('custom_theme'));
        } else {
            $this->setTemplate('em_minifilterproducts/em_ajax_grid.phtml');
        }
        return parent::_toHtml();
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $strCategories = $this->getData('category');
        $arrCategories = explode(",", $strCategories);
        return $arrCategories;
    }

    public function getTypeFilter()
    {
        return $this->getData('type_filter');
    }

    public function getColumnCount()
    {
        return $this->getData('column_count');
    }

    public function getFeatureChoosed()
    {
        return $this->getData('featured');
    }

    public function getCustomClass()
    {
        return $this->getData('custom_class');
    }

    public function getFrontendTitle()
    {
        return $this->getData('frontend_title');
    }

    public function getFrontendDescription()
    {
        return $this->getData('frontend_description');
    }

    public function getItemWidth()
    {
        $tempwidth = $this->getData('item_width');
        if (!(is_numeric($tempwidth)))
            $tempwidth = null;
        return $tempwidth;
    }

    public function getItemHeight()
    {
        $tempheight = $this->getData('item_height');
        if (!(is_numeric($tempheight)))
            $tempheight = null;
        return $tempheight;
    }

    public function getItemSpacing()
    {
        $tempheight = $this->getData('item_spacing');
        if (!(is_numeric($tempheight)))
            $tempheight = null;
        return $tempheight;
    }

    public function getShowFrontend($attr)
    {
        return (($this->getData('show') != '') && in_array($attr, explode(',', $this->
            getData('show'))));
    }

    public function getThumbnailWidth()
    {
        $tempwidth = $this->getData('thumbnail_width');
        if (!(is_numeric($tempwidth)))
            $tempwidth = 150;
        return $tempwidth;
    }

    public function getThumbnailHeight()
    {
        $tempheight = $this->getData('thumbnail_height');
        if (!(is_numeric($tempheight)))
            $tempheight = 150;
        return $tempheight;
    }

    public function getAltImg()
    {
        return $this->getData('alt_img');
    }

    public function getCacheLifeTime()
    {
        $value = $this->getData('cache_lifetime');
        if ($value && !is_numeric($value))
            return $value;
        return 86400;
    }
    
    public function generateParamsWidget(){
		return base64_encode(serialize($this->getData()));
	}
    
    public function getMaxPage(){
		return $this->_maxPage;
	}

    /**
     * Get product collection by condition
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getProductCollection()
    {        
        $type = $this->getData('type_filter');
        $storeId = Mage::app()->getStore()->getId();
        if ($type != 6) {
            $products = Mage::getModel('catalog/product')->getCollection()->setStoreId($storeId);
        } else {
            $products = Mage::getResourceModel('reports/product_collection')->setStoreId($storeId);
        }
        $products = $this->_addProductAttributesAndPrices($products)->addStoreFilter()->
            addAttributeToFilter('status', array('neq' => Mage_Catalog_Model_Product_Status::
                STATUS_DISABLED))->addAttributeToFilter('visibility', array("neq" => 1));


        if ($type == 1)
            $products = $this->getFilterFeatured($products); //	Special Attribute
        elseif ($type == 2)
            $products = $this->getFilterBest($products); //	Bestseller Product
        elseif ($type == 3)
            $products = $this->getFilterNew($products); //	New Products
        elseif ($type == 4)
            $products = $this->getFilterSale($products); //	Sales Products
        elseif ($type == 6)
            $products = $this->getFilterMostViewed($products); //	Most Viewed Products


        //Sort
        if ($type != 6) {
            $str = $this->getData('order_by');
            if ($str == 'em_order_random') {
                $products->getSelect()->order('RAND()');
            } else {
                if (isset($str))
                    $orders = explode(' ', $str);
                if (count($orders))
                    $products->addAttributeToSort($orders[0], $orders[1]);
                else
                    $products->addAttributeToSort('name', 'asc');
            }
        }


        /* Filter by categories */
        $products = $this->filterCate($products);

        //Page size & CurPage
        $pageSize = 10;
        if ($this->getData('limit_count') && is_numeric($this->getData('limit_count')))
            $pageSize = $this->getData('limit_count');
        /*
        $curPage = 1;
        $products->setPageSize($pageSize);
        $products->setCurPage($curPage);*/
        
        $curPage = $this->getRequest()->getParam('p',1);
		$this->_pageSize = $this->getLimitCount();	
		$products->setPageSize($this->_pageSize);
		$products->setCurPage($curPage);
		$this->_maxPage = ceil($products->getSize()/$this->_pageSize);
		
		return $products;
    }

    /**
     * Filter : Most Viewed Type
     *
     * @param $products
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getFilterMostViewed($products)
    {
        $products->addOrderedQty()->addViewsCount();
        return $products;
    }

    /**
     * Filter : Special Attribute Type
     *
     * @param $products
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getFilterFeatured($products)
    { //	Special Attribute
        $products->addAttributeToFilter($this->getFeatureChoosed(), array('gt' => 0));
        return $products;
    }

    /**
     * Filter : New Products Type
     *
     * @param $products
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getFilterNew($products)
    { //	New Products
        $todayStartOfDayDate = Mage::app()->getLocale()->date()->setTime('00:00:00')->
            toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate = Mage::app()->getLocale()->date()->setTime('23:59:59')->
            toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $products->addAttributeToFilter('news_from_date', array('or' => array(0 => array
                    ('date' => true, 'to' => $todayEndOfDayDate), 1 => array('is' => new
                        Zend_Db_Expr('null')))), 'left')->addAttributeToFilter('news_to_date', array('or' =>
                array(0 => array('date' => true, 'from' => $todayStartOfDayDate), 1 => array('is' =>
                        new Zend_Db_Expr('null')))), 'left')->addAttributeToFilter(array(array('attribute' =>
                    'news_from_date', 'is' => new Zend_Db_Expr('not null')), array('attribute' =>
                    'news_to_date', 'is' => new Zend_Db_Expr('not null'))));

        return $products;
    }

    /**
     * Filter : Sales Attribute Type
     *
     * @param $products
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getFilterSale($products)
    { //	Sales Products
        $todayStartOfDayDate = Mage::app()->getLocale()->date()->setTime('00:00:00')->
            toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate = Mage::app()->getLocale()->date()->setTime('23:59:59')->
            toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $products->addAttributeToFilter('special_price', array('gt' => 0))->
            addAttributeToFilter('special_from_date', array('or' => array(0 => array('date' => true,
                        'to' => $todayEndOfDayDate), 1 => array('is' => new Zend_Db_Expr('null')))),
            'left')->addAttributeToFilter('special_to_date', array('or' => array(0 => array
                    ('date' => true, 'from' => $todayStartOfDayDate), 1 => array('is' => new
                        Zend_Db_Expr('null')))), 'left')->addAttributeToFilter(array(array('attribute' =>
                    'special_from_date', 'is' => new Zend_Db_Expr('not null')), array('attribute' =>
                    'special_to_date', 'is' => new Zend_Db_Expr('not null'))));

        return $products;
    }

    /**
     * Filter : Bestseller Products Type
     *
     * @param $products
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getFilterBest($products)
    { //	Bestseller Product
        $_bestseller_products = $this->bestsellerProductArray();
        $_temp_productIds = array();
        $count = 0;
        $limit = $this->getData('limit_count');

        /**
         * Build up a case statement to ensure the order of ids is preserved
         */
        $orderString = array('CASE e.entity_id');
        foreach ($_bestseller_products as $i => $_product) {
            if (in_array($_product['entity_id'], $_temp_productIds))
                continue;
            else {
                $_temp_productIds[] = $_product['entity_id'];
                $orderString[] = 'WHEN ' . $_product['entity_id'] . ' THEN ' . $i;
                $count++;
                if ($count == $limit)
                    break;
            }
        }
        $orderString[] = 'END';
        $orderString = implode(' ', $orderString);

        $products->addAttributeToFilter('entity_id', array('in' => $_temp_productIds))->
            addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes
            ());

        if ($_temp_productIds) {
            $products->getSelect()->order(new Zend_Db_Expr($orderString));
        }
        return $products;
    }


    /**
     * @return array
     */
    protected function bestsellerProductArray()
    {
        $strCategories = $this->getData('category');
        if ($strCategories) {
            $query = "  
						SELECT DISTINCT SUM( order_items.qty_ordered ) AS  `ordered_qty` ,  `order_items`.`name` AS  `order_items_name` ,  `order_items`.`product_id` AS  `entity_id` ,  `e`.`entity_type_id` ,  `e`.`attribute_set_id` , `e`.`type_id` ,  `e`.`sku` ,  `e`.`has_options` ,  `e`.`required_options` ,  `e`.`created_at` ,  `e`.`updated_at` 
						FROM  `" . Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item') .
                "` AS  `order_items` 
						INNER JOIN  `" . Mage::getSingleton('core/resource')->getTableName('sales_flat_order') .
                "` AS  `order` ON  `order`.entity_id = order_items.order_id
						AND  `order`.state <>  'canceled'
						LEFT JOIN  `" . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity') .
                "` AS  `e` ON e.entity_id = order_items.product_id
						INNER JOIN  `" . Mage::getSingleton('core/resource')->getTableName('catalog_product_website') .
                "` AS  `product_website` ON product_website.product_id = e.entity_id
						AND product_website.website_id =  '" . Mage::app()->getWebsite()->getId() .
                "'
						INNER JOIN  `" . Mage::getSingleton('core/resource')->getTableName('catalog_category_product_index') .
                "` AS  `cat_index` ON cat_index.product_id = e.entity_id
						AND cat_index.store_id = '" . Mage::app()->getStore()->getStoreId() . "'
						AND cat_index.category_id
						IN ( " . $strCategories . " ) 
						WHERE (
						parent_item_id IS NULL
						)
						GROUP BY  `order_items`.`product_id` 
						HAVING (
						SUM( order_items.qty_ordered ) >0
						)
						ORDER BY  `ordered_qty` DESC 
						LIMIT 0 ," . $this->getLimitCount() . "
					";
        } else {
            $query = "	SELECT SUM( order_items.qty_ordered ) AS  `ordered_qty` ,  `order_items`.`name` AS  `order_items_name` ,  `order_items`.`product_id` AS  `entity_id` ,  `e`.`entity_type_id` ,  `e`.`attribute_set_id` , `e`.`type_id` ,  `e`.`sku` ,  `e`.`has_options` ,  `e`.`required_options` ,  `e`.`created_at` ,  `e`.`updated_at` 
						FROM  `" . Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item') .
                "` AS  `order_items` 
						INNER JOIN  `" . Mage::getSingleton('core/resource')->getTableName('sales_flat_order') .
                "` AS  `order` ON  `order`.entity_id = order_items.order_id
						AND  `order`.state <>  'canceled'
						LEFT JOIN  `" . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity') .
                "` AS  `e` ON e.entity_id = order_items.product_id
						INNER JOIN  `" . Mage::getSingleton('core/resource')->getTableName('catalog_product_website') .
                "` AS  `product_website` ON product_website.product_id = e.entity_id
						AND product_website.website_id =  '" . Mage::app()->getWebsite()->getId() .
                "'
						WHERE (
						parent_item_id IS NULL
						)
						GROUP BY  `order_items`.`product_id` 
						HAVING (
						SUM( order_items.qty_ordered ) >0
						)
						ORDER BY  `ordered_qty` DESC 
						LIMIT 0 ," . $this->getLimitCount() . "
						";
        }
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        return $readConnection->fetchAll($query);
    }

    /**
     * @param $products
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function filterCate($products)
    { //Filter by categories
        $config = $this->getData('category');
        if ($config) {
            $result = array();
            $condition_cat = array();
            $alias = 'cat_index';
            $categoryCondition = $products->getConnection()->quoteInto($alias .
                '.product_id=e.entity_id AND ' . $alias . '.store_id=? AND ', $products->
                getStoreId());
            $categoryCondition .= $alias . '.category_id IN (' . $config . ')';
            $products->getSelect()->joinInner(array($alias => $products->getTable('catalog/category_product_index')),
                $categoryCondition, array());
            $products->_categoryIndexJoined = true;
            $products->distinct(true);
        }
        return $products;
    }
    
    public function getIdJsWidget(){
		if(!$idJs = $this->getData('slider_unique_id'))
			$this->setData('slider_unique_id','em_ajaxminifilter_products_'.rand(0,100));
		return $this->getData('slider_unique_id');	
	}

}
