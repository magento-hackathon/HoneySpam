<?php
class Hackathon_HoneySpam_Model_Observer
{
    public function checkHoneypot()
    {
        /* @var $helper Hackathon_HoneySpam_Helper_Data */
        $helper = Mage::helper('hackathon_honeyspam');
        if (strlen(Mage::app()->getRequest()->getParam($helper->getHoneypotName()))) {
            Mage::throwException('Honeypot filled. Aborted.');
        }
    }
}