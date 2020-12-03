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
 * @category    Hackathon
 * @package     Hackathon_HoneySpam
 * @author      Andreas Emer <honeyspam@emdec.de>
 * @author      Fabian Blechschmidt <hackathon@fabian-blechschmidt.de>
 * @author      Sascha Wohlgemuth <sascha.wohlgemuth@gmail.com>
 * @author      Bastian Ike <bastian.ike@gmail.com>
 * @author      Peter Ukener <peterukener@gmail.com>
 * @copyright   2012 Magento Hackathon
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link        http://www.magento-hackathon.de/
 */
class Hackathon_HoneySpam_Model_Observer
{
    /**
     * call rules
     *
     * @throws Hackathon_HoneySpam_Exception
     */
    public function checkHoneypotCustomerAccountCreatepost()
    {
        $helper = $this->getHelper();
        if ($helper->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }

        if ($helper->isHoneypotAccountCreateTimeEnabled()) {
            $this->_checkTimestamp();
        }

        if ($helper->isSpamIndexingEnabled()) {
            $this->_indexLoginParams();
        }
    }

    /**
     * @return Hackathon_HoneySpam_Helper_Data
     */
    private function getHelper()
    {
        return Mage::helper('hackathon_honeyspam');
    }

    /**
     * validate honeypot field
     *
     * @throws Hackathon_HoneySpam_Exception
     */
    protected function _checkHoneypot()
    {
        $helper = $this->getHelper();
        if ($helper->isHoneypotFilled()) {
            $helper->log('Honeypot Input filled. Aborted.', Zend_Log::WARN);

            $e = new Hackathon_HoneySpam_Exception();
            $e->prepareRedirect('honeyspam/error/index');
            throw $e;
        }
    }

    /**
     * validate time
     *
     * @throws Hackathon_HoneySpam_Exception
     */
    protected function _checkTimestamp()
    {
        $helper  = $this->getHelper();
        $session = $this->getCustomerSession();

        if (!$session->getData('account_create_time', false)
            || ($session->getData('account_create_time') > (time() - $helper->getHoneypotAccountCreateTime()))
        ) {
            $helper->log('Honeypot Timestamp filled. Aborted.', Zend_Log::WARN);

            $e = new Hackathon_HoneySpam_Exception();
            $e->prepareRedirect('honeyspam/error/index');
            throw $e;
        }
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    private function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Invoke indexing
     *
     * @throws Hackathon_HoneySpam_Exception
     */
    protected function _indexLoginParams()
    {
        $helper = $this->getHelper();
        $return = $this->getCheckerModel()->init($helper->getRequestParams());
        if ($return >= $helper->getSpamIndexLevel()) {
            $helper->log("Honeypot spam index at $return. Aborted.", Zend_Log::WARN);

            $e = new Hackathon_HoneySpam_Exception();
            $e->prepareRedirect('honeyspam/error/index');
            throw $e;
        }
    }

    /**
     * @return Hackathon_HoneySpam_Model_Checker
     */
    private function getCheckerModel()
    {
        return Mage::getModel('hackathon_honeyspam/checker');
    }

    /**
     * @throws Hackathon_HoneySpam_Exception
     */
    public function checkHoneypot()
    {
        if ($this->getHelper()->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }
    }

    /**
     * set access timestamp
     */
    public function checkHoneypotCustomerAccountCreate()
    {
        $this->getCustomerSession()->setData('account_create_time', time());
    }
}
