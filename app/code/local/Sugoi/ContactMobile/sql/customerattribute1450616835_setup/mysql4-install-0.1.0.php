<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "contact_person_phone",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Mobile No",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => true,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "contact_person_phone");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 81)
		;
        $attribute->save();
	
	
	
$installer->endSetup();
	 