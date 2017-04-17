<?php
 
class EM_Themeframework_Model_Config_Product_Socialblock
{
	public function toOptionArray()
    {
		return array(
			array('label' => "twitter", 'value' => "twitter"),
			array('label' => "facebook", 'value' => "facebook"),
			array('label' => "linkedin", 'value' => "linkedin"),
            array('label' => "digg", 'value' => "digg"),
			array('label' => "stumbleupon", 'value' => "stumbleupon"),
			array('label' => "delicious", 'value' => "delicious"),
            array('label' => "pinterest", 'value' => "pinterest"),
			array('label' => "buffer", 'value' => "buffer"),
			array('label' => "print", 'value' => "print"),
            array('label' => "email", 'value' => "email")
		);
    }    
}
