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
 */
class EM_Themeframework_Adminhtml_Themeframework_ThemeController extends Mage_Adminhtml_Controller_Action
{

    public function _initTheme()
    {
        $id = $this->getRequest()->getParam('theme_id');
        $theme = Mage::getModel('themeframework/theme');
        // Initial checking
        if ($id) {
            $theme->load($id);
        }
        if(Mage::registry('theme_data'))
            Mage::unregister ('theme_data');
        Mage::register('theme_data', $theme);
        return Mage::registry('theme_data');
    }
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('emthemes')
            ->_addBreadcrumb(Mage::helper('themeframework')->__('Manage Themes'),       Mage::helper('themeframework')->__('Manage Themes'));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Manage Themes'));

        if($this->getRequest()->getParam('theme'))
        {
            $parent_theme = $this->getRequest()->getParam('theme');
            Mage::getSingleton('adminhtml/session')->setData('parent_theme', $parent_theme);
        }
        Mage::register('parent_theme',$parent_theme);
        $this->_initAction()
            ->renderLayout();
    }


    public function editAction() {
        $redirectBack = false;
        try {
            $id = $this->getRequest()->getParam('theme_id');
            //$parent_theme = Mage::getSingleton('adminhtml/session')->getParentTheme();
            $model = Mage::getModel('themeframework/theme');

            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('themeframework')->__('This theme no longer exists.'));
                    $this->_redirect('*/*/',array('_current' => true,'theme'=>Mage::registry('parent_theme')));
                    return;
                }
                $model->addJsonConfigData();
                $parent_theme = $model->getBaseTheme();

            }

            $type = Mage::app()->getFrontController()->getRequest()->getParam('type',0);

            $this->_title(($type && $type == 'clone') ? $this->__('New Theme') : $this->__("Edit ") . $model->getThemeName());

            if($type === 'clone'){
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('Filling the required fields before save new theme.'));
            }
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data))
                $model->setData($data);

            Mage::register('theme_data', $model);

            $this->_initAction()
                ->_addBreadcrumb($type && $type == 'clone' ? Mage::helper('themeframework')->__('New Theme') : Mage::helper('themeframework')->__('Edit Theme'), $type && $type == 'clone' ? Mage::helper('themeframework')->__('New Theme') : Mage::helper('themeframework')->__('Edit Theme'));

            // add event on edit page
            Mage::dispatchEvent('themeframework_edit_theme_'.$model->getBaseTheme(),array('head'=>$this->getLayout()->getBlock('head')));

            $this->_setActiveMenu('emthemes/items');
            $this->_addContent($this->getLayout()->createBlock('themeframework/adminhtml_theme_edit'))
                ->_addLeft($this->getLayout()->createBlock('themeframework/adminhtml_theme_edit_tabs'));
            $this->renderLayout();

        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $redirectBack = true;
            Mage::logException($e);
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to load theme form.'));
            $redirectBack = true;
            Mage::logException($e);
        }
        if ($redirectBack) {
            $this->_redirect('*/*/',array('theme'=>$model->getBaseTheme()));
            return;
        }
    }


    public function saveAction() {

        if($dataTemp = $this->getRequest()->getPost()) {
            $id = $dataTemp['theme_id'];
            $model = Mage::getModel('themeframework/theme')->load($id);

            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This theme no longer exists.'));
                $this->_redirect('*/*/', array('_current' => true,'theme' => $dataTemp['base_theme']));
                return;
            }


            //echo "<pre>"; print_r($_FILES);exit;

            if(!isset($dataTemp['identifier']))
                $dataTemp['identifier'] = $model->getIdentifier();
                        
            $configJson = (array)json_decode($model->getConfigJson());


            // prepare background image
            //echo "<pre>"   ; print_r($dataTemp);exit;
            foreach($_FILES as $key => $value)
            {

                if(isset($value) && (file_exists($_FILES[$key]['tmp_name'])))
                {
                    try {
                        $uploader = new Varien_File_Uploader($key);
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);                        
                        $imageName = Mage::helper('themeframework/managetheme')->replaceFileNameOnUpload($value['name']);
                        if($key=='thumbnail')
                        {    
                            
                            $imagePath = Mage::getBaseDir('media') . DS . 'em_themeframework'.DS.$model->getBaseTheme().DS.'images'.DS.'thumbnail';
                            $uploader->save($imagePath, $imageName);                            
                            $model->setPath($uploader->getUploadedFileName());
                        }
                        else
                        {
                            $imagePath = Mage::getBaseDir('media') . DS . $model->getBaseTheme().DS.'variations';
                            $uploader->save($imagePath, $imageName);
                            $dataTemp['settings'][$key] = $uploader->getUploadedFileName();
                        }

                    }catch(Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }

                }
                else
                {
                    if(isset($dataTemp[$key]['delete']) && $dataTemp[$key]['delete'] == 1)
                    {
                        $model->setPath('');
                    }
                    else
                    {
                        $dataTemp[$key] = $configJson[$key];
                        unset($dataTemp[$key]);

                    }

                    if(isset($dataTemp['settings'][$key]['delete']) && $dataTemp['settings'][$key]['delete'] == 1)
                    {

                        $dataTemp['settings'][$key] = '';

                    }
                    else
                    {
                        $dataTemp['settings'][$key] = $configJson[$key];
                        unset($dataTemp[$key]);

                    }

                }
            }

            $model->addData($dataTemp);
            $model->setConfigJson(json_encode($dataTemp['settings']));


            // Prepare exlcuded blocks
            $links = $this->getRequest()->getPost('links');

            if (isset($links['excluded'])) {                               
                $model->setExcludedBlocks($links['excluded']);
            }

            /* Prepare stores activated */
            $newStores = isset($dataTemp['stores']) ? $dataTemp['stores'] : array();
            if(in_array(Mage_Core_Model_App::ADMIN_STORE_ID,$newStores)){
                $newStores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
            }
            $model->setStores($newStores)->setChangeStores(true);

            /* Prepare stores imported */
            $stores = explode(',',$model->getIsImport());
            $importStores = array_diff($newStores,$stores);
            if(count($importStores) > 0){
                $model->setImportStores($importStores);
                $model->setIsImport(array_merge($importStores,$stores));
            }

            try {

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('The theme has been saved.'));

                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // Save and Continue Edit
                if ($this->getRequest()->getParam('back')) {
                    if($this->getRequest()->getParam('type'))
                        $this->_redirect('*/*/edit', array('theme_id' => $model->getId()));
                    else
                        $this->_redirect('*/*/edit', array('theme_id' => $model->getId(),'_current' => true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/',array('_current' => true,'theme_id' =>'','theme'=> $model->getBaseTheme()));
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($dataTemp);
            }
            $this->_redirect('*/*/edit', array('_current' => true,'theme_id' => $this->getRequest()->getParam('theme_id')));
            return;

        }

        $this->_redirect('*/*/');
    }


    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('theme_id')) {
            try {
                $model = Mage::getModel('themeframework/theme');
                $model->load($id);
                /* Get current scope, scope id */

                if($this->getRequest()->getParam('store') || !$this->getRequest()->getParam('website')){                
                    $scope = $this->getRequest()->getParam('store') ? 'stores' : 'default';
                } else {                
                    $scope = 'websites';
                }
                if($scope == 'stores' || $scope == 'default'){
                    $configObject = Mage::app()->getStore($this->getRequest()->getParam('store',0));
                } else {
                    $configObject = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
                }
                $scopeId = $configObject->getId();
                if($model->isActive($scope, $scopeId, $configObject)){                    
                    $model->deactivate($scope, $scopeId);
                }
                // init model and delete

                $parent_theme = $model->getBaseTheme();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('The theme has been deleted.'));
                // go to grid
                $this->_redirect('*/*/',array('theme'=>$parent_theme));
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('_current' => true,'theme_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('admihtml/session')->addError(Mage::helper('themeframework')->__('Unable to find theme to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function validateAction()
    {
        $postData = $this->getRequest()->getPost();
        $response = new Varien_Object();
        if(isset($postData['identifier']))
        {
            $identifier = $postData['identifier'];
            $model = Mage::getModel('themeframework/theme')->load($identifier,'identifier');
            $id = 0;
            if(isset($postData['theme_id']))
                $id = $postData['theme_id'];

            if($model->getId())
            {
                if($model->getId()!=$id)
                {
                    $response->setError(true);
                    $response->setAttribute("info_identifier");
                    $response->setMessage(Mage::helper('themeframework')->__("The value of identifier is unique"));
                } else {
                    $response->setError(false);
                }
            }
            else
                $response->setError(false);
        }
        else
            $response->setError(false);
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Active theme
     */
    public function activeAction(){
        $theme = Mage::getModel('themeframework/theme')->load($this->getRequest()->getParam('theme_id'));
        if(is_null($this->getRequest()->getParam('store'))){
            $scope = $this->getRequest()->getParam('website') ? 'websites' : 'default';
            $scopeId = $this->getRequest()->getParam('website') ? Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getId() : Mage_Core_Model_App::ADMIN_STORE_ID;
        } else {
            $scope = 'stores';
            $scopeId = Mage::app()->getStore($this->getRequest()->getParam('store'))->getId();
        }

        /* activate theme and import sample data */
        $theme->activate($scope,$scopeId);
        $theme->importSampleData(false);

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('The theme has been activated.'));
        $this->_redirect('*/*',array('_current' => true,'theme_id' => '','theme' => $theme->getBaseTheme()));
    }

    /**
     * Deactivate theme
     */
    public function deactivateAction(){
        $theme = Mage::getModel('themeframework/theme')->load($this->getRequest()->getParam('theme_id'));
        if(is_null($this->getRequest()->getParam('store'))){
            $scope = $this->getRequest()->getParam('website') ? 'websites' : 'default';
            $scopeId = $this->getRequest()->getParam('website') ? Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getId() : Mage_Core_Model_App::ADMIN_STORE_ID;
        } else {
            $scope = 'stores';
            $scopeId = Mage::app()->getStore($this->getRequest()->getParam('store'))->getId();
        }
        $theme->deactivate($scope, $scopeId);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('The theme has been deactivated.'));
        $this->_redirect('*/*',array('_current' => true,'theme_id' => '','theme' => $theme->getBaseTheme()));
    }

    /**
     * Install theme
     */
    public function installAction(){        
        $baseTheme = $this->getRequest()->getParam('theme');
        $theme = Mage::getModel("themeframework/theme")->load($baseTheme,'base_theme');        
        $pathFile = Mage::getBaseDir('var').DS.'install_'.$baseTheme.'.txt';        
        if(!file_exists($pathFile))
        {
            $user = Mage::getSingleton('admin/session');
            $userName = $user->getUser()->getUsername();
            
            /*if(file_exists($pathFile)){            
                echo 'Installing '.$baseTheme.' theme by '.$userName.', please come back in some minutes ...';
                exit;
            }*/
            file_put_contents($pathFile,$userName);                    
            Mage::helper('themeframework/managetheme')->importTheme($baseTheme);
            $theme = Mage::getModel('themeframework/theme')->load($baseTheme,'identifier');
            $theme->importSampleData(true);
			if(Mage::getModel('admin/block')){Mage::helper('themeframework/import')->installPermissionBlock($this->getRequest()->getParam('theme'),true);}            
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('The theme has been installed.'));
            unlink($pathFile);        
            $this->_redirect('*/*',array('theme' => $baseTheme));
        }
        else{
            
            $string = file_get_contents($pathFile);
            Mage::getSingleton('adminhtml/session')->addWarning('Installing '.$baseTheme.' theme by '.$string.', please come back in some minutes ...'); 
            $this->_redirect('*/adminhtml_basetheme',array());    
        }
        
    }

    public function exportAction()
    {            
        $this->_initTheme();
        $this->loadLayout()->_addContent($this->getLayout()->createBlock('themeframework/adminhtml_theme_export'))
            ->_setActiveMenu('emthemes')
            ->renderLayout();

    }   

    public function exportPostAction(){
        if ($id = $this->getRequest()->getParam('theme_id')) {
            try{
                if($dataTemp = $this->getRequest()->getPost()) { 
                    $typeExport = "";
                    $model = Mage::getModel('themeframework/theme');
                    $model->load($id);
                    $data = $model->getData();
                    unset($data['theme_id']);
                    unset($data['created_at']);
                    unset($data['updated_at']);                    
                    unset($data['is_import']);
                    $doc = new DOMDocument('1.0','utf-8');
                    $doc->formatOutput = true;
                    $root = $doc->createElement( "theme" );
                    $doc->appendChild( $root );
                    $sampleNode = $doc->createElement('sample_data');   
                    $general = $doc->createElement('general');
                    $settings = $doc->createElement('settings');
                    $root->appendChild( $general );
                    

                    //get base information of a theme

                    foreach($data as $key => $value)
                    {
                        if($key != 'excluded_blocks' && $key != 'config_json')
                        {
                            $cdata_value = $doc->createCDATASection($value);
                            $node = $doc->createElement($key);
                            $node->appendChild($cdata_value);
                            $general->appendChild($node);
                        }
                    }                 
                    // export settings
                    if($dataTemp['export_type']!=2){  
                        $typeExport.="settings_";                      
                        $root->appendChild($settings);
                        // config_json
                        $cdata_value = $doc->createCDATASection($data['config_json']);
                        $node = $doc->createElement('config_json');
                        $node->appendChild($cdata_value);
                        $settings->appendChild($node);
                        // excluded_block
                        $cdata_value = $doc->createCDATASection($data['excluded_blocks']);
                        $node = $doc->createElement('excluded_blocks');
                        $node->appendChild($cdata_value);
                        $settings->appendChild($node);
                    }

                    //export sample data
                    if(!$storeId = $dataTemp['store_id'])
                        $storeId = 0;                                          
                    if($dataTemp['export_type']!=1){
                        $typeExport.="sample_data_";
                        $filterData = $dataTemp['filter_export'];    
                        $root->appendChild($sampleNode);
                        
                        $theme_slug = $model->getPackage() . '/' . ($model->getTemplate() ? $model->getTemplate() : EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME);
                        $prefix = $data['identifier'];
                        if(in_array('fblock', $filterData))
                            $this->exportFlexibleBlock($sampleNode,$doc,$theme_slug,$storeId);
                        if(in_array('layout', $filterData))
                            $this->exportThemeFrameworkArea($sampleNode,$doc,$theme_slug,$storeId);  
                        if(in_array('staticcontent', $filterData))
                            $this->exportStaticContent($sampleNode,$doc,$prefix,$storeId);
                        if(in_array('menu', $filterData))
                            $this->exportMegaMenu($sampleNode,$doc,$prefix);
                        if(in_array('slideshow', $filterData))
                            $this->exportSlideshow($sampleNode,$doc,$prefix);
                       // $this->backupWidgetInstanceInTheme($sampleNode,$doc,$theme_slug,$storeId);
                    }
                                      


                    header("Content-Type: application/octet-stream");
                    header('Content-Disposition: attachment; filename="'.$data['identifier']."_".$typeExport.date('Ymd_His').".xml");
                    
                    $this->getResponse()->setBody($doc->saveXML());
                    
                }
            }catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('No valid data sent'));
            }
        }
    }

    public function blockAction()
    {
        $this->_initTheme();
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.edit.tab.block')
            ->setExcludedBlocks($this->getRequest()->getPost('excluded_blocks', null))
            ->setData('reload',false);
        $this->renderLayout();
    }
    public function blockGridAction()
    {
        $this->_initTheme();
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.edit.tab.block')
            ->setExcludedBlocks($this->getRequest()->getPost('excluded_blocks', null))
            ->setData('reload',true);
        $this->renderLayout();
    }

    public function resetAction()
    {
        $id = $this->getRequest()->getParam('theme_id');
        if ($id) {
            $model = Mage::getModel('themeframework/theme')->load($id);

            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This theme no longer exists.'));
                $this->_redirect('*/*/', array('_current' => true,'theme' => $dataTemp['base_theme']));
                return;
            }

            $configJson = array();

            $themeHelper = Mage::helper('themeframework/managetheme');
            $themeSlug = $model->getTemplate();
            if($themeSlug == "")
                $themeSlug = $model->getIdentifier();
            $xml = $themeHelper->loadTheme($themeSlug,$model->getPackage());
            $formData = $xml->getFormData();

            foreach($formData as $parentKey => $fieldset)
            {
                foreach ($fieldset['fieldset'] as $key => $field)
                {
                    foreach ($field['fields'] as $variable => $value)
                    {
                        if(isset($value['value']))
                            $configJson[$key.'_'.$variable] = $value['value'];
                        else
                            $configJson[$key.'_'.$variable] = '';
                    }


                }
            }

            $model->setConfigJson(json_encode($configJson));
            $model->setExcludedBlocks();
            try {

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('The theme has been reset and saved.'));

                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/edit', array('_current' => true,'theme_id' => $model->getId()));
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($dataTemp);
            }
            $this->_redirect('*/*/edit', array('_current' => true,'theme_id' => $this->getRequest()->getParam('theme_id')));
            return;

        }
        $this->_redirect('*/*');
    }

    /* import theme */

    public function importAction() {
        if(Mage::getSingleton('admin/session')->getXmldata()){                                 
            Mage::getSingleton('admin/session')->unsetData('xmldata');                                
            Mage::getSingleton('admin/session')->unsetData('basetheme');  
        }
        $this->_initTheme();
        $this->loadLayout()->_addContent($this->getLayout()->createBlock('themeframework/adminhtml_theme_import'))
            ->_setActiveMenu('emthemes')
            ->renderLayout();
    }


    public function importPostAction(){        
        $id = $this->getRequest()->getParam('theme_id');        
        $model = Mage::getModel('themeframework/theme')->load($id);
        $url = $this->getUrl('*/*/confirm', array('_current' => true, 'theme_id' => $id));
        $response = new Varien_Object();
  
            //echo "<pre>";print_r($_FILES['import_file']);            
        $dataTemp = $this->getRequest()->getPost();   
        if (!empty($_FILES['import_file']['tmp_name']) && $dataTemp) {
            try {
                $tempFile = $_FILES['import_file']['tmp_name'];                    
                try {
                    $text = file_get_contents($tempFile);
                    $xmlData = simplexml_load_string($text);
                    
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError('Can\'t load XML.');                        
                    $this->_redirect('*/*/import', array('_current' => true,'theme_id' => $id));                        
                }
                if (empty($xmlData)) {
                    Mage::getSingleton('adminhtml/session')->addError('Invalid File.');                        
                    $this->_redirect('*/*/import', array('_current' => true,'theme_id' => $id));                      
                }
                else{
                    $theme = Mage::helper('themeframework/managetheme')->_xmlToArray($xmlData->general);
                      
                    $general = $theme['general'];                        
                    if($general['base_theme'])
                    {
                        $arrData = array();            
                        $arrData["base_theme"] = $model->getBaseTheme();
                        $arrData["package"] = $model->getPackage();
                        $arrData["template"] = $model->getTemplate();
                        $arrData["layout"] = $model->getLayout();
                        $arrData["skin"] = $model->getSkin();
                        $arrData["default_theme"] = $model->getDefaultTheme();

                        unset($general['theme_name']);
                        unset($general['identifier']);
                        unset($general['is_clone']);

                        Mage::getSingleton('admin/session')->setData('xmldata',$text);   
                                                                                                    
                        if(count(array_diff_assoc($arrData,$general))>0)
                        {                        
                            // compare base theme if same theme, redirect to filter import                                
                            Mage::getSingleton('admin/session')->setData('basetheme',false);                                              
                        }
                        else
                        {                             
                            Mage::getSingleton('admin/session')->setData('basetheme',true);              
                        }
                        if(!isset($dataTemp['store_id']))
                            $dataTemp['store_id'] = 0;
                        $this->_redirect('*/*/filterImport', array('_current' => true,'theme_id' => $id,'store_id' => $dataTemp['store_id'],'import_type' => $dataTemp['import_type']));     
                    }
                    else {
                        Mage::getSingleton('adminhtml/session')->addError('Invalid XML.');                        
                        $this->_redirect('*/*/import', array('_current' => true,'theme_id' => $id));                                  
                    }
                }

            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());                        
                $this->_redirect('*/*/import', array('_current' => true,'theme_id' => $id));                    
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Invalid file upload attempt');                        
                $this->_redirect('*/*/import', array('_current' => true,'theme_id' => $id));                                        
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError('Invalid file upload attempt');                        
            $this->_redirect('*/*/import', array('_current' => true,'theme_id' => $id));                                        
            
        }            
        
    }

    public function filterImportAction(){                 
        
        $this->loadLayout()->_addContent($this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid'))
                ->_addLeft($this->getLayout()->createBlock('themeframework/adminhtml_theme_import_grid_tabs'))
                ->_setActiveMenu('emthemes')            
                ->renderLayout();        
    }


    public function importPageAction()
    {        
        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $sampleData = $xmlData->sample_data;          
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.import.grid.tab.cmspage')
            ->setImportPage($sampleData->cmspage);
        $this->renderLayout();
    }

    public function importBlockAction()
    {        

        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $sampleData = $xmlData->sample_data;                     
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.import.grid.tab.staticblock')
            ->setImportBlock($sampleData->staticblock);
        $this->renderLayout();
    }

    public function importLayoutAction()
    {        
        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $sampleData = $xmlData->sample_data;             
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.import.grid.tab.layout')
            ->setImportLayout($sampleData->themeframework);
        $this->renderLayout();
    }

    public function importFblockAction()
    {        
        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $sampleData = $xmlData->sample_data;             
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.import.grid.tab.fblock')
            ->setImportFblocks($sampleData->flexible_block);
        $this->renderLayout();
    }

    public function importSlideshowAction()
    {        
        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $sampleData = $xmlData->sample_data;             
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.import.grid.tab.slideshow')
            ->setImportSlideshow($sampleData->slideshows)
			->setImportMiniSlideshow($sampleData->minislideshows);
        $this->renderLayout();
    }

    public function importMegamenuAction()
    {        
        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $sampleData = $xmlData->sample_data;             
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.import.grid.tab.menu')
            ->setImportMenu($sampleData->megamenu);
        $this->renderLayout();
    }
    /*public function importFblockGridAction()
    {     
        $this->loadLayout();
        $this->getLayout()->getBlock('themeframework.theme.import.grid.tab.block')
            ->setImportFblocks($this->getRequest()->getPost('importfblock', null));            
        $this->renderLayout();
    }*/

    public function importSampleDataAction(){
        $xmlData = simplexml_load_string(Mage::getSingleton('admin/session')->getXmldata());
        $import_type = $dataTemp = $this->getRequest()->getParam('import_type'); 
        $storeId = $this->getRequest()->getParam('store_id',0);
        $themeId = $this->getRequest()->getParam('theme_id');
        if($dataTemp = $this->getRequest()->getPost()) 
        {
            if(isset($dataTemp['links']))
            {
                $links = $dataTemp['links'];
                if($import_type != 1)
                    Mage::helper('themeframework/import')->importSampleData($xmlData->sample_data,$links,$storeId);
            }
            if(isset($dataTemp['type_setting']))
            {
                $filterSettings = $dataTemp['type_setting'];
                if($import_type != 2)                
                    Mage::helper('themeframework/import')->importSettings($xmlData->settings,$filterSettings,$themeId);        
            }  
            if(isset($dataTemp['type_setting_exclude_block']))
            {
                $filterSettings = $dataTemp['type_setting_exclude_block'];                    
                if($import_type != 2)                
                    Mage::helper('themeframework/import')->importSettings($xmlData->settings,$filterSettings,$themeId);        
            }       
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('themeframework')->__('The theme data has been imported.'));
            $this->_redirect('*/*/edit', array('_current' => true,'theme_id' => $this->getRequest()->getParam('theme_id')));
        }
    }

    /*export sample data for each theme*/


    public function exportFlexibleBlock($root,$doc,$package,$storeId){
        $collection = Mage::getModel('flexibleblock/fblock')->getCollection()->setStoreId($storeId)->addAttributeToFilter('package_theme',$package)->addAttributeToSelect('*');
        $flexible_block = $doc->createElement('flexible_block');
        $root->appendChild($flexible_block);
        if($collection->getSize() > 0){
            foreach($collection as $c)
            {                                
                $blockData = $c->getData();
                unset($blockData['created_at']);
                unset($blockData['updated_at']);
                unset($blockData['entity_id']);
                unset($blockData['entity_type_id']);
                $node = $doc->createElement('fblock');
                foreach($blockData as $key => $value){
                    $cdataValue = $doc->createCDATASection($value);
                    $childNode = $doc->createElement($key);
                    $childNode->appendChild($cdataValue);
                    $node->appendChild($childNode);
                }
                $flexible_block->appendChild($node);
            }                 
        }
        return $this;
    }
    public function exportThemeFrameworkArea($root,$doc,$themeSlug,$storeId){
        //$_themeFrameworkAreaModel = 'themeframework/area';
        $r = $doc->createElement( 'themeframework' );
        $root->appendChild($r);
        $collection = Mage::getModel('themeframework/area')->setStoreId($storeId)->getCollection()
            ->addFieldToFilter('is_active',1)
            ->addFieldToFilter('package_theme',$themeSlug);
        foreach($collection as $area){
            $b = $doc->createElement('area');
            $this->appendElementXml('package_theme',$area->getPackageTheme(),$doc,$b);
            $this->appendElementXml('name',$area->getName(),$doc,$b);
            $this->appendElementXml('layout',$area->getLayout(),$doc,$b);
            $this->appendElementXml('is_active',$area->getIsActive(),$doc,$b);
            $this->appendElementXml('content',$area->getContent(),$doc,$b);
            $r->appendChild($b);
        }
        return $this;
    }
    public function appendElementXml($name,$value,$doc,$parent,$isSerialize = false){
        $content = $doc->createElement($name);
        if($isSerialize)
            $cdata = $doc->createCDATASection(serialize($value));
        else    
            $cdata = $doc->createCDATASection($value);
        $content->appendChild($cdata);  
        $parent->appendChild($content); 
        return $this;
    }

    /**
     * Export Static block and cms page of theme       
     */
    public function exportStaticContent($root,$doc,$prefix,$storeId)
    {           
        $collectionBlock = Mage::getModel('cms/block')->getCollection()->distinct(true)
            ->addStoreFilter($storeId)
            ->addFieldToFilter('identifier',array('like'=>$prefix.'%'));
        
        $collectionPage = Mage::getModel('cms/page')->getCollection()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('identifier',array('like'=>$prefix.'%')); 
            
        $this->exportType($collectionBlock,$root,'staticblock',$doc);
        $this->exportType($collectionPage,$root,'cmspage',$doc);
        return $doc;
    }

    public function exportType($collection,$root,$type,$doc){
        if($collection->count()){
            $r = $doc->createElement( $type );
            $root->appendChild($r);
            if($type == 'staticblock')
                $blockType = 'block';
            else
                $blockType = 'page';
            foreach( $collection as $data)
            {   
                $value  =   $data->getData();
                /*$store= $data->getResource()->lookupStoreIds($data->getId());
                if(count(array_diff($store,array(0))) > 0)
                    continue;*/
                    
                $b = $doc->createElement($blockType);
                $att = $doc->createAttribute('id');
                $att->value = $data->getId();
                $b->appendChild($att);

                $this->appendElementXml('title',$value['title'],$doc,$b);
                $this->appendElementXml('identifier',$value['identifier'],$doc,$b);
                //$this->appendElementXml('store_id',$store,$doc,$b,true);
                $this->appendElementXml('content',$value['content'],$doc,$b);
                $this->appendElementXml('is_active',$value['is_active'],$doc,$b);
                
                if($type == 'cmspage'){
                    $this->appendElementXml('root_template',$value['root_template'],$doc,$b);
                    $this->appendElementXml('meta_keywords',$value['meta_keywords'],$doc,$b);
                    $this->appendElementXml('meta_description',$value['meta_description'],$doc,$b);
                    $this->appendElementXml('content_heading',$value['content_heading'],$doc,$b);
                    $this->appendElementXml('sort_order',$value['sort_order'],$doc,$b);
                    $this->appendElementXml('layout_update_xml',$value['layout_update_xml'],$doc,$b);
                    $this->appendElementXml('custom_theme',$value['custom_theme'],$doc,$b);
                    $this->appendElementXml('custom_root_template',$value['custom_root_template'],$doc,$b);
                    $this->appendElementXml('custom_layout_update_xml',$value['custom_layout_update_xml'],$doc,$b);
                    $this->appendElementXml('custom_theme_from',$value['custom_theme_from'],$doc,$b);
                    $this->appendElementXml('custom_theme_to',$value['custom_theme_to'],$doc,$b);
                }

                $r->appendChild($b);
            }
        }
        return $this;   
    }

    public function exportMegaMenu($root,$doc,$prefix){
        $r = $doc->createElement( 'megamenu' );
        $root->appendChild($r);
        if(Mage::getConfig()->getModuleConfig('EM_Megamenupro')->is('active', 'true'))
            $collection = Mage::getModel('megamenupro/megamenupro')->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('identifier',array('like'=>$prefix.'%'));
        else
            $collection = Mage::getModel('themeframework/megamenupro')->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('identifier',array('like'=>$prefix.'%'));        
        foreach($collection as $menu){
            $b = $doc->createElement('menu');
            $att = $doc->createAttribute('id');
            $att->value = $menu->getId();
            $b->appendChild($att);
            $this->appendElementXml('name',$menu->getName(),$doc,$b);
            $this->appendElementXml('identifier',$menu->getIdentifier(),$doc,$b);
            $this->appendElementXml('description',$menu->getDescription(),$doc,$b);
            $this->appendElementXml('type',$menu->getType(),$doc,$b);
            $this->appendElementXml('status',$menu->getStatus(),$doc,$b);
            $this->appendElementXml('content',$menu->getContent(),$doc,$b);
            $this->appendElementXml('css_class',$menu->getCssClass(),$doc,$b);
            $r->appendChild($b);
        }
        return $this;
    }

    public function exportSlideshow($root,$doc,$prefix){        
        if (Mage::getConfig()->getModuleConfig('EM_Slideshow2')->is('active', 'true')){
            $r = $doc->createElement( 'slideshows' );
            $root->appendChild($r);
            $collection = Mage::getModel('slideshow2/slider')->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('identifier',array('like'=>$prefix.'%'));
            foreach($collection as $slideshow){
                $b = $doc->createElement('slideshow');
                $att = $doc->createAttribute('id');
                $att->value = $slideshow->getId();
                $b->appendChild($att);
                $this->appendElementXml('name',$slideshow->getName(),$doc,$b);
                $this->appendElementXml('identifier',$slideshow->getIdentifier(),$doc,$b);
                $this->appendElementXml('description',$slideshow->getDescription(),$doc,$b);
                $this->appendElementXml('images',$slideshow->getImages(),$doc,$b);
                $this->appendElementXml('slider_type',$slideshow->getSliderType(),$doc,$b);
                $this->appendElementXml('slider_params',$slideshow->getSliderParams(),$doc,$b);
                $this->appendElementXml('delay',$slideshow->getDelay(),$doc,$b);
                $this->appendElementXml('touch',$slideshow->getTouch(),$doc,$b);
                $this->appendElementXml('stop_hover',$slideshow->getStopHover(),$doc,$b);
                $this->appendElementXml('shuffle_mode',$slideshow->getShuffleMode(),$doc,$b);
                $this->appendElementXml('stop_slider',$slideshow->getStopSlider(),$doc,$b);
                $this->appendElementXml('stop_after_loop',$slideshow->getStopAfterLoop(),$doc,$b);
                $this->appendElementXml('stop_at_slide',$slideshow->getStopAtSlide(),$doc,$b);
                $this->appendElementXml('position',$slideshow->getPosition(),$doc,$b);
                $this->appendElementXml('appearance',$slideshow->getAppearance(),$doc,$b);
                $this->appendElementXml('navigation',$slideshow->getNavigation(),$doc,$b);
                $this->appendElementXml('thumbnail',$slideshow->getThumbnail(),$doc,$b);
                $this->appendElementXml('visibility',$slideshow->getVisibility(),$doc,$b);
                $this->appendElementXml('trouble',$slideshow->getTrouble(),$doc,$b);
                $this->appendElementXml('status',$slideshow->getStatus(),$doc,$b);
                $r->appendChild($b);
            }
        }
        
        if (Mage::getConfig()->getModuleConfig('EM_Minislideshow')->is('active', 'true')){
            $_minir = $doc->createElement( 'minislideshows' );
            $root->appendChild($_minir);
            $_minicollection = Mage::getModel('minislideshow/slider')->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('identifier',array('like'=>$prefix.'%'));
            foreach($_minicollection as $_minislideshow){
                $_minib = $doc->createElement('minislideshow');
                $_miniatt = $doc->createAttribute('id');
                $_miniatt->value = $_minislideshow->getId();
                $_minib->appendChild($_miniatt);
                $this->appendElementXml('name',$_minislideshow->getName(),$doc,$_minib);
                $this->appendElementXml('identifier',$_minislideshow->getIdentifier(),$doc,$_minib);
                $this->appendElementXml('images',$_minislideshow->getImages(),$doc,$_minib);
                $this->appendElementXml('slider_params',$_minislideshow->getSliderParams(),$doc,$_minib);
                $this->appendElementXml('appearance',$_minislideshow->getAppearance(),$doc,$_minib);
                $this->appendElementXml('navigation',$_minislideshow->getNavigation(),$doc,$_minib);
                $this->appendElementXml('status',$_minislideshow->getStatus(),$doc,$_minib);
                $_minir->appendChild($_minib);
            }
        }
        return $this;
    }

     /* update new layout */

    public function updateAction() {        
        $this->_initTheme();
        $this->loadLayout()->_addContent($this->getLayout()->createBlock('themeframework/adminhtml_theme_update'))
            ->_setActiveMenu('emthemes')
            ->renderLayout();
    }

    public function updatePostAction() {        
        $basetheme = $this->getRequest()->getParam('theme');        
        $model = Mage::getModel('themeframework/theme')->load($id);        
        $response = new Varien_Object();
  
            //echo "<pre>";print_r($_FILES['import_file']);            
        
        if (!empty($_FILES['update_file']['tmp_name'])) {
            try {
                $tempFile = $_FILES['update_file']['tmp_name'];                    
                try {
                    $text = file_get_contents($tempFile);
                    $xml = simplexml_load_string($text);
                    
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError('Can\'t load XML.');                        
                    $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));                        
                }
                if (empty($xml)) {
                    Mage::getSingleton('adminhtml/session')->addError('Invalid File.');                        
                    $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));                      
                }
                else{
                    $general = Mage::helper('themeframework/managetheme')->_xmlToArray($xml->general);
                    $general = $general['general'];
                    //echo "<pre>"; var_dump($general);exit;
                    if($general['base_theme'] && $general['identifier'] && $general['package'])
                    {        
                        $identifier = $general['identifier']; 
                        $model = Mage::getModel('themeframework/theme')->load($identifier,'identifier');                                                                    
                        if($general['base_theme'] == $basetheme && !$model->getId())
                        {                        
                            // compare base theme if same theme, do install new layout                               
                            $data = array();                                                                    
                            $settings = Mage::helper('themeframework/managetheme')->_xmlToArray($xml->settings);   

                            $data = array_merge($general,$settings['settings']);
                            $model = Mage::getModel('themeframework/theme');
                            $model->setData($data);
                            $model->setId(null);
                            $model->save();     
                                                
                            $xmlData = $xml->sample_data;
                            //echo "<pre>";print_r($xmlData);exit;
                            $importHelper = Mage::helper('themeframework/import');
                            $importHelper->importThemeFrameworkArea($xmlData);
                           
                            $importHelper->importStaticContent($xmlData);
                           
                            $importHelper->importMegaMenu($xmlData);
                           
                            $importHelper->importSlideshow($xmlData);                
                           
                            $importHelper->updateWidgetTabsId();                
                           
                            $importHelper->importFlexibleBlock($xmlData);
                            Mage::getSingleton('adminhtml/session')->addSuccess('Update New Layout Successfully.');                        
                            $this->_redirect('*/*/', array('_current' => true, 'theme' => $basetheme));   
                                                               
                        }
                        else
                        {                             
                            Mage::getSingleton('adminhtml/session')->addError('Please check file init.xml. This layout hasn\'t same basetheme with this theme or this layout already exists.');                        
                            $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));                                       
                        }                                                                                                
                    }
                    else {                        
                        $_pblock = Mage::helper('themeframework/managetheme')->_xmlToArray($xml->permission_block);                        
                        if(count($_pblock)){
                            if((string)$xml->permission_block['id'] == $this->getRequest()->getParam('theme') && Mage::getModel('admin/block')){
                                Mage::helper('themeframework/import')->installPermissionBlock($this->getRequest()->getParam('theme'),true);
                                Mage::getSingleton('adminhtml/session')->addSuccess('Update Permission Block Successfully.');                        
                                $this->_redirect('*/*/', array('_current' => true, 'theme' => $basetheme));                                
                            }else{
                                Mage::getSingleton('adminhtml/session')->addError('This file is not belong to this theme.');                        
                                $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));
                            }
                        }else{
                            Mage::getSingleton('adminhtml/session')->addError('Invalid init.xml file.');                        
                            $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));
                        }                                  
                    }
                }

            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());                        
                $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Invalid file upload attempt');                        
                $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));                                   
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError('Invalid file upload attempt');                        
            $this->_redirect('*/*/update', array('_current' => true, 'theme' => $basetheme));                                    
            
        }     
    }
  
}