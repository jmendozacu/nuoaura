<?php
/**
 * EMThemes
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
 * Do not edit or add to this file if you wish to upgrade the framework to newer
 * versions in the future. If you wish to customize the framework for your
 * needs please refer to http://www.emthemes.com/ for more information.
 *
 * @category    EM
 * @package     EM_ThemeFramework
 * @copyright   Copyright (c) 2012 CodeSpot JSC. (http://www.emthemes.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Giao L. Trinh (giao.trinh@emthemes.com)
 */

class EM_Themeframework_Helper_ManageTheme extends Mage_Core_Helper_Abstract
{


    /**
     * Current theme file
     *
     * @var string|null
     */
    protected $_file = null;

    /**
     * Loaded theme xml object
     *
     * @var SimpleXMLElement|null
     */
    protected $_xml = null;

    /**
     * Theme configuration
     *
     * @var array|null
     */
    protected $_conf = null;

    /**
     * Load Theme xml from $file
     *
     * @param string $file
     * @throws Mage_Core_Exception
     */



    public function loadTheme($themename,$package_theme)
    {                
        $path = Mage::getBaseDir('design').DS.'frontend'.DS.str_replace('/',DS,$package_theme).DS.str_replace('/',DS,$themename).DS.'etc'.DS.'variations.xml';
        $this->_file = $path;
        if (!file_exists($path)) {            
            $path = Mage::getBaseDir('design').DS.'frontend'.DS.str_replace('/',DS,$package_theme).DS.EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME.DS.'etc'.DS.'variations.xml';            
            if (!file_exists($path)) 
                Mage::throwException(Mage::helper('themeframework')->__('File doesn\'t exist "%s".', $path));
        }
        if (!is_readable($path)) {
            Mage::throwException(Mage::helper('themeframework')->__('Can\'t read file "%s".', $path));
        }
        try {
            $text = file_get_contents($path);
            $this->_xml = simplexml_load_string($text);

        } catch (Exception $e) {
            Mage::throwException(Mage::helper('themeframework')->__('Can\'t load XML.'));
        }
        if (empty($this->_xml)) {
            Mage::throwException(Mage::helper('themeframework')->__('Invalid XML.'));
        }
        $this->_conf = $this->_xmlToArray($this->_xml->configuration);
        $this->_conf = $this->_conf['configuration'];

        if (!is_array($this->_conf)) {
            Mage::throwException(Mage::helper('themeframework')->__('Wrong theme format.'));
        }
        return $this;
    }



    /**
     * Get theme xml as array
     *
     * @param array $xml
     * @return array
     */
    public function _xmlToArray($xml)
    {
        $result = array();
        foreach ($xml as $key => $value) {
            if (count($value)) {
                $result[$key] = $this->_xmlToArray($value);
            } else {
                $result[$key] = (string) $value;
            }
        }

        return $result;
    }

    /**
     * Get theme file
     *
     * @return null|string
     */
    protected function _getThemeFile()
    {
        return $this->_file;
    }

