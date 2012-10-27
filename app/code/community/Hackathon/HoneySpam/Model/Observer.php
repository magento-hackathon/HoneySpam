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
     */
    public function controllerActionPredispatchCustomerAccountCreatepost()
    {
        if (Mage::getStoreConfig('hackathon/honeyspam/enableHoneypotName')) {
            $this->_checkHoneypot();
        }

        if (Mage::getStoreConfig('hackathon/honeyspam/enableHoneypotAccountCreateTime')) {
            $this->_checkTimestamp();
        }

        $this->_indexLoginParams();
    }

    public function controllerActionPredispatchBlockReviewForm()
    {
        if (Mage::getStoreConfig('hackathon/honeyspam/enableHoneypotName')) {
            $this->_checkHoneypot();
        }

    }

    /**
     * validate honeypot field
     */
    protected function _checkHoneypot()
    {
        /* @var $helper Hackathon_HoneySpam_Helper_Data */
        $helper = Mage::helper('hackathon_honeyspam');
        if (strlen(Mage::app()->getRequest()->getParam($helper->getHoneypotName()))) {
            Mage::throwException('Honeypot filled. Aborted.');
        }
    }

    /**
     * validate time
     */
    protected function _checkTimestamp()
    {
        $session = Mage::getSingleton('customer/session');
        $accountCreateTime = Mage::getStoreConfig('hackathon/honeyspam/honeypotAccountCreateTime');
        if (
            !$session->getAccountCreateTime(false) || ($session->getAccountCreateTime() > (time() - $accountCreateTime))
        ) {
            Mage::throwException('Honeypot catched');
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
    public function _indexLoginParams() {

        $checker = Mage::getModel('hackathon_honeypot/checker');

        Mage::log("Index of login is: " + $checker->init(Mage::app()->getRequest()->getParams()));

    }
}
