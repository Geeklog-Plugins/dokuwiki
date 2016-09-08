<?php
/**
* glFusion CMS
*
* DokuWiki Integration
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2006-2015 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*/

if (!defined ('VERSION')) {
    die ('This file can not be used on its own!');
}

/**
* Generate salt for hash generation
*/
function _hash_gensalt_private($input, &$itoa64, $iteration_count_log2 = 6)
{
    if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31) {
        $iteration_count_log2 = 8;
    }

    $output = '$H$';
    $output .= $itoa64[min($iteration_count_log2 + 5, 30)];
    $output .= _hash_encode64($input, 6, $itoa64);

    return $output;
}

/**
* Encode hash
*/
function _hash_encode64($input, $count, &$itoa64)
{
    $output = '';
    $i = 0;

    do {
        $value = ord($input[$i++]);
        $output .= $itoa64[$value & 0x3f];

        if ($i < $count) {
            $value |= ord($input[$i]) << 8;
        }

        $output .= $itoa64[($value >> 6) & 0x3f];

        if ($i++ >= $count) {
            break;
        }

        if ($i < $count) {
            $value |= ord($input[$i]) << 16;
        }

        $output .= $itoa64[($value >> 12) & 0x3f];

        if ($i++ >= $count) {
            break;
        }

        $output .= $itoa64[($value >> 18) & 0x3f];
    } while ($i < $count);

    return $output;
}

/**
* The crypt function/replacement
*/
function _hash_crypt_private($password, $setting, &$itoa64)
{
    $output = '*';

    // Check for correct hash
    if (substr($setting, 0, 3) != '$H$') {
        return $output;
    }

    $count_log2 = strpos($itoa64, $setting[3]);

    if ($count_log2 < 7 || $count_log2 > 30) {
        return $output;
    }

    $count = 1 << $count_log2;
    $salt = substr($setting, 4, 8);

    if (strlen($salt) != 8) {
        return $output;
    }

    /**
    * We're kind of forced to use MD5 here since it's the only
    * cryptographic primitive available in all versions of PHP
    * currently in use.  To implement our own low-level crypto
    * in PHP would result in much worse performance and
    * consequently in lower iteration counts and hashes that are
    * quicker to crack (by non-PHP code).
    */
    $hash = md5($salt . $password, true);
    do {
        $hash = md5($hash . $password, true);
    }
    while (--$count);

    $output = substr($setting, 0, 12);
    $output .= _hash_encode64($hash, 16, $itoa64);

    return $output;
}

/**
* Return unique id
* @param string $extra additional entropy
*/
function _unique_id($extra = 'c')
{
    static $dss_seeded = false;
    global $_SYSTEM;

    $rand_seed = COM_makesid();

    $val = $rand_seed . microtime();
    $val = md5($val);
    $rand_seed = md5($rand_seed . $val . $extra);

    return substr($val, 4, 16);
}

?>