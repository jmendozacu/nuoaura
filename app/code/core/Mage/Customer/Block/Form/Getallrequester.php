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
 * Customer edit form block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Form_Getallrequester extends Mage_Customer_Block_Account_Dashboard
{

	public function getAllRequesterData(){
	
	/*$collection = mage::getModel('customer/customer')->getCollection()
	   ->addAttributeToSelect('email')
	   ->addAttributeToFilter('firstname', 'sander')
	   ->addAttributeToSort('email', 'ASC');
	var_dump((string)$collection->getSelect());
	This will print the following query
	
	SELECT e.*, at_firstname.value AS firstname FROM  customer_entity AS e INNER JOIN customer_entity_varchar AS  at_firstname ON (at_firstname.entity_id = e.entity_id) AND (at_firstname.attribute_id = '5') WHERE (e.entity_type_id = '1') AND (at_firstname.value = 'sander') ORDER BY e.email ASC
	*/	
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$approver = $customer->getId();
		
		$requester = Mage::getResourceModel('customer/customer_collection')
     	 	->addAttributeToFilter('approver', $approver)
      		->addNameToSelect()
      		->load();

	   
    	return $requester->getData();
	   
	   //return $approvers;
	}

}
