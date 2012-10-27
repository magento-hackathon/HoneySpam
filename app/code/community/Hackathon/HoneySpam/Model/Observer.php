<?php
class Hackathon_HoneySpam_Model_Observer
{
    /**
     * call rules
     *
     * @param $observer Varien_Event_Observer
     */
    public function controllerActionPredispatchCustomerAccountCreatepost($observer)
    {
        if (Mage::getStoreConfig('hackathon/honeyspam/enableHoneypotName')) {
            $this->_checkHoneypot($observer);
        }

        if (Mage::getStoreConfig('hackathon/honeyspam/enableHoneypotAccountCreateTime')) {
            $this->_checkTimestamp($observer);
        }
    }

    /**
     * validate honeypot field
     *
     * @param $observer Varien_Event_Observer
     */
    protected function _checkHoneypot($observer)
    {
        /* @var $helper Hackathon_HoneySpam_Helper_Data */
        $helper = Mage::helper('hackathon_honeyspam');
        if (strlen(Mage::app()->getRequest()->getParam($helper->getHoneypotName()))) {
            Mage::throwException('Honeypot filled. Aborted.');
        }
    }

    /**
     * validate time
     *
     * @param $observer Varien_Event_Observer
     */
    protected function _checkTimestamp($observer)
    {
        $session = Mage::getSingleton('customer/session');
        $accountCreateTime = Mage::getStoreConfig('hackathon/honeyspam/honeypotAccountCreateTime');
        if (!$session->getAccountCreateTime(false) || ($session->getAccountCreateTime() > (time() - $accountCreateTime))) {
            Mage::throwException('Honeypot catched');
        }
    }

    /**
     * set access timestamp
     *
     * @param $observer Varien_Event_Observer
     */
    public function controllerActionPredispatchCustomerAccountCreate($observer)
    {
        $session = Mage::getSingleton('customer/session');
        $session->setAccountCreateTime(time());
    }
}
