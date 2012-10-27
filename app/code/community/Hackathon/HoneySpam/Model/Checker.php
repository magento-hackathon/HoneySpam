<?php
/**
 * Created by JetBrains PhpStorm.
 * User: blaber
 * Date: 27.10.12
 * Time: 15:44
 * To change this template use File | Settings | File Templates.
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
            if ($lastname = $firstname) {
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
                $_index += 0.5;
                Mage::log("SPAM: " . $param . " has more than 15 Characters");
            } else if (is_numeric($param)) { // Param contains numbers only == spam!
                $_index += 0.5;
                Mage::log("SPAM: " . $param . " contains only numbers");
            } else if (preg_match("([b-df-hj-np-tv-z]{3})", $param)) { // More than 3 consecutive consonants == Spam!
                $_index += 0.5;
                Mage::log("SPAM: " . $param . " contains 3 or more consecutive consonants");
            } else if (preg_match("([aeiou]{3})", $param)) { // More than 3 consecutive vouwels == spam
                $_index += 0.5;
                Mage::log("SPAM: " . $param . " contains 3 consecutive vowels");
            } else if (preg_match("([A-Z]{2,})", substr($param, -4))) { // At least two CAPITALS at the end of a string == Spam!
                $_index += 0.5;
                Mage::log("SPAM: " . $param . " has at least 2 CAPITAL letters at the end");
            } else if (preg_match_all("([A-Z])", $param)) { // Param contains more than 3 Capital letters at all
                $_index += 0.5;
                Mage::log("SPAM: " . $param . " contains more than 3 CAPITALS at all");
            }
        }

        return $_index;
    }
}