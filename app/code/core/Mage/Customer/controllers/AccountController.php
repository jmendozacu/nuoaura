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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
    const CUSTOMER_ID_SESSION_NAME = "customerId";
    const TOKEN_SESSION_NAME = "token";

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('loginPost', 'createpost');

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'changeforgotten',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Action postdispatch
     *
     * Remove No-referer flag from customer session after each action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }

    /**
     * Default customer account page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }

    /**
     * Customer login form page
     */
    public function loginAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /**
     * Login post action
     */
    public function loginPostAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
			
				/* Tapan ****************************/				
				$resource     = Mage::getSingleton('core/resource');	
				/**
				 * Retrieve the read connection
				 */
				$connection   = $resource->getConnection('core_read');			
				/**
				 * Retrieve our table name 
				 */
				//$table = $resource->getTableName('catalog/product');
				$table = $resource->getTableName('customer_entity');				
				/**
				 * Set the product ID
				 */
				$groupId = 4;
				
				$query = 'SELECT count(*) as cnt FROM ' . $table . ' WHERE email = "' . $login['username'] .  '" and group_id = '
						 . (int)$groupId . ' LIMIT 1';
				
				/**
				 * Execute the query and store the result in $sku
				 */
				$_cnt = $connection->fetchOne($query);
				
				if($_cnt==0){
				
				/* Tapan ends **********************/
			
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
				
				} else {
                $session->addError($this->__('We are reviewing your registration. You can login after admin approval. Thank You.'));
				
            }
				
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect();
    }

    /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl($this->_getHelper('customer')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = $this->_getModel('core/url')
                            ->getRebuiltUrl( $this->_getHelper('core')->urlDecodeAndEscape($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl( $this->_getHelper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() ==  $this->_getHelper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl( $this->_getHelper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }

    /**
     * Customer logout action
     */
    public function logoutAction()
    {
        $session = $this->_getSession();
        $session->logout()->renewSession();

        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }
        $this->_redirect('*/*/logoutSuccess');
    }

    /**
     * Logout success page
     */
    public function logoutSuccessAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer register form page
     */
    public function createAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*');
            return;
        }
		

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));

        if (!$this->_validateFormKey()) {
            $this->_redirectError($errUrl);
            return;
        }

        /** @var $session Mage_Customer_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->_redirectError($errUrl);
            return;
        }

        $customer = $this->_getCustomer();

        try {
            $errors = $this->_getCustomerErrors($customer);

            if (empty($errors)) {
                $customer->cleanPasswordsValidationData();
				$customer->setIsApprover(1);//tapan add for is_approver
				$customer->setApprover(NULL);////tapan add for approver is blank							
                $customer->save();				
				
				/************ Tapan***************/	
				$insertId = (int) $customer->getId();
				$comp_   = trim($this->getRequest()->getPost('company'));                    
				$customerGroupCode = substr($comp_, 0, 28);
				/*$collection->addAttributeToFilter('name', array(
					array('like' => '% '.$needle.' %'), //spaces on each side
					array('like' => '% '.$needle), //space before and ends with $needle
					array('like' => $needle.' %') // starts with needle and space after
				));
				*/
				$collection = Mage::getModel('customer/group')->getCollection() 
							->addFieldToFilter('customer_group_code',array(
					array('like' => ''.$customerGroupCode.'%'),
					));// filter by group code
				//$group = Mage::getModel('customer/group')->load($collection->getFirstItem()->getId()); 
		
				//print_r(count($collection));
				
				$already_created = count($collection);
				$already_created = (int) $already_created;
				
				//$new_no = $already_created+1;
						
				if($already_created>0)
				{				
					$collectionlast = Mage::getModel('customer/group')->getCollection() 
								 ->setOrder('customer_group_id', 'DESC')
								 ->addFieldToFilter('customer_group_code',array(
									array('like' => ''.$customerGroupCode.'%'),
								));
					$collectionlast->getSelect()->limit(1);									
					//print_r($collectionlast->getData());			
					$last_code =  $collectionlast->getData()[0]['customer_group_code'];
					
					$last_code_arr = explode(" ", $last_code);
					if(!isset($last_code_arr[1]))
						$new_no = 2;
					else
						$new_no = (int)$last_code_arr[1]+1;		
					
					$customerGroupCode = $customerGroupCode . " " . $new_no;
				}
					
				$taxClass = 3;
				$customerGroup = Mage::getModel('customer/group');
				$customerGroup->setCode($customerGroupCode);
				//$customerGroup->setApproverId($insertId);
				$customerGroup->setTaxClassId($taxClass)->save();
				$insertGroupId = (int) $customerGroup->getId();
				
				/** tapan :: Update user group id **/				
				$email = trim($this->getRequest()->getPost('email'));
				$customerU = Mage::getModel('customer/customer');
				$customerU->setWebsiteId(Mage::app()->getWebsite()->getId());
				$customerU->loadByEmail($email);
				if($customerU->getId()){     
					 $customerU->setGroupId($insertGroupId); 
					 $customerU->save();
				}
				/** Update user group id **/
				                
        
				// reindex
                // `php -f /var/www/html/shell/indexer.php reindexall`; 
				$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
				$process->reindexEverything();	
				//$id = 0;
				//$customerData = Mage::getSingleton('customer/session')->getCustomer();				
     			//$id = 5;//$customerData->getId();
				
				/*$insertId = (int) $customer->getId();        
				
				$resource     = Mage::getSingleton('core/resource');
				$connection   = $resource->getConnection('core_write');
				$table        = $resource->getTableName('custom_customer_type');
				$query        = "INSERT INTO {$table} (`entity_id`, `customer_type`, `approver_id`) VALUES ('$insertId', '0', '$insertId');";
				$connection->multiQuery($query);
				*/
				
				/*
				$write = Mage::getSingleton("core/resource")->getConnection("core_write");
				// Concatenated with . for readability
				$query = "insert into custom_customer_type "
					   . "(customer_id, approver_id) values "
					   . "(:customer_id, :approver_id)";
				
				$binds = array(
					'customer_id'   => $id,
					'approver_id'   => $id
				);
				$write->query($query, $binds); */        		
				//$model_ = Mage::getModel('customer/custom_customer_type')->setData($data_);
				
				
				
				/*$resource_ = Mage::getResourceModel('customer/custom_customer_type')->setData($data_);          		
				try{
					$resource_->save();					
				}
				catch(Exception $e){
					echo $e->getMessage();
				}*/
		
				/*$connection->insert(
					"custom_customer_type",
					$data_						
				);
				/*******************************/
				
                $this->_dispatchRegisterSuccess($customer);
                $this->_successProcessRegistration($customer);
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
            } else {
                $message = $this->_escapeHtml($e->getMessage());
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            $session->addException($e, $this->__('Cannot save the customer.'));
        }

        $this->_redirectError($errUrl);
    }

    /**
     * Success Registration
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_AccountController
     */
    protected function _successProcessRegistration(Mage_Customer_Model_Customer $customer)
    {
        $session = $this->_getSession();
        if ($customer->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store*/
            $store = $app->getStore();
            $customer->sendNewAccountEmail(
                'confirmation',
                $session->getBeforeAuthUrl(),
                $store->getId()
            );
            $customerHelper = $this->_getHelper('customer');
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
             $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
            $url = $this->_getUrl('*/*/index', array('_secure' => true));
        } else {
            $session->setCustomerAsLoggedIn($customer);
            $url = $this->_welcomeCustomer($customer);
        }
        $this->_redirectSuccess($url);
        return $this;
    }
	
	protected function _successProcessRequesterRegistration(Mage_Customer_Model_Customer $customer, Mage_Customer_Model_Customer $approver)
    {
        $session = $this->_getSession();
        if ($customer->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store*/
            $store = $app->getStore();
			//$session->getBeforeAuthUrl()
            $customer->sendNewAccountEmail(
                'confirmation',
                '',
                $store->getId()
            );
            $customerHelper = $this->_getHelper('customer');
            //$session->addSuccess($this->__('Account confirmation is required. Please, inform your requester to check  email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
           $session->addSuccess($this->__('Account confirmation is required. Please, inform your requester to check  email for the confirmation link.',
             $customerHelper->getEmailConfirmationUrl($customer->getEmail())));
			 $session->setCustomerAsLoggedIn($approver);
            $url = $this->_getUrl('*/*/index', array('_secure' => true));
        } else {
           // $session->setCustomerAsLoggedIn($customer);
            $url = $this->_welcomeCustomer($customer);
        }
        $this->_redirectSuccess($url);
        return $this;
    }

    /**
     * Get Customer Model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        $customer = $this->_getFromRegistry('current_customer');
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        return $customer;
    }

    /**
     * Add session error method
     *
     * @param string|array $errors
     */
    protected function _addSessionError($errors)
    {
        $session = $this->_getSession();
        $session->setCustomerFormData($this->getRequest()->getPost());
        if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
                $session->addError($this->_escapeHtml($errorMessage));
            }
        } else {
            $session->addError($this->__('Invalid customer data'));
        }
    }

    /**
     * Escape message text HTML.
     *
     * @param string $text
     * @return string
     */
    protected function _escapeHtml($text)
    {
        return Mage::helper('core')->escapeHtml($text);
    }

    /**
     * Validate customer data and return errors if they are
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array|string
     */
    protected function _getCustomerErrors($customer)
    {
        $errors = array();
        $request = $this->getRequest();
        if ($request->getPost('create_address')) {
            $errors = $this->_getErrorsOnCustomerAddress($customer);
        }
        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);
        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($customerData);
            $customer->setPassword($request->getPost('password'));
            $customer->setPasswordConfirmation($request->getPost('confirmation'));
            $customerErrors = $customer->validate();
            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }
        return $errors;
    }

    /**
     * Get Customer Form Initalized Model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerForm($customer)
    {
        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = $this->_getModel('customer/form');
        $customerForm->setFormCode('customer_account_create');
        $customerForm->setEntity($customer);
        return $customerForm;
    }

    /**
     * Get Helper
     *
     * @param string $path
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($path)
    {
        return Mage::helper($path);
    }

    /**
     * Get App
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return Mage::app();
    }

    /**
     * Dispatch Event
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _dispatchRegisterSuccess($customer)
    {
        Mage::dispatchEvent('customer_register_success',
            array('account_controller' => $this, 'customer' => $customer)
        );
    }

    /**
     * Gets customer address
     *
     * @param $customer
     * @return array $errors
     */
    protected function _getErrorsOnCustomerAddress($customer)
    {
        $errors = array();
        /* @var $address Mage_Customer_Model_Address */
        $address = $this->_getModel('customer/address');
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = $this->_getModel('customer/form');
        $addressForm->setFormCode('customer_register_address')
            ->setEntity($address);

        $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
        $addressErrors = $addressForm->validateData($addressData);
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        $address->setId(null)
            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        $addressForm->compactData($addressData);
        $customer->addAddress($address);

        $addressErrors = $address->validate();
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        return $errors;
    }

    /**
     * Get model by path
     *
     * @param string $path
     * @param array|null $arguments
     * @return false|Mage_Core_Model_Abstract
     */
    public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
    }

    /**
     * Get model from registry by path
     *
     * @param string $path
     * @return mixed
     */
    protected function _getFromRegistry($path)
    {
        return Mage::registry($path);
    }

    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess(
            $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );
        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType =  $this->_getHelper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation',
                        $this->_getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = $this->_getUrl('*/*/index', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Confirm customer account by id and confirmation key
     */
    public function confirmAction()
    {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_getSession()->logout()->regenerateSessionId();
        }
        try {
            $id      = $this->getRequest()->getParam('id', false);
            $key     = $this->getRequest()->getParam('key', false);
            $backUrl = $this->getRequest()->getParam('back_url', false);
            if (empty($id) || empty($key)) {
                throw new Exception($this->__('Bad request.'));
            }

            // load customer by id (try/catch in case if it throws exceptions)
            try {
                $customer = $this->_getModel('customer/customer')->load($id);
                if ((!$customer) || (!$customer->getId())) {
                    throw new Exception('Failed to load customer by id.');
                }
            }
            catch (Exception $e) {
                throw new Exception($this->__('Wrong customer account specified.'));
            }

            // check if it is inactive
            if ($customer->getConfirmation()) {
                if ($customer->getConfirmation() !== $key) {
                    throw new Exception($this->__('Wrong confirmation key.'));
                }

                // activate customer
                try {
                    $customer->setConfirmation(null);
                    $customer->save();
                }
                catch (Exception $e) {
                    throw new Exception($this->__('Failed to confirm customer account.'));
                }

                // log in and send greeting email, then die happy
                //$session->setCustomerAsLoggedIn($customer);
                //$successUrl = "home";//$this->_welcomeCustomer($customer, true);
                //$this->_redirectSuccess($backUrl ? $backUrl : $successUrl);
				$this->_redirectSuccess($this->_getUrl('*/*/index', array('_secure' => true)));
                return;
            }

            // die happy
            $this->_redirectSuccess($this->_getUrl('*/*/index', array('_secure' => true)));
            return;
        }
        catch (Exception $e) {
            // die unhappy
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectError($this->_getUrl('*/*/index', array('_secure' => true)));
            return;
        }
    }

    /**
     * Send confirmation link to specified email
     */
    public function confirmationAction()
    {
        $customer = $this->_getModel('customer/customer');
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        // try to confirm by email
        $email = $this->getRequest()->getPost('email');
        if ($email) {
            try {
                $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
                if (!$customer->getId()) {
                    throw new Exception('');
                }
                if ($customer->getConfirmation()) {
                    $customer->sendNewAccountEmail('confirmation', '', Mage::app()->getStore()->getId());
                    $this->_getSession()->addSuccess($this->__('Please, check your email for confirmation key.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('This email does not require confirmation.'));
                }
                $this->_getSession()->setUsername($email);
                $this->_redirectSuccess($this->_getUrl('*/*/index', array('_secure' => true)));
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $this->__('Wrong email.'));
                $this->_redirectError($this->_getUrl('*/*/*', array('email' => $email, '_secure' => true)));
            }
            return;
        }

        // output form
        $this->loadLayout();

        $this->getLayout()->getBlock('accountConfirmation')
            ->setEmail($this->getRequest()->getParam('email', $email));

        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Get Url method
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }

    /**
     * Forgot customer password page
     */
    public function forgotPasswordAction()
    {
        $this->loadLayout();

        $this->getLayout()->getBlock('forgotPassword')->setEmailValue(
            $this->_getSession()->getForgottenEmail()
        );
        $this->_getSession()->unsForgottenEmail();

        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/forgotpassword');
                    return;
                }
            }
            $this->_getSession()
                ->addSuccess( $this->_getHelper('customer')
                ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                    $this->_getHelper('customer')->escapeHtml($email)));
            $this->_redirect('*/*/');
            return;
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->_redirect('*/*/forgotpassword');
            return;
        }
    }

    /**
     * Display reset forgotten password form
     *
     */
    public function changeForgottenAction()
    {
        try {
            list($customerId, $resetPasswordLinkToken) = $this->_getRestorePasswordParameters($this->_getSession());
            $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
            $this->loadLayout();
            $this->renderLayout();

        } catch (Exception $exception) {
            $this->_getSession()->addError($this->_getHelper('customer')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/forgotpassword');
        }
    }

    /**
     * Checks reset forgotten password token
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email.
     *
     */
    public function resetPasswordAction()
    {
        try {
            $customerId = (int)$this->getRequest()->getQuery("id");
            $resetPasswordLinkToken = (string)$this->getRequest()->getQuery('token');

            $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
            $this->_saveRestorePasswordParameters($customerId, $resetPasswordLinkToken)
                ->_redirect('*/*/changeforgotten');

        } catch (Exception $exception) {
            $this->_getSession()->addError($this->_getHelper('customer')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/forgotpassword');
        }
    }

    /**
     * Reset forgotten password
     * Used to handle data recieved from reset forgotten password form
     */
    public function resetPasswordPostAction()
    {
        list($customerId, $resetPasswordLinkToken) = $this->_getRestorePasswordParameters($this->_getSession());
        $password = (string)$this->getRequest()->getPost('password');
        $passwordConfirmation = (string)$this->getRequest()->getPost('confirmation');

        try {
            $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
        } catch (Exception $exception) {
            $this->_getSession()->addError($this->_getHelper('customer')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/');
            return;
        }

        $errorMessages = array();
        if (iconv_strlen($password) <= 0) {
            array_push($errorMessages, $this->_getHelper('customer')->__('New password field cannot be empty.'));
        }
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_getModel('customer/customer')->load($customerId);

        $customer->setPassword($password);
        $customer->setPasswordConfirmation($passwordConfirmation);
        $validationErrorMessages = $customer->validate();
        if (is_array($validationErrorMessages)) {
            $errorMessages = array_merge($errorMessages, $validationErrorMessages);
        }

        if (!empty($errorMessages)) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
            foreach ($errorMessages as $errorMessage) {
                $this->_getSession()->addError($errorMessage);
            }
            $this->_redirect('*/*/changeforgotten');
            return;
        }

        try {
            // Empty current reset password token i.e. invalidate it
            $customer->setRpToken(null);
            $customer->setRpTokenCreatedAt(null);
            $customer->cleanPasswordsValidationData();
            $customer->save();

            $this->_getSession()->unsetData(self::TOKEN_SESSION_NAME);
            $this->_getSession()->unsetData(self::CUSTOMER_ID_SESSION_NAME);

            $this->_getSession()->addSuccess($this->_getHelper('customer')->__('Your password has been updated.'));
            $this->_redirect('*/*/login');
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('Cannot save a new password.'));
            $this->_redirect('*/*/changeforgotten');
            return;
        }
    }

    /**
     * Check if password reset token is valid
     *
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @throws Mage_Core_Exception
     */
    protected function _validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken)
    {
        if (!is_int($customerId)
            || !is_string($resetPasswordLinkToken)
            || empty($resetPasswordLinkToken)
            || empty($customerId)
            || $customerId < 0
        ) {
            throw Mage::exception('Mage_Core', $this->_getHelper('customer')->__('Invalid password reset token.'));
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_getModel('customer/customer')->load($customerId);
        if (!$customer || !$customer->getId()) {
            throw Mage::exception('Mage_Core', $this->_getHelper('customer')->__('Wrong customer account specified.'));
        }

        $customerToken = $customer->getRpToken();
        if (strcmp($customerToken, $resetPasswordLinkToken) != 0 || $customer->isResetPasswordLinkTokenExpired()) {
            throw Mage::exception('Mage_Core', $this->_getHelper('customer')->__('Your password reset link has expired.'));
        }
    }

    /**
     * Forgot customer account information page
     */
    public function editAction()
    {	
			//$customerGroupCode = "Gen";
	
					
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('customer_edit');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
		
		if (!empty($data)) {
            $customer->addData($data);
        }
        if ($this->getRequest()->getParam('changepass') == 1) {
            $customer->setChangePassword(1);
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }

    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = $this->_getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                // If password change was requested then add it to common validation scheme
                if ($this->getRequest()->getParam('change_password')) {
                    $currPass   = $this->getRequest()->getPost('current_password');
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');

                    $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                    if ( $this->_getHelper('core/string')->strpos($oldPass, ':')) {
                        list($_salt, $salt) = explode(':', $oldPass);
                    } else {
                        $salt = false;
                    }

                    if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                        if (strlen($newPass)) {
                            /**
                             * Set entered password and its confirmation - they
                             * will be validated later to match each other and be of right length
                             */
                            $customer->setPassword($newPass);
                            $customer->setPasswordConfirmation($confPass);
                        } else {
                            $errors[] = $this->__('New password field cannot be empty.');
                        }
                    } else {
                        $errors[] = $this->__('Invalid current password');
                    }
                }

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                $customer->cleanPasswordsValidationData();
                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));

                $this->_redirect('customer/account');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('*/*/edit');
    }
	/**
     * Tapan get all requestor for a customer/approver
     */
    public function getAllRequesterAction()
    {   
		$session = $this->_getSession();
		 /**
		 * Tapan Checking is logged customer is aprrover?
		 */
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		 
		    $customerData = Mage::getSingleton('customer/session')->getCustomer();
		 	$is_approver = $customerData->getIsApprover();
       		$resource = Mage::getSingleton('core/resource');	
			/**
			 * Retrieve the read connection
			 */
			$readConnection = $resource->getConnection('core_read');
			
			/*$query = 'SELECT * FROM custom_customer_type where entity_id='. (int)$id ;
			
			$results = $readConnection->fetchAll($query);
         	if($results[0]['customer_type']==1){*/
			if(!$is_approver){
				return $this->_redirect('*/*/index');			
			}
		}
		else
			return $this->_redirect('*/*/index');
		/**
		 * Tapan Checking ends
		 */
		 
		 
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('customer_getallrequester');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
		
		//var_dump($customer->getData()['entity_id']);

        $this->getLayout()->getBlock('head')->setTitle($this->__('Requester List'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
	
	public function deleteRequesterAction()
    { 
	
		if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) 
			$id = $_REQUEST['id'];
		else
			return $this->_redirect('*/*/index');
			
			 
		$session = $this->_getSession();
		 /**
		 * Tapan Checking is logged customer is aprrover?
		 */
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		 
		    $customer = $this->_getSession()->getCustomer();
		
			$approver=  $customer->getData()['entity_id'];
			
			$requester = Mage::getResourceModel('customer/customer_collection')
				->addAttributeToFilter('entity_id', $id)
				->addAttributeToFilter('approver', $approver)
				->addNameToSelect()
				->load();		 
			
			$is_approver = count($requester->getData());
			
			if(!$is_approver){
				return $this->_redirect('*/*/index');			
			}
			else
			{
				//$requester = Mage::getModel('customer/customer')->load($id);
		 		$requester->delete();
				$session->addError($this->__('Requester Successfully deleted.'));
				
            
				return $this->_redirect('*/*/getallrequester');
			}
		}
		else
			return $this->_redirect('*/*/index');
		/**
		 * Tapan Checking ends
		 */
		 
		 
    }
	
	
	
	/**
     * Tapan add customer  requester account information page 
     */
    public function addRequesterAction()
    {   
		$session = $this->_getSession();
		 /**
		 * Tapan Checking is logged customer is aprrover?
		 */
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		 
		    $customerData = Mage::getSingleton('customer/session')->getCustomer();
		 	$is_approver = $customerData->getIsApprover();
       		$resource = Mage::getSingleton('core/resource');	
			/**
			 * Retrieve the read connection
			 */
			$readConnection = $resource->getConnection('core_read');
			
			/*$query = 'SELECT * FROM custom_customer_type where entity_id='. (int)$id ;
			
			$results = $readConnection->fetchAll($query);
         	if($results[0]['customer_type']==1){*/
			if(!$is_approver){
				$session->addError($this->__('You are not authorized to add a requester.'));
				return $this->_redirect('*/*/index');			
			}
		}
		else
			return $this->_redirect('*/*/index');
		/**
		 * Tapan Checking ends
		 */
		 
		 
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('customer_addrequester');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
		
		//var_dump($customer->getData()['entity_id']);

        if (!empty($data)) {
            $customer->addData($data);
        }
        if ($this->getRequest()->getParam('changepass') == 1) {
            $customer->setChangePassword(1);
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
	
	
	/**
     * Change customer password action
     */
    public function addRequesterPostAction()
	{
	
		 $session = $this->_getSession();
		 /**
		 * Tapan Checking is logged customer is aprrover?
		 */
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		 
		    $customerData = Mage::getSingleton('customer/session')->getCustomer();
		 	$is_approver = $customerData->getIsApprover();
       		$resource = Mage::getSingleton('core/resource');	
			/**
			 * Retrieve the read connection
			 */
			$readConnection = $resource->getConnection('core_read');
			
			/*$query = 'SELECT * FROM custom_customer_type where entity_id='. (int)$id ;
			
			$results = $readConnection->fetchAll($query);
         	if($results[0]['customer_type']==1){*/
			if(!$is_approver){
				$session->addError($this->__('You are not authorized to add a requester.'));
				return $this->_redirect('*/*/index');			
			}
		}
		else
			return $this->_redirect('*/*/index');
		/**
		 * Tapan Checking ends
		 */
		 
		if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/addrequester');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $approver = $this->_getSession()->getCustomer();

            /** @var $customerForm Mage_Customer_Model_Form */
           //// $customerForm = $this->_getModel('customer/form');
            //$customerForm->setFormCode('customer_account_addrequester')->setEntity($customer);

           // $customerData = $customerForm->extractData($this->getRequest());
			
			//var_dump($this->getRequest()->getParams());
			
			
            $errors = array();
            $customerErrors = true;//$customerForm->validateData($customerData);
		   
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } 
			else
			{
                //$customerForm->compactData($customerData);
                $errors = array();
                // If password change was requested then add it to common validation scheme
                $confPass   = $this->getRequest()->getPost('confirmation'); 
				
				$firstname    = $this->getRequest()->getPost('firstname');
                $lastname    = $this->getRequest()->getPost('lastname');
               	$newPass    = $this->getRequest()->getPost('password');
                $email    = $this->getRequest()->getPost('email');
				
				if (!strlen($firstname)) 
				{
					$errors[] = $this->__('First Name field cannot be empty.');
				}
				
				if (!strlen($lastname)) 
				{
					$errors[] = $this->__('Last Name field cannot be empty.');
				}
				
				if (strlen($email)) 
				{
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						  $errors[] = "Invalid email format."; 
					}
					else
					{
						$resource     = Mage::getSingleton('core/resource');	
						$connection   = $resource->getConnection('core_read');			
						$table = $resource->getTableName('customer_entity');			
						$query = 'SELECT count(*) as cnt FROM ' . $table . ' WHERE email = "' . $email . '" LIMIT 1';				
						
						$_cnt = $connection->fetchOne($query);
						
						if($_cnt==1){
							$errors[] = "There is already an account with this email address.";						
						}					
					}
					
                } 
				else 
				{
                     $errors[] = $this->__('Email field cannot be empty.');
                }

                               

                if (strlen($newPass)) 
				{
					/**
					 * Set entered password and its confirmation - they
					 * will be validated later to match each other and be of right length
					 */
					//$customer->setPassword($newPass);
					//$customer->setPasswordConfirmation($confPass);
                 } 
				 else 
				 {
                     $errors[] = $this->__('Password field cannot be empty.');
                 }

                // Validate account and compose list of errors if any
               /* $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }*/
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
				
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/addRequester');
                return $this;
            }

            try {
                  //$customer->save();
				
				$firstname    = $this->getRequest()->getPost('firstname');
                $lastname    = $this->getRequest()->getPost('lastname');
               	$newPass    = $this->getRequest()->getPost('password');
                $email    = $this->getRequest()->getPost('email');
                
				
				$customer = Mage::getModel("customer/customer");				
				
				$app_customer = $this->_getSession()->getCustomer();
		
				$approver_id = $app_customer->getData()['entity_id'];					
				$approver_group = (int)$app_customer->getData()['group_id'];
				
				$address_id = $customer->getDefaultBilling();
				if ((int)$address_id){
		   			$approver_address = Mage::getModel('customer/address')->load($address_id);						
					//$approver_company = $approver_address['company'];
					$approver_company = $approver_address->getCompany();					
				}					
					
				
				$customer   ->setFirstname($firstname)
							->setMiddleName($middlename)
							->setLastname($lastname)
							->setEmail($email)
							->setPassword($newPass)
							->setApprover($approver_id)
							->setGroupId($approver_group);
				 
				try{
					$customer->cleanPasswordsValidationData();
                
					$customer->save();
					
					$customer_address = Mage::getModel("customer/address");
					
					$customer_address->setCustomerId($customer->getId())
					->setFirstname($firstname)
					->setMiddleName($middlename)
					->setLastname($lastname)
					->setCompany($approver_company)
					->setIsDefaultBilling('1')
					->setIsDefaultShipping('1')
					->setSaveInAddressBook('1');
					
					$customer_address->save();
				
					
					
					/*$insertId = (int) $customer->getId();				
					$resource     = Mage::getSingleton('core/resource');
					$connection   = $resource->getConnection('core_write');
					$table        = $resource->getTableName('custom_customer_type');
					$query        = "INSERT INTO {$table} (`entity_id`, `customer_type`, `approver_id`) VALUES ('$insertId', '1', '$approver_id');";
					$connection->multiQuery($query);*/					
				}
				
				catch (Exception $e) {
					Zend_Debug::dump($e->getMessage());
				}
				
				
				
				
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));


				$this->_dispatchRegisterSuccess($customer);
                $this->_successProcessRequesterRegistration($customer, $app_customer);
				
                $this->_redirect('customer/account/addrequester');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the requester.'));
            }
       }

        //$this->_redirect('*/*/addrequester');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    protected function _isVatValidationEnabled($store = null)
    {
        return  $this->_getHelper('customer/address')->isVatValidationEnabled($store);
    }

    /**
     * Get restore password params.
     *
     * @param Mage_Customer_Model_Session $session
     * @return array array ($customerId, $resetPasswordToken)
     */
    protected function _getRestorePasswordParameters(Mage_Customer_Model_Session $session)
    {
        return array(
            (int) $session->getData(self::CUSTOMER_ID_SESSION_NAME),
            (string) $session->getData(self::TOKEN_SESSION_NAME)
        );
    }

    /**
     * Save restore password params to session.
     *
     * @param int $customerId
     * @param  string $resetPasswordLinkToken
     * @return $this
     */
    protected function _saveRestorePasswordParameters($customerId, $resetPasswordLinkToken)
    {
        $this->_getSession()
            ->setData(self::CUSTOMER_ID_SESSION_NAME, $customerId)
            ->setData(self::TOKEN_SESSION_NAME, $resetPasswordLinkToken);

        return $this;
    }
	
	
	/**
     * Forgot customer change password
     */
    public function editPasswordAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('customer_editpassword');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
        if (!empty($data)) {
            $customer->addData($data);
        }
        if ($this->getRequest()->getParam('changepass') == 1) {
            $customer->setChangePassword(1);
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('Change Password'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }

    /**
     * Change customer password action
     */
    public function editPasswordPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = $this->_getModel('customer/form');
            $customerForm->setFormCode('customer_account_editpassword')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = true;//$customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                // If password change was requested then add it to common validation scheme
                if ($this->getRequest()->getParam('change_password')) {
                    $currPass   = $this->getRequest()->getPost('current_password');
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');

                    $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                    if ( $this->_getHelper('core/string')->strpos($oldPass, ':')) {
                        list($_salt, $salt) = explode(':', $oldPass);
                    } else {
                        $salt = false;
                    }

                    if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                        if (strlen($newPass)) {
                            /**
                             * Set entered password and its confirmation - they
                             * will be validated later to match each other and be of right length
                             */
                            $customer->setPassword($newPass);
                            $customer->setPasswordConfirmation($confPass);
                        } else {
                            $errors[] = $this->__('New password field cannot be empty.');
                        }
                    } else {
                        $errors[] = $this->__('Invalid current password');
                    }
                }

                // Validate account and compose list of errors if any
               $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/editpassword');
                return $this;
            }

            try {
                $customer->cleanPasswordsValidationData();
                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The password information has been saved.'));

                $this->_redirect('customer/account');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('*/*/editpassword');
    }

}
