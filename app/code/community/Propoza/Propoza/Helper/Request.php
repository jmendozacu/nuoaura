<?php

class Propoza_Propoza_Helper_Request extends Mage_Core_Helper_Abstract
{
    public function prepareQuoteRequest($quote)
    {
        $to_propoza_quote = array();
        $to_propoza_quote['Quote'] = $this->prepareQuote($quote);
        $to_propoza_quote['Quote']['Requester'] = $this->prepareRequester(Mage::getSingleton('customer/session')->getCustomer());
        $to_propoza_quote['Quote']['Product'] = $this->prepareProducts($quote);
        $to_propoza_quote['Quote']['shop_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        return $to_propoza_quote;
    }

    private function prepareQuote($quote)
    {
        $to_propoza_quote = array();
        $to_propoza_quote ['shop_quote_id'] = $quote->getId();
        $to_propoza_quote['cart_currency'] = Mage::app()->getStore()->getBaseCurrencyCode();
        $to_propoza_quote['include_default_store_tax'] = Mage::getStoreConfig('tax/calculation/price_includes_tax', Mage::app()->getStore()->getId());
        return $to_propoza_quote;
    }

    public function prepareRequester($customer)
    {
        $requester = array();
        $requester['firstname'] = $customer->getFirstname();
        $requester['middlename'] = $customer->getMiddlename();
        $requester['lastname'] = $customer->getLastname();
        $requester['email'] = $customer->getEmail();
        $requester['company'] = $this->getCurrentCustomerCompany($customer);
        return $requester;
    }

    public function getCurrentCustomerCompany($customer)
    {
        $company = null;
        if ($this->getCurrentCustomerBillingAddress($customer)) {
            $company = $this->getCurrentCustomerBillingAddress($customer)->getCompany();
        } else if ($this->getCurrentCustomerShippingAddress($customer)) {
            $company = $this->getCurrentCustomerShippingAddress($customer)->getCompany();
        }
        return $company;
    }

    public function getCurrentCustomerBillingAddress($customer)
    {
        $billing_address_id = $customer->getDefaultBilling();
        if ($billing_address_id) {
            return Mage::getModel('customer/address')->load($billing_address_id);
        } else {
            return false;
        }
    }

    public function getCurrentCustomerShippingAddress($customer)
    {
        $shipping_address_id = $customer->getDefaultShipping();
        if ($shipping_address_id) {
            return Mage::getModel('customer/address')->load($shipping_address_id);
        } else {
            return false;
        }
    }

    public function prepareProducts($quote)
    {
        $products = array();
        $count = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $products[$count] = $this->prepareProduct($item);
            $products[$count]['Child'] = $this->prepareChildren($item);
            $count++;
        }

        return $products;
    }

    public function prepareProduct($item)
    {
        $product = array();
        $product['name'] = $item->getName();
        $product['original_price'] = $item->getBaseOriginalPrice() !== null ? $item->getBaseOriginalPrice() : 0;
        $product['sku'] = $item->getSku();
        $product['quantity'] = $item->getQty();
        $product['ProductAttribute'] = $this->prepareProductAttributes($item);
        return $product;
    }

    public function prepareProductAttributes($item)
    {
        $productAttribtues = array();
        $orderOptions = ($item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct()));
        $counter = 0;
        if (isset($orderOptions['options'])) {
            foreach ($orderOptions['options'] as $option) {
                $productAttribtues[$counter]['name'] = $option['label'];
                $productAttribtues[$counter]['value'] = $option['print_value'];
                $counter++;
            }
        }
        if (isset($orderOptions['attributes_info'])) {
            foreach ($orderOptions['attributes_info'] as $attribute) {
                $productAttribtues[$counter]['name'] = $attribute['label'];
                $productAttribtues[$counter]['value'] = $attribute['value'];
                $counter++;
            }
        }
        return $productAttribtues;
    }

    public function prepareChildren($parent)
    {
        $children = array();
        if (count($parent->getChildren()) > 0) {
            foreach ($parent->getChildren() as $child) {
                if ($parent->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $children[] = $this->prepareProduct($child);
                }
            }
        }
        return $children;
    }


    /**
     * Get url to set quote as ordered request to
     * @return string
     */
    public function getQuoteOrderedUrl()
    {
        return sprintf('%s/api/MagentoQuotes/edit.json', Mage::helper('propoza')->getDashboardPropozaUrl());
    }
}