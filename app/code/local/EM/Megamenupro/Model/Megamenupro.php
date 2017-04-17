<?php

class EM_Megamenupro_Model_Megamenupro extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenupro/megamenupro');
    }
	
	public function toOptionArray()
    {
        return $this->getAttributeSetList();
    }
    public function getAttributeSetList()
    {
		$collection = $this->getCollection();
		$data	=	$collection->getData();
		$result	= array();
		$result[] = array('value' => '','label' => 'Please choose menu');
		foreach($data as $value)
			$result[] = array('value' => $value['megamenupro_id'],'label' => $value['name']);
		return $result;
	}
	
	public function getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        $categories = Mage::getModel('megamenupro/resource_category_flat')
            ->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
        return $categories;
    }
    
    protected function curl_request_async($url, $params, $type='POST')
    {
      foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
      }
      $post_string = implode('&', $post_params);

      $parts=parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80,
          $errno, $errstr, 30);

      // Data goes in the path for a GET request
      if('GET' == $type) $parts['path'] .= '?'.$post_string;

      $out = "$type ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      // Data goes in the request body for a POST request
      if ('POST' == $type && isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);
      fclose($fp);
    }
    
    protected function _afterSaveCommit()
    {
        $menuId = Mage::app()->getRequest()->getParam('id');
        //==== bom flush cache ====
        foreach(Mage::app()->getStores() as $store)
        {
            $code = $store->getCode();
            $url = Mage::getUrl('megamenupro/index/reload/') . '?___store=' . $code . '&id=' . $menuId;
            file_get_contents($url);
		}
		//==== end bom flush cache ====
    }
}
