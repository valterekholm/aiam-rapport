<?php
    
//random
/**
 * Random_* Compatibility Library
 * for using the new PHP 7 random_* API in PHP 5 projects
 *
 * @version 2.0.10
 * @released 2017-03-13
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 - 2017 Paragon Initiative Enterprises
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
if (!defined('PHP_VERSION_ID')) {
    // This constant was introduced in PHP 5.2.7
    $RandomCompatversion = array_map('intval', explode('.', PHP_VERSION));
    define(
        'PHP_VERSION_ID',
        $RandomCompatversion[0] * 10000
        + $RandomCompatversion[1] * 100
        + $RandomCompatversion[2]
    );
    $RandomCompatversion = null;
}
/**
 * PHP 7.0.0 and newer have these functions natively.
 */
if (PHP_VERSION_ID >= 70000) {
    return;
}
if (!defined('RANDOM_COMPAT_READ_BUFFER')) {
    define('RANDOM_COMPAT_READ_BUFFER', 8);
}
$RandomCompatDIR = dirname(__FILE__);
require_once $RandomCompatDIR . '/byte_safe_strings.php';
require_once $RandomCompatDIR . '/cast_to_int.php';
require_once $RandomCompatDIR . '/error_polyfill.php';
if (!is_callable('random_bytes')) {
    /**
     * PHP 5.2.0 - 5.6.x way to implement random_bytes()
     *
     * We use conditional statements here to define the function in accordance
     * to the operating environment. It's a micro-optimization.
     *
     * In order of preference:
     *   1. Use libsodium if available.
     *   2. fread() /dev/urandom if available (never on Windows)
     *   3. mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM)
     *   4. COM('CAPICOM.Utilities.1')->GetRandom()
     *
     * See RATIONALE.md for our reasoning behind this particular order
     */
    if (extension_loaded('libsodium')) {
        // See random_bytes_libsodium.php
        if (PHP_VERSION_ID >= 50300 && is_callable('\\Sodium\\randombytes_buf')) {
            require_once $RandomCompatDIR . '/random_bytes_libsodium.php';
        } elseif (method_exists('Sodium', 'randombytes_buf')) {
            require_once $RandomCompatDIR . '/random_bytes_libsodium_legacy.php';
        }
    }
    /**
     * Reading directly from /dev/urandom:
     */
    if (DIRECTORY_SEPARATOR === '/') {
        // DIRECTORY_SEPARATOR === '/' on Unix-like OSes -- this is a fast
        // way to exclude Windows.
        $RandomCompatUrandom = true;
        $RandomCompat_basedir = ini_get('open_basedir');
        if (!empty($RandomCompat_basedir)) {
            $RandomCompat_open_basedir = explode(
                PATH_SEPARATOR,
                strtolower($RandomCompat_basedir)
            );
            $RandomCompatUrandom = (array() !== array_intersect(
                array('/dev', '/dev/', '/dev/urandom'),
                $RandomCompat_open_basedir
            ));
            $RandomCompat_open_basedir = null;
        }
        if (
            !is_callable('random_bytes')
            &&
            $RandomCompatUrandom
            &&
            @is_readable('/dev/urandom')
        ) {
            // Error suppression on is_readable() in case of an open_basedir
            // or safe_mode failure. All we care about is whether or not we
            // can read it at this point. If the PHP environment is going to
            // panic over trying to see if the file can be read in the first
            // place, that is not helpful to us here.
            // See random_bytes_dev_urandom.php
            require_once $RandomCompatDIR . '/random_bytes_dev_urandom.php';
        }
        // Unset variables after use
        $RandomCompat_basedir = null;
    } else {
        $RandomCompatUrandom = false;
    }
    /**
     * mcrypt_create_iv()
     *
     * We only want to use mcypt_create_iv() if:
     *
     * - random_bytes() hasn't already been defined
     * - the mcrypt extensions is loaded
     * - One of these two conditions is true:
     *   - We're on Windows (DIRECTORY_SEPARATOR !== '/')
     *   - We're not on Windows and /dev/urandom is readabale
     *     (i.e. we're not in a chroot jail)
     * - Special case:
     *   - If we're not on Windows, but the PHP version is between
     *     5.6.10 and 5.6.12, we don't want to use mcrypt. It will
     *     hang indefinitely. This is bad.
     *   - If we're on Windows, we want to use PHP >= 5.3.7 or else
     *     we get insufficient entropy errors.
     */
    if (
        !is_callable('random_bytes')
        &&
        // Windows on PHP < 5.3.7 is broken, but non-Windows is not known to be.
        (DIRECTORY_SEPARATOR === '/' || PHP_VERSION_ID >= 50307)
        &&
        // Prevent this code from hanging indefinitely on non-Windows;
        // see https://bugs.php.net/bug.php?id=69833
        (
            DIRECTORY_SEPARATOR !== '/' ||
            (PHP_VERSION_ID <= 50609 || PHP_VERSION_ID >= 50613)
        )
        &&
        extension_loaded('mcrypt')
    ) {
        // See random_bytes_mcrypt.php
        require_once $RandomCompatDIR . '/random_bytes_mcrypt.php';
    }
    $RandomCompatUrandom = null;
    /**
     * This is a Windows-specific fallback, for when the mcrypt extension
     * isn't loaded.
     */
    if (
        !is_callable('random_bytes')
        &&
        extension_loaded('com_dotnet')
        &&
        class_exists('COM')
    ) {
        $RandomCompat_disabled_classes = preg_split(
            '#\s*,\s*#',
            strtolower(ini_get('disable_classes'))
        );
        if (!in_array('com', $RandomCompat_disabled_classes)) {
            try {
                $RandomCompatCOMtest = new COM('CAPICOM.Utilities.1');
                if (method_exists($RandomCompatCOMtest, 'GetRandom')) {
                    // See random_bytes_com_dotnet.php
                    require_once $RandomCompatDIR . '/random_bytes_com_dotnet.php';
                }
            } catch (com_exception $e) {
                // Don't try to use it.
            }
        }
        $RandomCompat_disabled_classes = null;
        $RandomCompatCOMtest = null;
    }
    /**
     * throw new Exception
     */
    if (!is_callable('random_bytes')) {
        /**
         * We don't have any more options, so let's throw an exception right now
         * and hope the developer won't let it fail silently.
         *
         * @param mixed $length
         * @return void
         * @throws Exception
         */
        function random_bytes($length)
        {
            unset($length); // Suppress "variable not used" warnings.
            throw new Exception(
                'There is no suitable CSPRNG installed on your system'
            );
        }
    }
}
if (!is_callable('random_int')) {
    require_once $RandomCompatDIR . '/random_int.php';
}
$RandomCompatDIR = null;



