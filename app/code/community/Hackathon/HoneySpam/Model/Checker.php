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

class Hackathon_HoneySpam_Model_Checker extends Mage_Core_Model_Abstract {

    public function init($params) {

        $firstname = $params['firstname'];
        $lastname = $params['lastname'];
        $emailprefix = explode('@', $params['email']);
        $emailprefix = $emailprefix[0];

        $params = array($firstname, $lastname, $emailprefix);

        return $this->check($firstname, $lastname, $emailprefix, $params);
    }

    public function check($firstname, $lastname, $emailprefix, $params) {

        $_index = 0;

        // Two fields identical
        if ($firstname == $lastname) {
            $_index += 1;
            // the third one?
            if ($lastname == $emailprefix) {
                $_index += 2;
            }
        // Two fields...
        } else if ($firstname == $emailprefix) {
            $_index += 1;
            if ($lastname == $firstname) {
                // the third one?
                $_index += 2;
            }
        } else if ($lastname == $emailprefix) {
            $_index += 1;
            if ($firstname == $lastname) {
                $_index += 2;
            }
        }

        /**
         *
         * This loop checks all parameters seperately on
         * different aspects such as lenght or content
         *
         **/

        foreach ($params as $param) {
            if (strlen($param) >= 15) { // item has more than 15 chars = spam possibility increases a little
                $_index += 1;
                Mage::log("SPAM: " . $param . " has more than 15 Characters");
            }

            if (is_numeric($param)) { // Param contains numbers only == spam (heavy rating!
                $_index += 2.5;
                Mage::log("SPAM: " . $param . " contains only numbers");
            }

            if (preg_match("([b-df-hj-np-tv-z]{3})", $param, $matches)) { // More than 3 consecutive consonants == Spam!
                if (!($matches[0] == "rrm")) {  // Herrmann is okay
                    $_index += 1;
                    Mage::log("SPAM: " . $param . " contains 3 or more consecutive consonants");
                }
            }

            if (preg_match("([aeiou]{3})", $param, $matches)) { // More than 3 consecutive vouwels == spam
                if (!($matches[0] == "eie")) {
                    Mage::log("matches: " . $matches[0]); // Meier is okay
                    $_index += 1;
                    Mage::log("SPAM: " . $param . " contains 3 consecutive vowels");
                }
            }

            if (preg_match("([A-Z]{2,})", substr($param, -4))) { // At least two CAPITALS at the end of a string == Spam!
                $_index += 1;
                Mage::log("SPAM: " . $param . " has at least 2 CAPITAL letters at the end");
            }

            if (preg_match_all("([A-Z])", $param, $matches) > 3) { // Param contains more than 3 Capital letters at all
                $_index += 1;
                Mage::log("SPAM: " . $param . " contains more than 3 CAPITALS at all");
            }

            if (preg_match("([a-z])", substr($param, 1, 1)) && preg_match("([A-Z])", substr($param, 1, 1))) {   // Param starts with a lowercase+uppercase
                $_index += 1;
                Mage::log("SPAM: " . $param . " starts with a combination lc/uc. E.g. aJohn, bSmith...");
            }
        }

        return $_index;
    }
}
