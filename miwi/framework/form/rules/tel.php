<?php
/*
* @package		Miwi Framework
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @copyright	Copyright (C) 2005-2012 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('MIWI') or die('MIWI');

class MFormRuleTel extends MFormRule {

    public function test(&$element, $value, $group = null, &$input = null, &$form = null) {
        // If the field is empty and not required, the field is valid.
        $required = ((string)$element['required'] == 'true' || (string)$element['required'] == 'required');
        if (!$required && empty($value)) {
            return true;
        }
        // @see http://www.nanpa.com/
        // @see http://tools.ietf.org/html/rfc4933
        // @see http://www.itu.int/rec/T-REC-E.164/en

        // Regex by Steve Levithan
        // @see http://blog.stevenlevithan.com/archives/validate-phone-number
        // @note that valid ITU-T and EPP must begin with +.
        $regexarray = array('NANP'  => '/^(?:\+?1[-. ]?)?\(?([2-9][0-8][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/',
                            'ITU-T' => '/^\+(?:[0-9] ?){6,14}[0-9]$/', 'EPP' => '/^\+[0-9]{1,3}\.[0-9]{4,14}(?:x.+)?$/');
        if (isset($element['plan'])) {

            $plan = (string)$element['plan'];
            if ($plan == 'northamerica' || $plan == 'us') {
                $plan = 'NANP';
            }
            elseif ($plan == 'International' || $plan == 'int' || $plan == 'missdn' || !$plan) {
                $plan = 'ITU-T';
            }
            elseif ($plan == 'IETF') {
                $plan = 'EPP';
            }

            $regex = $regexarray[$plan];
            // Test the value against the regular expression.
            if (preg_match($regex, $value) == false) {

                return false;
            }
        }
        else {
            //If the rule is set but no plan is selected just check that there are between
            //7 and 15 digits inclusive and no illegal characters (but common number separators
            //are allowed).
            $cleanvalue = preg_replace('/[+. \-(\)]/', '', $value);
            $regex      = '/^[0-9]{7,15}?$/';
            if (preg_match($regex, $cleanvalue) == true) {

                return true;
            }
            else {

                return false;
            }
        }

        return true;
    }
}
