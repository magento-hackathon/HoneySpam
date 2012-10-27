<?php
class Hackathon_HoneySpam_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_INPUT_NAME = 'hackathon/honeyspam/honeypotName';

    public function getHoneypotName()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_INPUT_NAME);
    }
}