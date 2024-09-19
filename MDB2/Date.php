<?php

/**
 * +----------------------------------------------------------------------+
 * | PHP version 5                                                        |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1998-2006 Manuel Lemos, Tomas V.V.Cox,                 |
 * | Stig. S. Bakken, Lukas Smith                                         |
 * | All rights reserved.                                                 |
 * +----------------------------------------------------------------------+
 * | MDB2 is a merge of PEAR DB and Metabases that provides a unified DB  |
 * | API as well as database abstraction for PHP applications.            |
 * | This LICENSE is in the BSD license style.                            |
 * |                                                                      |
 * | Redistribution and use in source and binary forms, with or without   |
 * | modification, are permitted provided that the following conditions   |
 * | are met:                                                             |
 * |                                                                      |
 * | Redistributions of source code must retain the above copyright       |
 * | notice, this list of conditions and the following disclaimer.        |
 * |                                                                      |
 * | Redistributions in binary form must reproduce the above copyright    |
 * | notice, this list of conditions and the following disclaimer in the  |
 * | documentation and/or other materials provided with the distribution. |
 * |                                                                      |
 * | Neither the name of Manuel Lemos, Tomas V.V.Cox, Stig. S. Bakken,    |
 * | Lukas Smith nor the names of his contributors may be used to endorse |
 * | or promote products derived from this software without specific prior|
 * | written permission.                                                  |
 * |                                                                      |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
 * | FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE      |
 * | REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,          |
 * | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
 * | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS|
 * |  OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED  |
 * | AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT          |
 * | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY|
 * | WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE          |
 * | POSSIBILITY OF SUCH DAMAGE.                                          |
 * +----------------------------------------------------------------------+
 * | Author: Lukas Smith <smith@pooteeweet.org>                           |
 * +----------------------------------------------------------------------+.
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */

/**
 * Several methods to convert the MDB2 native timestamp format (ISO based)
 * to and from data structures that are convenient to worth with in side of php.
 * For more complex date arithmetic please take a look at the Date package in PEAR.
 *
 * @category Database
 *
 * @author  Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2_Date
{
    /**
     * return the current datetime.
     *
     * @return string current datetime in the MDB2 format
     */
    public static function mdbNow()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * return the current date.
     *
     * @return string current date in the MDB2 format
     */
    public static function mdbToday()
    {
        return date('Y-m-d');
    }

    /**
     * return the current time.
     *
     * @return string current time in the MDB2 format
     */
    public static function mdbTime()
    {
        return date('H:i:s');
    }

    /**
     * convert a date into a MDB2 timestamp.
     *
     * @param int hour of the date
     * @param int minute of the date
     * @param int second of the date
     * @param int month of the date
     * @param int day of the date
     * @param int year of the date
     * @param mixed|null $hour
     * @param mixed|null $minute
     * @param mixed|null $second
     * @param mixed|null $month
     * @param mixed|null $day
     * @param mixed|null $year
     *
     * @return string a valid MDB2 timestamp
     */
    public static function date2Mdbstamp(
        $hour = null,
        $minute = null,
        $second = null,
        $month = null,
        $day = null,
        $year = null
    ) {
        return MDB2_Date::unix2Mdbstamp(mktime($hour, $minute, $second, $month, $day, $year));
    }

    /**
     * convert a unix timestamp into a MDB2 timestamp.
     *
     * @param int a valid unix timestamp
     * @param mixed $unix_timestamp
     *
     * @return string a valid MDB2 timestamp
     */
    public static function unix2Mdbstamp($unix_timestamp)
    {
        return date('Y-m-d H:i:s', $unix_timestamp);
    }

    /**
     * convert a MDB2 timestamp into a unix timestamp.
     *
     * @param int a valid MDB2 timestamp
     * @param mixed $mdb_timestamp
     *
     * @return string unix timestamp with the time stored in the MDB2 format
     */
    public static function mdbstamp2Unix($mdb_timestamp)
    {
        $arr = MDB2_Date::mdbstamp2Date($mdb_timestamp);

        return mktime($arr['hour'], $arr['minute'], $arr['second'], $arr['month'], $arr['day'], $arr['year']);
    }

    /**
     * convert a MDB2 timestamp into an array containing all
     * values necessary to pass to php's date() function.
     *
     * @param int a valid MDB2 timestamp
     * @param mixed $mdb_timestamp
     *
     * @return array with the time split
     */
    public static function mdbstamp2Date($mdb_timestamp)
    {
        [$arr['year'], $arr['month'], $arr['day'], $arr['hour'], $arr['minute'], $arr['second']]
            = sscanf($mdb_timestamp, '%04u-%02u-%02u %02u:%02u:%02u');

        return $arr;
    }
}
