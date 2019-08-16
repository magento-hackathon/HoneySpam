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
 * @category  Hackathon
 * @package   Hackathon_HoneySpam
 * @author    Andreas Emer <honeyspam@emdec.de>
 * @author    Fabian Blechschmidt <hackathon@fabian-blechschmidt.de>
 * @author    Sascha Wohlgemuth <sascha.wohlgemuth@gmail.com>
 * @author    Bastian Ike <bastian.ike@gmail.com>
 * @author    Peter Ukener <peterukener@gmail.com>
 * @copyright 2012 Magento Hackathon
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.magento-hackathon.de/
 */
class Hackathon_HoneySpam_Model_Observer
{
    /**
     * call rules
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function controllerActionPredispatchCustomerAccountCreatepost()
    {
        /** @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');
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
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function controllerActionPredispatchBlockReviewForm()
    {
        /** @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');
        if ($helper->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }
    }

    /**
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function controllerActionPredispatchCustomerAccountForgotPasswordPost()
    {
        /** @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');
        if ($helper->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }
    }

    /**
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function controllerActionPredispatchContactsIndexPost()
    {
        /** @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');
        if ($helper->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }
    }

    /**
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function controllerActionPredispatchNewsletterSubscriberNew()
    {
        /** @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');
        if ($helper->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }
    }

    /**
     * validate honeypot field
     * @throws Mage_Core_Controller_Varien_Exception
     */
    protected function _checkHoneypot()
    {
        /* @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');
        if (strlen(Mage::app()->getRequest()->getParam($helper->getHoneypotName()))) {
            $helper->log('Honeypot Input filled. Aborted.', Zend_Log::WARN);

            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'honeyspam');
            throw $e;
        }
    }

    /**
     * validate time
     * @throws Mage_Core_Controller_Varien_Exception
     */
    protected function _checkTimestamp()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        /* @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');

        $accountCreateTime = $helper->getHoneypotAccountCreateTime();
        if ($session->getData('account_create_time', false)
            && ($session->getData('account_create_time') > (time() - $accountCreateTime))
        ) {
            $helper->log('Honeypot Timestamp filled. Aborted.', Zend_Log::WARN);

            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'honeyspam');
            throw $e;
        }
    }

    /**
     * set access timestamp
     */
    public function controllerActionPredispatchCustomerAccountCreate()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');
        $session->setData('account_create_time', time());
    }

    /**
     * Invoke indexing
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function _indexLoginParams()
    {
        /** @var Hackathon_HoneySpam_Model_Checker $checker */
        $checker = Mage::getModel('hackathon_honeyspam/checker');

        /* @var Hackathon_HoneySpam_Helper_Data $helper */
        $helper = Mage::helper('hackathon_honeyspam');

        $return = $checker->init(Mage::app()->getRequest()->getParams());

        if ($return >= $helper->getSpamIndexLevel()) {
            $helper->log("Honeypot spam index at $return. Aborted.", Zend_Log::WARN);

            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'honeyspam');
            throw $e;
        }
    }
}
