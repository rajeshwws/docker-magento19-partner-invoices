<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';
class Goldenscent_Partners_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{

    public function saveAction()
    {

        $data = $this->getRequest()->getPost('shipment');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $shipment = $this->_initShipment();
            if (!$shipment) {
                $this->_forward('noRoute');
                return;
            }

            $comment = '';
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            $shipmentCreatedMessage = $this->__('The shipment has been created.');
            $labelCreatedMessage = $this->__('The shipping label has been created.');

            //split to groups
            $shipments = $this->_initShipmentGroups($shipment);

            foreach ($shipments as $shipment) {
                $shipment->register();
                if (!empty($data['send_email'])) {
                    $shipment->setEmailSent(true);
                }
                $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));

                $this->_saveShipment($shipment);
                $shipment->sendEmail(!empty($data['send_email']), $comment);
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
            }
            $this->_getSession()->addSuccess($isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage
                    : $shipmentCreatedMessage);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot save shipment.'));
            $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));

    }

    protected function _initShipmentGroups(Mage_Sales_Model_Order_Shipment $shipment)
    {
        
        $order = $shipment->getOrder();
        $qtys = $this->_getItemQtys();
        $shipmentGroups = array();

        if (!empty($order->getPartnerName())) {

            $allItems = array();

            foreach ($qtys as $itemId => $qty) {
                while ($qty--) $allItems[] = $itemId;
            }

            $partnerItems = array();
            $webShopItems = array();

            for ($i = 0; $i < count($allItems); $i++) { 
                if($i % 2 == 0) {
                    $webShopItems[] = $allItems[$i];
                } else {
                    $partnerItems[] = $allItems[$i];
                }
            }

            if (!empty($webShopItems)) {
                $groupItems = array_count_values($webShopItems);
                $shipmentGroups[] =  Mage::getModel('sales/service_order', $order)->prepareShipment($groupItems);
            }

            if (!empty($partnerItems)) {
                $groupItems = array_count_values($partnerItems);
                $shipmentGroups[] =  Mage::getModel('sales/service_order', $order)->prepareShipment($groupItems);
            }
        } else {
             $shipmentGroups[] =  Mage::getModel('sales/service_order', $order)->prepareShipment($qtys);
        }

        return $shipmentGroups;
    }
}