//--------------------------------------------------------------------------------
//random_int
    defined('BASEPATH') OR exit('No direct script access allowed');//?

if (!is_callable('random_int')) {
    /**
     * Random_* Compatibility Library
     * for using the new PHP 7 random_* API in PHP 5 projects
     *
     * The MIT License (MIT)
     *
     * Copyright (c) 2015 - 2017 Paragon Initiative Enterprises
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
     * SOFTWARE.
     */
    /**
     * Fetch a random integer between $min and $max inclusive
     *
     * @param int $min
     * @param int $max
     *
     * @throws Exception
     *
     * @return int
     */
    function random_int($min, $max)
    {
        /**
         * Type and input logic checks
         *
         * If you pass it a float in the range (~PHP_INT_MAX, PHP_INT_MAX)
         * (non-inclusive), it will sanely cast it to an int. If you it's equal to
         * ~PHP_INT_MAX or PHP_INT_MAX, we let it fail as not an integer. Floats
         * lose precision, so the <= and => operators might accidentally let a float
         * through.
         */
        try {
            $min = RandomCompat_intval($min);
        } catch (TypeError $ex) {
            throw new TypeError(
                'random_int(): $min must be an integer'
            );
        }
        try {
            $max = RandomCompat_intval($max);
        } catch (TypeError $ex) {
            throw new TypeError(
                'random_int(): $max must be an integer'
            );
        }
        /**
         * Now that we've verified our weak typing system has given us an integer,
         * let's validate the logic then we can move forward with generating random
         * integers along a given range.
         */
        if ($min > $max) {
            throw new Error(
                'Minimum value must be less than or equal to the maximum value'
            );
        }
        if ($max === $min) {
            return $min;
        }
        /**
         * Initialize variables to 0
         *
         * We want to store:
         * $bytes => the number of random bytes we need
         * $mask => an integer bitmask (for use with the &) operator
         *          so we can minimize the number of discards
         */
        $attempts = $bits = $bytes = $mask = $valueShift = 0;
        /**
         * At this point, $range is a positive number greater than 0. It might
         * overflow, however, if $max - $min > PHP_INT_MAX. PHP will cast it to
         * a float and we will lose some precision.
         */
        $range = $max - $min;
        /**
         * Test for integer overflow:
         */
        if (!is_int($range)) {
            /**
             * Still safely calculate wider ranges.
             * Provided by @CodesInChaos, @oittaa
             *
             * @ref https://gist.github.com/CodesInChaos/03f9ea0b58e8b2b8d435
             *
             * We use ~0 as a mask in this case because it generates all 1s
             *
             * @ref https://eval.in/400356 (32-bit)
             * @ref http://3v4l.org/XX9r5  (64-bit)
             */
            $bytes = PHP_INT_SIZE;
            $mask = ~0;
        } else {
            /**
             * $bits is effectively ceil(log($range, 2)) without dealing with
             * type juggling
             */
            while ($range > 0) {
                if ($bits % 8 === 0) {
                    ++$bytes;
                }
                ++$bits;
                $range >>= 1;
                $mask = $mask << 1 | 1;
            }
            $valueShift = $min;
        }
        $val = 0;
        /**
         * Now that we have our parameters set up, let's begin generating
         * random integers until one falls between $min and $max
         */
        do {
            /**
             * The rejection probability is at most 0.5, so this corresponds
             * to a failure probability of 2^-128 for a working RNG
             */
            if ($attempts > 128) {
                throw new Exception(
                    'random_int: RNG is broken - too many rejections'
                );
            }
            /**
             * Let's grab the necessary number of random bytes
             */
            $randomByteString = random_bytes($bytes);
            /**
             * Let's turn $randomByteString into an integer
             *
             * This uses bitwise operators (<< and |) to build an integer
             * out of the values extracted from ord()
             *
             * Example: [9F] | [6D] | [32] | [0C] =>
             *   159 + 27904 + 3276800 + 201326592 =>
             *   204631455
             */
            $val &= 0;
            for ($i = 0; $i < $bytes; ++$i) {
                $val |= ord($randomByteString[$i]) << ($i * 8);
            }
            /**
             * Apply mask
             */
            $val &= $mask;
            $val += $valueShift;
            ++$attempts;
            /**
             * If $val overflows to a floating point number,
             * ... or is larger than $max,
             * ... or smaller than $min,
             * then try again.
             */
        } while (!is_int($val) || $val > $max || $val < $min);
        return (int)$val;
    }
}