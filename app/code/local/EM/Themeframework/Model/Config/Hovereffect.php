<?php
class EM_Themeframework_Model_Config_Hovereffect
{
    public function toOptionArray()
    {
        return array(
            array('label' => 'Enable On All Devices', 'value' => 'enable'),
            array('label' => 'Disable On All Devices', 'value' => 'disable'),
            array('label' => 'Disable Below 1200px', 'value' => 'medium_desktop'),
            array('label' => 'Disable Below 992px', 'value' => 'tablet'),
            array('label' => 'Disable Below 768px', 'value' => 'mobile'),
        );
    }

}
