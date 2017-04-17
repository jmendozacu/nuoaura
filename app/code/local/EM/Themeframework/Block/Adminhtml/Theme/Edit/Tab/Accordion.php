<?php
/**
 * EMThemes
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the framework to newer
 * versions in the future. If you wish to customize the framework for your
 * needs please refer to http://www.emthemes.com/ for more information.
 *
 * @category    EM
 * @package     EM_ThemeFramework
 * @copyright   Copyright (c) 2012 CodeSpot JSC. (http://www.emthemes.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Giao L. Trinh (giao.trinh@emthemes.com)
 */
class EM_Themeframework_Block_Adminhtml_Theme_Edit_Tab_Accordion
    extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{



    /**
     * Class constructor
     *
     */
    public function __construct()
    {


        parent::__construct();
        $this->_fieldSetCollection = array();
        $this->_titleFieldSet = '';
        $this->_comment = '';
        $this->setTemplate('em_themeframework/theme/edit/theme.phtml');

    }





    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_titleFieldSet;
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_titleFieldSet;
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $fieldsetKey = $this->getFieldset();

        $this->_fieldSetCollection = $this->getData($fieldsetKey);
        $open = true;
        if(count($this->_fieldSetCollection)>1)
             $open = false;
        foreach($this->_fieldSetCollection as $index => $valueSet)
        {   
            $show = true;         
            if(isset($valueSet['show']))
                $show = (bool)($valueSet['show']);
            if($show){
                $this->_titleFieldSet = $valueSet['label'];
                if(isset($valueSet['comment']))
                    $this->_comment = $valueSet['comment'];                
                $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
                    ->setId($index);
                $accordion->addItem($index, array(
                    'title'   =>  $valueSet['label'],
                    'content' => $this->getLayout()
                            ->createBlock('themeframework/adminhtml_theme_edit_tab_accordion_form')
                            ->setData($index,$valueSet['fields'])
                            ->setData('comment',$this->_comment)
                            ->setData('field',$index)
                            ->toHtml(),
                    'open'    => $open,
                ));
                $this->setChild('accordion_'.$index, $accordion);
            }
        }

        return parent::_toHtml();
    }

    public function getFieldsetCollection()
    {
       return $this->_fieldSetCollection;
    }

    public function getNotice()
    {        
        return $this->getComment();
    }

}
