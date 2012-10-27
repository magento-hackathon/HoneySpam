<?php
class Hackathon_HoneySpam_Block_Honeypot extends Mage_Core_Block_Template
{
    protected $_template = 'hackathon/honeyspam/honeypot.phtml';

    protected function _construct()
    {
        parent::_construct();
    }

    public function getHoneypotName()
    {
        /* @var $helper Hackathon_HoneySpam_Helper_Data */
        $helper = Mage::helper('hackathon_honeyspam');
        return $helper->getHoneypotName();
    }


}
