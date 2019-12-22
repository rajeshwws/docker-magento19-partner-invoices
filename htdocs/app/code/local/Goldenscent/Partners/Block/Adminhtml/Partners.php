<?php
  
class Goldenscent_Partners_Block_Adminhtml_Partners extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_partners';
        $this->_blockGroup = 'partners';
        $this->_headerText = Mage::helper('partners')->__('Partner Manager');
        $this->_addButtonLabel = Mage::helper('partners')->__('Add Partner Name');
        parent::__construct();
    }
} 