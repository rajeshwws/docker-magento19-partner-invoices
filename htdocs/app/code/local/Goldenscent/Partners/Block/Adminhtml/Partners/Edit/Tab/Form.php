<?php
  
class Goldenscent_Partners_Block_Adminhtml_Partners_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('partners_form', array('legend'=>Mage::helper('partners')->__('Partner information')));
        
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('partners')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
        
        if ( Mage::getSingleton('adminhtml/session')->getPartnersData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPartnersData());
            Mage::getSingleton('adminhtml/session')->setPartnersData(null);
        } elseif ( Mage::registry('partners_data') ) {
            $form->setValues(Mage::registry('partners_data')->getData());
        }
        return parent::_prepareForm();
    }
} 