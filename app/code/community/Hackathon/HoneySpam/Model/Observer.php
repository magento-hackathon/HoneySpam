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
     * @var Hackathon_HoneySpam_Helper_Data
     */
    protected $helper;

    /**
     * Hackathon_HoneySpam_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->helper = Mage::helper('hackathon_honeyspam');
    }

    /**
     * call rules
     */
    public function controllerActionPredispatchCustomerAccountCreatepost()
    {
        if ($this->helper->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }

        if ($this->helper->isHoneypotAccountCreateTimeEnabled()) {
            $this->_checkTimestamp();
        }

        if ($this->helper->isSpamIndexingEnabled()) {
            $this->_indexLoginParams();
        }
    }

    /**
     * Check if honeypot is filled
     *
     * Used for:
     *  - controllerActionPredispatchBlockReviewForm
     *  - controllerActionPredispatchCustomerAccountForgotPasswordPost
     *  - controllerActionPredispatchContactsIndexPost
     *  - controllerActionPredispatchNewsletterSubscriberNew
     */
    public function checkHoneypot()
    {
        if ($this->helper->isHoneypotNameEnabled()) {
            $this->_checkHoneypot();
        }
    }

    /**
     * validate honeypot field
     */
    protected function _checkHoneypot()
    {
        if (strlen(Mage::app()->getRequest()->getParam($this->helper->getHoneypotName()))) {
            $this->helper->log('Honeypot Input filled. Aborted.',Zend_Log::WARN);

            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index','error','honeyspam');
            throw $e;
        }
    }

    /**
     * validate time
     */
    protected function _checkTimestamp()
    {
        $session = Mage::getSingleton('customer/session');
        $accountCreateTime = $this->helper->getHoneypotAccountCreateTime();
        if (
            !$session->getAccountCreateTime(false) || ($session->getAccountCreateTime() > (time() - $accountCreateTime))
        ) {
            $this->helper->log('Honeypot Timestamp filled. Aborted.', Zend_Log::WARN);

            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index','error','honeyspam');
            throw $e;
        }
    }

    /**
     * set access timestamp
     */
    public function controllerActionPredispatchCustomerAccountCreate()
    {
        $session = Mage::getSingleton('customer/session');
        $session->setAccountCreateTime(time());
    }

    // Invoke indexing
    public function _indexLoginParams()
    {
        $checker = Mage::getModel('hackathon_honeyspam/checker');

        $return = $checker->init(Mage::app()->getRequest()->getParams());

        if ($return >= $this->helper->getSpamIndexLevel()) {
            $this->helper->log("Honeypot spam index at $return. Aborted.", Zend_Log::WARN);

            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index','error','honeyspam');
            throw $e;
        }
    }
}
