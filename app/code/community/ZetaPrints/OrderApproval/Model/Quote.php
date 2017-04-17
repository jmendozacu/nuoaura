<?php

class ZetaPrints_OrderApproval_Model_Quote extends Mage_Sales_Model_Quote {

  /**
   * Load only approved items and store them as items_collection to use them
   * int the quote.
   * 
   * getItemsCollection() method returns items_collection if it's set, otherwise
   * it returns $_items field.
   *
   * @return Mage_Sales_Model_Quote
   */
  public function useOnlyApprovedItems () {

    //Save quote items collection if it was loaded previously to store
    //possible changes in quote items before loading collection with approved
    //items only
    if ($this->_items !== null)
      $this
        ->getItemsCollection()
        ->save();

    $items = Mage::getModel('sales/quote_item')
               ->getCollection()
               ->addFieldToFilter('approved', 1)
               ->setQuote($this);

    //Save collection as item_collection in the quote.
    //See getItemsCollection() method.
    $this->setItemsCollection($items);

    return $this;
  }


  



  public function setIsActive ($active) {
    if ($active || !$this->hasItemsCollection())
      return parent::setIsActive($active);

    //Change quote active state only then it contains only approved items,
    //otherwise ignore it

    $approvedItems = $this->getItemsCollection();

    $this->setItemsCollection(null);

    $allItemsNumber = count($this->getItemsCollection());
    $approvedItemsNumber = count($approvedItems);

    $this->setItemsCollection($approvedItems);

    return $allItemsNumber == $approvedItemsNumber
             ? parent::setIsActive($active)
               : $this;
  }

  /**
   * Retrieve quote items collection
   *
   * NOTE: the method adds backward compatibility
   *       with Magento < 1.7
   *
   * @param   bool $useCache
   * @return  Mage_Eav_Model_Entity_Collection_Abstract
   */
  public function getItemsCollection($useCache = true) {
    if ($this->hasItemsCollection())
      return $this->getData('items_collection');

    return parent::getItemsCollection($useCache);
  }





 
        public function getPendingOrders(){

                        $approverId = Mage::getSingleton('customer/session')
                                     ->getCustomer()
                                     ->getId();

                        $groups = Mage::getResourceModel('customer/group_collection')
                                 ->addFieldToFilter('approver_id', $approverId)
                                 ->load();

                        $_customers = array();

                        foreach ($groups as $group)
                                $_customers += Mage::getResourceModel('customer/customer_collection')
                                              ->addNameToSelect()
                                              ->addAttributeToFilter('group_id', $group->getId())
                                              ->getItems();

                                $_customers += Mage::getResourceModel('customer/customer_collection')
                                               ->addNameToSelect()
                                               ->addAttributeToFilter('approver', $approverId)
                                               ->getItems();

                                $customers = new Varien_Data_Collection();

                                foreach ($_customers as $customer) {
                                        $quote = Mage::getModel('sales/quote')
                                                ->setStoreId(Mage::app()->getStore()->getId())
                                                ->loadByCustomer($customer->getId());

                                $pending= 0;
      
                               
                                $itemCollection= $quote->getItemsCollection();
                                foreach($itemCollection as $item){

                                    if('Pending approval'==Mage::helper('orderapproval')->getNoticeFromItem($item))
                                     $pending++;
                                      }

     


     

                               if($pending > 0){
                                    $customer->setUnapprovedItemsNumber($pending);

                                    $customers->addItem($customer);

                               }

      

                     }


                  return $customers;



          }



public function cloneQuote(){

     return  Mage::getModel('checkout/cart')->getQuote();
   }










}

?>
