<?php
  
class Goldenscent_Partners_Block_Adminhtml_Partners_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  
    public function __construct()
    {
        parent::__construct();
        $this->setId('partners_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('partners')->__('Partner Information'));
    }
  
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('partners')->__('Partner Information'),
            'title'     => Mage::helper('partners')->__('Partner Information'),
            'content'   => $this->getLayout()->createBlock('partners/adminhtml_partners_edit_tab_form')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}