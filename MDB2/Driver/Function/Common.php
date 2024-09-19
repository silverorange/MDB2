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
 * Base class for the function modules that is extended by each MDB2 driver.
 *
 * To load this module in the MDB2 object:
 * $mdb->loadModule('Function');
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2_Driver_Function_Common extends MDB2_Module_Common
{
    // {{{ executeStoredProc()

    /**
     * Execute a stored procedure and return any results.
     *
     * @param string $name              string that identifies the function to execute
     * @param mixed  $params            array that contains the paramaters to pass the stored proc
     * @param mixed  $types             array that contains the types of the columns in
     *                                  the result set
     * @param mixed  $result_class      string which specifies which result class to use
     * @param mixed  $result_wrap_class string which specifies which class to wrap results in
     *
     * @return mixed a result handle or MDB2_OK on success, a MDB2 error on failure
     */
    public function executeStoredProc($name, $params = null, $types = null, $result_class = true, $result_wrap_class = false)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        return $db->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
    // {{{ functionTable()

    /**
     * return string for internal table used when calling only a function.
     *
     * @return string for internal table used when calling only a function
     */
    public function functionTable()
    {
        return '';
    }

    // }}}
    // {{{ now()

    /**
     * Return string to call a variable with the current timestamp inside an SQL statement
     * There are three special variables for current date and time:
     * - CURRENT_TIMESTAMP (date and time, TIMESTAMP type)
     * - CURRENT_DATE (date, DATE type)
     * - CURRENT_TIME (time, TIME type).
     *
     * @param string $type 'timestamp' | 'time' | 'date'
     *
     * @return string to call a variable with the current timestamp
     */
    public function now($type = 'timestamp')
    {
        switch ($type) {
            case 'time':
                return 'CURRENT_TIME';

            case 'date':
                return 'CURRENT_DATE';

            case 'timestamp':
            default:
                return 'CURRENT_TIMESTAMP';
        }
    }

    // }}}
    // {{{ unixtimestamp()

    /**
     * return string to call a function to get the unix timestamp from a iso timestamp.
     *
     * @param string $expression
     *
     * @return string to call a variable with the timestamp
     */
    public function unixtimestamp($expression)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        return $db->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
    // {{{ substring()

    /**
     * return string to call a function to get a substring inside an SQL statement.
     *
     * @param mixed      $value
     * @param mixed      $position
     * @param mixed|null $length
     *
     * @return string to call a function to get a substring
     */
    public function substring($value, $position = 1, $length = null)
    {
        if (null !== $length) {
            return "SUBSTRING({$value} FROM {$position} FOR {$length})";
        }

        return "SUBSTRING({$value} FROM {$position})";
    }

    // }}}
    // {{{ replace()

    /**
     * return string to call a function to get replace inside an SQL statement.
     *
     * @param mixed $str
     * @param mixed $from_str
     * @param mixed $to_str
     *
     * @return string to call a function to get a replace
     */
    public function replace($str, $from_str, $to_str)
    {
        return "REPLACE({$str}, {$from_str} , {$to_str})";
    }

    // }}}
    // {{{ concat()

    /**
     * Returns string to concatenate two or more string parameters.
     *
     * @param string $value1
     * @param string $value2
     *
     * @return string to concatenate two strings
     */
    public function concat($value1, $value2)
    {
        $args = func_get_args();

        return '(' . implode(' || ', $args) . ')';
    }

    // }}}
    // {{{ random()

    /**
     * return string to call a function to get random value inside an SQL statement.
     *
     * @return return string to generate float between 0 and 1
     */
    public function random()
    {
        return 'RAND()';
    }

    // }}}
    // {{{ lower()

    /**
     * return string to call a function to lower the case of an expression.
     *
     * @param string $expression
     *
     * @return return string to lower case of an expression
     */
    public function lower($expression)
    {
        return "LOWER({$expression})";
    }

    // }}}
    // {{{ upper()

    /**
     * return string to call a function to upper the case of an expression.
     *
     * @param string $expression
     *
     * @return return string to upper case of an expression
     */
    public function upper($expression)
    {
        return "UPPER({$expression})";
    }

    // }}}
    // {{{ length()

    /**
     * return string to call a function to get the length of a string expression.
     *
     * @param string $expression
     *
     * @return return string to get the string expression length
     */
    public function length($expression)
    {
        return "LENGTH({$expression})";
    }

    // }}}
    // {{{ guid()

    /**
     * Returns global unique identifier.
     *
     * @return string to get global unique identifier
     */
    public function guid()
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        return $db->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
}
