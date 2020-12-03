<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Hackathon
 * @package   Hackathon_HoneySpam
 * @author    Andreas Emer <honeyspam@emdec.de>
 * @author    Fabian Blechschmidt <hackathon@fabian-blechschmidt.de>
 * @author    Sascha Wohlgemuth <sascha.wohlgemuth@gmail.com>
 * @author    Bastian Ike <bastian.ike@gmail.com>
 * @author    Peter Ukener <peterukener@gmail.com>
 * @copyright 2012 Magento Hackathon
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.magento-hackathon.de/
 */
class Hackathon_HoneySpam_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_PATH_INPUT_NAME = 'hackathon/honeyspam/honeypotName';
    const CONFIG_PATH_ENABLE_POT = 'hackathon/honeyspam/enableHoneypotName';
    const CONFIG_PATH_ENABLE_TIME = 'hackathon/honeyspam/enableHoneypotAccountCreateTime';
    const CONFIG_PATH_ENABLE_SPAM = 'hackathon/honeyspam/enableSpamIndexing';
    const CONFIG_PATH_INDEX_LEVEL = 'hackathon/honeyspam/spamIndexLevel';
    const CONFIG_PATH_CREATE_TIME = 'hackathon/honeyspam/honeypotAccountCreateTime';
    const CONFIG_PATH_ENABLE_LOG = 'hackathon/honeyspam/enableLogging';
    const CONFIG_PATH_LOG_FILE = 'hackathon/honeyspam/logfile';

    /**
     * @return bool
     */
    public function isHoneypotNameEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLE_POT);
    }

    /**
     * @return bool
     */
    public function isSpamIndexingEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLE_SPAM);
    }

    /**
     * @return bool
     */
    public function isHoneypotAccountCreateTimeEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLE_TIME);
    }

    /**
     * @return string
     */
    public function getSpamIndexLevel()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_INDEX_LEVEL);
    }

    /**
     * @return string
     */
    public function getHoneypotAccountCreateTime()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_CREATE_TIME);
    }

    /**
     * @param string $message
     * @param int    $level
     */
    public function log($message, $level = Zend_Log::INFO)
    {
        if ($this->isLoggingEnabled()) {
            Mage::log($message, $level, $this->getLogFilename());
        }
    }

    /**
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLE_LOG);
    }

    /**
     * @return string
     */
    public function getLogFilename()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_LOG_FILE);
    }

    /**
     * @return bool
     */
    public function isHoneypotFilled()
    {
        return strlen($this->_getRequest()->getParam($this->getHoneypotName()));
    }

    /**
     * @return string
     */
    public function getHoneypotName()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_INPUT_NAME);
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return $this->_getRequest()->getParams();
    }
}
