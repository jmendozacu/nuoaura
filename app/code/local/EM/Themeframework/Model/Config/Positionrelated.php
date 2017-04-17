<?php
 
class EM_Themeframework_Model_Config_Positionrelated
{
	public function toOptionArray()
    {
		return array(
			array('label' => "Below The Brand Logo Block", 'value' => "below_brand"),
			array('label' => "At The Side Of The Tabs", 'value' => "beside_tabs"),	
            array('label' => "Below The Static Block Custom", 'value' => "below_block"),		
            array('label' => "At The Top Of The Tabs", 'value' => "top_tabs"),
            array('label' => "At The Sidebar (Left Or Right)", 'value' => "sidebar"),
		);
    }
 
}
