<?php 
class EM_Themeframework_Model_Config_Loginform
{
	public function toOptionArray()
    {
		return array(
			array('label' => "Don't Show Login Form", 'value' => "0"),
			array('label' => "Show Login Form When Hovering", 'value' => "1"),
			array('label' => "Show Popup Login Form When Clicking", 'value' => "2"),			
		);
    }
}
