<?php
 
class EM_Themeframework_Model_Config_Displayelement
{
	public function toOptionArray()
    {
		return array(
			array('label' => "Don't Display Completely", 'value' => '0'),
			array('label' => 'Display', 'value' => '1'),			
			array('label' => 'Display On Hover', 'value' => '2'),			
		);
    }
 
}