    /**
     * Get theme name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->_xml->manifest->name;
    }



    /**
     * Get theme Label
     *
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->_xml->manifest->label;
    }




    public function getFormData()
    {
        return $this->_conf;
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @param array $subTree
     * @param string $prefix
     * @return array
     */
    protected function _flatArray($subTree, $prefix = null)
    {
        $result = array();
        foreach ($subTree as $key => $value) {
            if (is_null($prefix)) {
                $name = $key;
            } else {
                $name = $prefix . '[' . $key . ']';
            }

            if (is_array($value)) {
                $result = array_merge($result, $this->_flatArray($value, $name));
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * Validate input Array, recursive
     *
     * @param array $data
     * @param array $xml
     * @return array
     */
    protected function _validateFormInput($data, $xml = null)
    {
        $root = false;
        $result = array();
        if (is_null($xml)) {
            $root = true;
            $data = array('configuration' => $data);
            $xml = $this->_xml->configuration;
        }
        foreach ($xml as $key => $value) {
            if (isset($data[$key])) {
                if (is_array($data[$key])) {
                    $result[$key] = $this->_validateFormInput($data[$key], $value);
                } else {
                    $result[$key] = $data[$key];
                }
            }
        }
        if ($root) {
            $result = $result['configuration'];
        }
        return $result;
    }

    public function readJsonConfiguration($object)
    {
        $conf = $this->_flatArray(json_decode($object));
        return $conf;
    }

    public function resizeImage($parent_theme, $width=NULL, $height=NULL, $path,$type)
    {       
        $imagePath = 'em_themeframework'.DS.$parent_theme.DS.'images'.DS.'thumbnail' .DS. $path;
        $imagePathFull = Mage::getBaseDir('media') . DS . $imagePath;
        
        if($width == NULL && $height == NULL && file_exists($imagePathFull)) {
            return str_replace('index.php/','',Mage::getBaseUrl()).'media/' . 'em_themeframework' . "/" .$parent_theme. "/" . 'images'."/". $type . "/" . $path;
        }
        else{
            $resizePath = $width . 'x' . $height;
            $resizePathFull = Mage::getBaseDir(). DS .'media' . DS . 'em_themeframework' .DS. $parent_theme .DS. 'images' . DS . $type. DS . $resizePath . DS . $path;
            if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
            $imageObj = new Varien_Image($imagePathFull);
            $imageObj->constrainOnly(TRUE);
            $imageObj->resize($width,$height);
            $imageObj->save($resizePathFull);
        }
        }
        return str_replace('index.php/','',Mage::getBaseUrl()).'media/' . 'em_themeframework' . "/" .$parent_theme. "/" . 'images'."/". $type . "/" . $resizePath . "/"  . $path;
    }

    public function getBackgroundImage($parent_theme, $imageName)
    {
        /*$imagePath = $parent_theme.DS.'variations'.DS.'' .DS. $imageName;
        $imagePathFull = Mage::getBaseDir('media') . DS . $imagePath;*/
        return str_replace('index.php/','',Mage::getBaseUrl()).'media/'.$parent_theme. "/" . 'variations'."/". $imageName;
    }
    public function importTheme($themename)
    {

        $path = Mage::getBaseDir('design').DS.'frontend'.DS.str_replace('/',DS,$themename).DS.EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME.DS.'etc'.DS.'init.xml';

        $this->_file = $path;
        if (!file_exists($path)) {
            Mage::throwException(Mage::helper('themeframework')->__('File doesn\'t exist "%s".', $path));
        }
        
        if (!is_readable($path)) {
            Mage::throwException(Mage::helper('themeframework')->__('Can\'t read file "%s".', $path));
        }

        try {

            $text = file_get_contents($path);
            $xml = simplexml_load_string($text);



        } catch (Exception $e) {
            Mage::throwException(Mage::helper('themeframework')->__('Can\'t load XML.'));
        }
        if (empty($xml)) {
            Mage::throwException(Mage::helper('themeframework')->__('Invalid XML.'));
        }
        $data = array();
        $xmlData = $this->_xmlToArray($xml);        
        $general = $this->_xmlToArray($xml->general);        
        $settings = $this->_xmlToArray($xml->settings);   

        $data = array_merge($general['general'],$settings['settings']);
        $model = Mage::getModel('themeframework/theme');

        $model->setData($data);
        $model->setId(null);
        $model->save();
        if(isset($xmlData['sub_theme']) && $xmlData['sub_theme']!='')
        {                                
            $sub_theme = explode(',',$xmlData['sub_theme']);			
            foreach($sub_theme as $theme)
            {
                $this->_importTheme(trim($theme),$general['general']['package']);
            }
        }
    }

    public function _importTheme($slugTheme,$package_theme)
    {
        $path = Mage::getBaseDir('design').DS.'frontend'.DS.str_replace('/',DS,$package_theme).DS.str_replace('/',DS,$slugTheme).DS.'etc'.DS.'init.xml';        
        $this->_file = $path;
        if (!file_exists($path)) {
            Mage::throwException(Mage::helper('themeframework')->__('File doesn\'t exist "%s".', $path));
        }
        if (!is_readable($path)) {
            Mage::throwException(Mage::helper('themeframework')->__('Can\'t read file "%s".', $path));
        }
        try {
            $text = file_get_contents($path);
            $xml = simplexml_load_string($text);

        } catch (Exception $e) {
            Mage::throwException(Mage::helper('themeframework')->__('Can\'t load XML.'));
        }
        if (empty($xml)) {
            Mage::throwException(Mage::helper('themeframework')->__('Invalid XML.'));
        }
        $data = array();
        $data = $this->_xmlToArray($xml);        
        $general = $this->_xmlToArray($xml->general);        

        $settings = $this->_xmlToArray($xml->settings);   

        $data = array_merge($general['general'],$settings['settings']);
        $model = Mage::getModel('themeframework/theme');
        $model->setData($data);
        $model->setId(null);
        $model->save();
    }
	
	public function getConfigTheme($key){
		if(Mage::registry('em_current_theme')){
			return Mage::registry('em_current_theme')->getData($key);
		}
		return '';
	}
	
	public function getActivatedTheme($storeId = Mage_Core_Model_App::ADMIN_STORE_ID){
		return Mage::getStoreConfig('theme_framework/theme/active',$storeId);
	}

    function replaceFileNameOnUpload($filename) {
        $ext = end(explode('.',$filename));
        // Replace all weird characters
        $replacer = preg_replace('/[^a-zA-Z0-9-_.]/','-', substr($filename, 0, -(strlen($ext)+1)));
        // Replace dots inside filename
        $replacer = str_replace('.','-', $replacer);
        return strtolower($replacer.'.'.$ext);
    }
    
    public function getDefaultXmlValueVariation($_callValue){
        if(!Mage::registry('em_default_xml_object')){
            $_currentThemeData = Mage::registry('theme_data');        
            if($_currentThemeData){
                if($_currentThemeData->getTemplate()){
            		$_currentLayoutTheme = $_currentThemeData->getTemplate();
            	}else{
                    $_currentLayoutTheme = EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME;
            	}
                $_currentPackage = $_currentThemeData->getPackage();			
            }else{
                $_currentLayoutTheme = EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME;
                $_currentPackage = 	Mage::getSingleton('core/design_package')->getPackageName();		
            }
            $_xml = $this->loadTheme($_currentLayoutTheme,$_currentPackage);              
            $_formData = $_xml->getFormData();
            
            $_defaultXmlValue = array();
            foreach($_formData as $note => $data){
                foreach($data['fieldset'] as $key => $variable){
                   foreach ($variable['fields'] as $index => $value) {
                        $_labelXml = $key.'_'.$index;
                        if(isset($value['value'])){
                            $_valueXml = $value['value'];
                            $_defaultXmlValue[$_labelXml] = $_valueXml;
                        }
                        
                   }
                }
            }
            $object = new Varien_Object($_defaultXmlValue);
            Mage::register('em_default_xml_object',$object);    
        }   
        return Mage::registry('em_default_xml_object')->getData($_callValue);
    }
}