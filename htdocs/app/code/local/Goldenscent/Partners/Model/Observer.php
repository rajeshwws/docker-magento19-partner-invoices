<?php
class Goldenscent_Partners_Model_Observer
{
    public function __construct()
    {
    }

    public function set_partner_cookie($observer)
    {   
        $partnerName = Mage::app()->getRequest()->getParam('partner');

        if(!empty($partnerName)) {

            $partnersModel = Mage::getModel('partners/partners')->load($partnerName, 'title');

            if(!empty($partnersModel->getId())) {
                $cookieValue = $partnersModel->getTitle();

                $cookie = Mage::getSingleton('core/cookie');
                $cookie->set('partner', $cookieValue, 86400, '/');
            }
        }
    }

    public function save_partner_cookie($observer)
    {   
        $cookie = Mage::getSingleton('core/cookie');
        $partnerCookie = $cookie->get('partner');

        if (!empty($partnerCookie)) {
            $partnersModel = Mage::getModel('partners/partners')->load($partnerCookie, 'title');

            if(!empty($partnersModel->getId())) {
                $this->_order = $observer->getEvent()->getOrder();
                $this->_order->setPartnerName($partnerCookie)->save();
            }
        }
    }

    public function addColumnToResource(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid)
            || $block->getNameInLayout() != 'sales_order.grid'
        ) {
            return $this;
        }

        $block->addColumnAfter('partner_name', [
            'header' => $block->__('Partner Name'),
            'index' => 'partner_name',
        ], 'shipping_name');

        return $this;
    }
}