<?php
class MF_Flexibleblock_Model_Fblock_Attribute_Source_Pattern extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions(){
		return $this->getPatternsToOptionsArray();
	}
	
	/*
     * Returns array of schedule patterns
     * @return array As 'pattern code' => 'pattern name'
     */
    public function getPatternsToOptionsArray()
    {
		$helper = Mage::helper('flexibleblock');
        return array(
            'every day'     => $helper->__('Every day'),
            'odd days'      => $helper->__('Odd days of the month'),
            'even days'     => $helper->__('Even days of the month'),
            '1'             => $helper->__('On 1'),
            '2'             => $helper->__('On 2'),
            '3'             => $helper->__('On 3'),
            '4'             => $helper->__('On 4'),
            '5'             => $helper->__('On 5'),
            '6'             => $helper->__('On 6'),
            '7'             => $helper->__('On 7'),
            '8'             => $helper->__('On 8'),
            '9'             => $helper->__('On 9'),
            '10'            => $helper->__('On 10'),
            '11'            => $helper->__('On 11'),
            '12'            => $helper->__('On 12'),
            '13'            => $helper->__('On 13'),
            '14'            => $helper->__('On 14'),
            '15'            => $helper->__('On 15'),
            '16'            => $helper->__('On 16'),
            '17'            => $helper->__('On 17'),
            '18'            => $helper->__('On 18'),
            '19'            => $helper->__('On 19'),
            '20'            => $helper->__('On 20'),
            '21'            => $helper->__('On 21'),
            '22'            => $helper->__('On 22'),
            '23'            => $helper->__('On 23'),
            '24'            => $helper->__('On 24'),
            '25'            => $helper->__('On 25'),
            '26'            => $helper->__('On 26'),
            '27'            => $helper->__('On 27'),
            '28'            => $helper->__('On 28'),
            '29'            => $helper->__('On 29'),
            '30'            => $helper->__('On 30'),
            '31'            => $helper->__('On 31'),
            '1,11,21'       => $helper->__('On 1, 11, and 21st of the month'),
            '1,11,21,31'    => $helper->__('On 1, 11, 21, and 31st of the month'),
            '10,20,30'      => $helper->__('On 10, 20, and 30th of the month'),
            'su'            => $helper->__('On Sundays'),
            'mo'            => $helper->__('On Mondays'),
            'tu'            => $helper->__('On Tuesdays'),
            'we'            => $helper->__('On Wednesdays'),
            'th'            => $helper->__('On Thursdays'),
            'fr'            => $helper->__('On Fridays'),
            'sa'            => $helper->__('On Saturdays'),
            'mon-fri'       => $helper->__('From Monday to Friday'),
            'sat-sun'       => $helper->__('On Saturdays and Sundays'),
            'tue-fri'       => $helper->__('From Tuesday to Friday'),
            'mon-sat'       => $helper->__('From Monday to Saturday'),
            'mon,wed,fri'   => $helper->__('On Mondays, Wednesdays, and Fridays'),
            'tue,thu,sat'   => $helper->__('On Tuesdays, Thursdays, and Saturdays'),
        );
    }
}