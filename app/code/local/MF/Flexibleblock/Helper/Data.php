<?php
class MF_Flexibleblock_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function editWysiwyg(){
		$config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
		$configPlugins = Mage::getModel('blog/variable_config')->getWysiwygPluginSettings($config);
		$config->setData('plugins',$configPlugins);
		$config->setData('widget_window_url',Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'));
		$config->setData('directives_url',Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'));
		$config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));
		if (Mage::getSingleton('admin/session')->isAllowed('cms/media_gallery')) {
			$config->setData('files_browser_window_url',Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'));
		}
		return $config;
	}

    /**
     * Returns total quantity of seconds within time period given
     * @param int $hours Quantity of hours
     * @param int $minutes Quantity of minutes
     * @param int $seconds Quantity of seconds
     * @return int Quantity of seconds
     */
    public function _getDaySeconds($hours, $minutes, $seconds)
    {
        return $hours*3600 + $minutes*60 + $seconds;
    }

    public function khongdau($str) {

        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);

        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);

        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);

        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);

        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);

        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);

        $str = preg_replace("/(đ)/", 'd', $str);



        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);

        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);

        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);

        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);

        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);

        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);

        $str = preg_replace("/(Đ)/", 'D', $str);

        /* $str = str_replace(" ", "-", str_replace("&*#39;","",$str)); */

        return $str;

    }

    public function friendlyIdentifier($string){
        $string = str_replace("$","",htmlspecialchars($string));
        $string = $this->khongdau($string);
        $string = preg_replace("`\[.*$%\]`U","",$string);
        $string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
        $string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
        return strtolower(trim($string, '-'));
    }

    public function getPositionKeyList($excludeCustom = false){
        $result = array('layout_handle' => 'position','layout_handle_2' => 'position_2','layout_handle_3' => 'position_3');
        if(!$excludeCustom){
            $result['custom_layout_handle'] = 'custom_position';
        }
        return $result;
    }

    public function getSelectedKeyList(){
        $result = array('layout_handle' => 'selected','layout_handle_2' => 'selected_2','layout_handle_3' => 'selected_3');
        return $result;
    }

    public function getDisabledKeyList(){
        $result = array('layout_handle' => 'disabled','layout_handle_2' => 'disabled_2','layout_handle_3' => 'disabled_3');
        return $result;
    }

    public function getAttributeKeyList(){
        $result = array('layout_handle' => 'layout_attribute','layout_handle_2' => 'layout_attribute_2','layout_handle_3' => 'layout_attribute_3','custom_layout_handle' => 'layout_attribute_custom');
        return $result;
    }
}

    