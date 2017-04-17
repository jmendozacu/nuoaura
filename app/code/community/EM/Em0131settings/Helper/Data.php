<?php
/**
 * @methods:
 * - get[Section]_[ConfigName]($defaultValue = '')
 */
class EM_Em0131settings_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return array
     * Get css config
    */    
    public function getAllCssConfig() {
        /** Mang luu tru bien duoi dang less */
        $configs = array();
        $skinUrl = 'frontend/em0131/default/css/';
        $stripesUrl = 'media/em_themeframework/background/stripes/';
        
        /** import less file */
		if(Mage::app()->getStore()->isCurrentlySecure() == 1){
    		$variables_url = Mage::getDesign()->getSkinUrl('css/less/theme.less',array('_secure'=>true));
            $function_url = Mage::getDesign()->getSkinUrl('css/less/functions.less',array('_secure'=>true));
        }else{
            $variables_url = Mage::getDesign()->getSkinUrl('css/less/theme.less');
            $function_url = Mage::getDesign()->getSkinUrl('css/less/functions.less');
        }		
		$configs['@variables_url'] = "\"{$variables_url}\"";
        $configs['@function_url'] = "\"{$function_url}\"";
                
        /** Lay bien tu file less.php. File less luu gia tri mac dinh cua bien. 
            Ko khai bao gia tri mac dinh cua bien trong file config.xml do co the ra gia tri null => less.js ko lay duoc bien
            Chi config bien google font va bien google font weight
        */
        $themeHelper = Mage::helper('themeframework/managetheme');
        $template = Mage::registry('em_current_theme')->getTemplate();        
        $xml = $themeHelper->loadTheme($template,'em0131');        
        $formData = $xml->getFormData();        

        foreach($formData['typography']['fieldset'] as $key => $variable)
        {
            foreach ($variable['fields'] as $index => $value) {
                $configValue = null;                
            	if( $themeHelper->getConfigTheme($key.'_'.$index)!='' ){
            	   $configValue =  $themeHelper->getConfigTheme($key.'_'.$index); 
            	}            		                   
                else{
                    if(isset($value['value'])){
                        $configValue = $value['value'];
                    }                	
                }
                
                if(isset($configValue) && !(preg_match("/google_fonts|file|custom_css/",$index)) && !(preg_match("/file|label|hidden/",$value['frontend_type'])) ){                    
                    if (preg_match("/\\s/",$configValue)) {
    					$configs["@{$index}"] = "~\"$configValue\"";
    				}
    				else{	
    					$configs["@{$index}"] = "{$configValue}";
    				}
                } 
                       
	            
				
            }                           
        }        
		//echo $configs['additional_css_file'];exit;
		/** Backgroung Image */        
        /** Khai bao bien luu duong dan background image trong less */        
		$image_bg_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $configs['@image_bg_url'] = "~\"{$image_bg_url}\"";
        
        $page_bg_image = $themeHelper->getConfigTheme('typo_general_page_bg_file') ? 
			'media/em0131/variations/'.$themeHelper->getConfigTheme('typo_general_page_bg_file')
			: ($themeHelper->getConfigTheme('typo_general_page_bg_image') ? $stripesUrl.$themeHelper->getConfigTheme('typo_general_page_bg_image') : $stripesUrl.'blank.gif');
        
        $header_bg_image = $themeHelper->getConfigTheme('header_header_bg_file') ? 
			'media/em0131/variations/'.$themeHelper->getConfigTheme('header_header_bg_file')
			: ($themeHelper->getConfigTheme('header_header_bg_image') ? $stripesUrl.$themeHelper->getConfigTheme('header_header_bg_image') : $stripesUrl.'blank.gif');
        
        $body_bg_image = $themeHelper->getConfigTheme('body_body_bg_file') ? 
			'media/em0131/variations/'.$themeHelper->getConfigTheme('body_body_bg_file')
			: ($themeHelper->getConfigTheme('body_body_bg_image') ? $stripesUrl.$themeHelper->getConfigTheme('body_body_bg_image') : $stripesUrl.'blank.gif');
        
        $footer_bg_image = $themeHelper->getConfigTheme('footer_footer_bg_file') ? 
			'media/em0131/variations/'.$themeHelper->getConfigTheme('footer_footer_bg_file')
			: ($themeHelper->getConfigTheme('footer_footer_bg_image') ? $stripesUrl.$themeHelper->getConfigTheme('footer_footer_bg_image') : $stripesUrl.'blank.gif');
                            
		$configs['@page_bg_image'] = "~\"{$page_bg_image}\"";
        $configs['@header_bg_image'] = "~\"{$header_bg_image}\"";
		$configs['@body_bg_image'] = "~\"{$body_bg_image}\"";
		$configs['@footer_bg_image'] = "~\"{$footer_bg_image}\""; 
        
        /** custom css file */
        if($additionalCssFilesString = explode(',', $themeHelper->getConfigTheme('css_additional_css_file'))){
            $i=0;
            if(Mage::app()->getStore()->isCurrentlySecure() == 1){
                foreach($additionalCssFilesString as $add){
                    if (preg_match("/.less/",$add)) {
    					$custom_url = Mage::getDesign()->getSkinUrl('css',array('_secure'=>true))."/".$add;						
                        $configs['additional_css_file'][$i] = "\"{$custom_url}\"";
                    }
                    $i++;
                }
            }else{
                foreach($additionalCssFilesString as $add){
                    if (preg_match("/.less/",$add)) {
    					$custom_url = Mage::getDesign()->getSkinUrl('css')."/".$add;						
                        $configs['additional_css_file'][$i] = "\"{$custom_url}\"";
                    }
                    $i++;
                }
            }
        }
        
        /** return less variable array */ 
        //var_dump($configs); exit;
		return $configs;
          
	}
}
