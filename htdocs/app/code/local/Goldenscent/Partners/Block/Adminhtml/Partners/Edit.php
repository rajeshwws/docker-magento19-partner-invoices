<?php
  
class Goldenscent_Partners_Block_Adminhtml_Partners_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                
        $this->_objectId = 'id';
        $this->_blockGroup = 'partners';
        $this->_controller = 'adminhtml_partners';
  
        $this->_updateButton('save', 'label', Mage::helper('partners')->__('Save Partner'));
        $this->_updateButton('delete', 'label', Mage::helper('partners')->__('Delete Partner'));
    }
  
    public function getHeaderText()
    {
        if( Mage::registry('partners_data') && Mage::registry('partners_data')->getId() ) {
            return Mage::helper('partners')->__("Edit Partner '%s'", $this->htmlEscape(Mage::registry('partners_data')->getTitle()));
        } else {
            return Mage::helper('partners')->__('Add Partner');
        }
    }
} 