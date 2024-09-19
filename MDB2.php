<?php

/**
 * +----------------------------------------------------------------------+
 * | PHP version 5                                                        |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1998-2007 Manuel Lemos, Tomas V.V.Cox,                 |
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
 * The method mapErrorCode in each MDB2_dbtype implementation maps
 * native error codes to one of these.
 *
 * If you add an error code here, make sure you also add a textual
 * version of it in MDB2::errorMessage().
 */
const MDB2_OK = true;
const MDB2_ERROR = -1;
const MDB2_ERROR_SYNTAX = -2;
const MDB2_ERROR_CONSTRAINT = -3;
const MDB2_ERROR_NOT_FOUND = -4;
const MDB2_ERROR_ALREADY_EXISTS = -5;
const MDB2_ERROR_UNSUPPORTED = -6;
const MDB2_ERROR_MISMATCH = -7;
const MDB2_ERROR_INVALID = -8;
const MDB2_ERROR_NOT_CAPABLE = -9;
const MDB2_ERROR_TRUNCATED = -10;
const MDB2_ERROR_INVALID_NUMBER = -11;
const MDB2_ERROR_INVALID_DATE = -12;
const MDB2_ERROR_DIVZERO = -13;
const MDB2_ERROR_NODBSELECTED = -14;
const MDB2_ERROR_CANNOT_CREATE = -15;
const MDB2_ERROR_CANNOT_DELETE = -16;
const MDB2_ERROR_CANNOT_DROP = -17;
const MDB2_ERROR_NOSUCHTABLE = -18;
const MDB2_ERROR_NOSUCHFIELD = -19;
const MDB2_ERROR_NEED_MORE_DATA = -20;
const MDB2_ERROR_NOT_LOCKED = -21;
const MDB2_ERROR_VALUE_COUNT_ON_ROW = -22;
const MDB2_ERROR_INVALID_DSN = -23;
const MDB2_ERROR_CONNECT_FAILED = -24;
const MDB2_ERROR_EXTENSION_NOT_FOUND = -25;
const MDB2_ERROR_NOSUCHDB = -26;
const MDB2_ERROR_ACCESS_VIOLATION = -27;
const MDB2_ERROR_CANNOT_REPLACE = -28;
const MDB2_ERROR_CONSTRAINT_NOT_NULL = -29;
const MDB2_ERROR_DEADLOCK = -30;
const MDB2_ERROR_CANNOT_ALTER = -31;
const MDB2_ERROR_MANAGER = -32;
const MDB2_ERROR_MANAGER_PARSE = -33;
const MDB2_ERROR_LOADMODULE = -34;
const MDB2_ERROR_INSUFFICIENT_DATA = -35;
const MDB2_ERROR_NO_PERMISSION = -36;
const MDB2_ERROR_DISCONNECT_FAILED = -37;

/**
 * These are just helper constants to more verbosely express parameters to prepare().
 */
const MDB2_PREPARE_MANIP = false;
const MDB2_PREPARE_RESULT = null;

/**
 * This is a special constant that tells MDB2 the user hasn't specified
 * any particular get mode, so the default should be used.
 */
const MDB2_FETCHMODE_DEFAULT = 0;

/**
 * Column data indexed by numbers, ordered from 0 and up.
 */
const MDB2_FETCHMODE_ORDERED = 1;

/**
 * Column data indexed by column names.
 */
const MDB2_FETCHMODE_ASSOC = 2;

/**
 * Column data as object properties.
 */
const MDB2_FETCHMODE_OBJECT = 3;

/**
 * For multi-dimensional results: normally the first level of arrays
 * is the row number, and the second level indexed by column number or name.
 * MDB2_FETCHMODE_FLIPPED switches this order, so the first level of arrays
 * is the column name, and the second level the row number.
 */
const MDB2_FETCHMODE_FLIPPED = 4;

/**
 * Portability: turn off all portability features.
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_NONE = 0;

/**
 * Portability: convert names of tables and fields to case defined in the
 * "field_case" option when using the query*(), fetch*() and tableInfo() methods.
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_FIX_CASE = 1;

/**
 * Portability: right trim the data output by query*() and fetch*().
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_RTRIM = 2;

/**
 * Portability: force reporting the number of rows deleted.
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_DELETE_COUNT = 4;

/**
 * Portability: not needed in MDB2 (just left here for compatibility to DB).
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_NUMROWS = 8;

/**
 * Portability: makes certain error messages in certain drivers compatible
 * with those from other DBMS's.
 *
 * + mysql, mysqli:  change unique/primary key constraints
 *   MDB2_ERROR_ALREADY_EXISTS -> MDB2_ERROR_CONSTRAINT
 *
 * + odbc(access):  MS's ODBC driver reports 'no such field' as code
 *   07001, which means 'too few parameters.'  When this option is on
 *   that code gets mapped to MDB2_ERROR_NOSUCHFIELD.
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_ERRORS = 16;

/**
 * Portability: convert empty values to null strings in data output by
 * query*() and fetch*().
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_EMPTY_TO_NULL = 32;

/**
 * Portability: removes database/table qualifiers from associative indexes.
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_FIX_ASSOC_FIELD_NAMES = 64;

/**
 * Portability: turn on all portability features.
 *
 * @see MDB2_Driver_Common::setOption()
 */
const MDB2_PORTABILITY_ALL = 127;

// These are global variables that are used to track the various class instances

$GLOBALS['_MDB2_databases'] = [];
$GLOBALS['_MDB2_dsninfo_default'] = [
    'phptype'  => false,
    'dbsyntax' => false,
    'username' => false,
    'password' => false,
    'protocol' => false,
    'hostspec' => false,
    'port'     => false,
    'socket'   => false,
    'database' => false,
    'mode'     => false,
];

/**
 * The main 'MDB2' class is simply a container class with some static
 * methods for creating DB objects as well as some utility functions
 * common to all parts of DB.
 *
 * The object model of MDB2 is as follows (indentation means inheritance):
 *
 * MDB2          The main MDB2 class.  This is simply a utility class
 *              with some 'static' methods for creating MDB2 objects as
 *              well as common utility functions for other MDB2 classes.
 *
 * MDB2_Driver_Common   The base for each MDB2 implementation.  Provides default
 * |            implementations (in OO lingo virtual methods) for
 * |            the actual DB implementations as well as a bunch of
 * |            query utility functions.
 * |
 * +-MDB2_Driver_mysql  The MDB2 implementation for MySQL. Inherits MDB2_Driver_Common.
 *              When calling MDB2::factory or MDB2::connect for MySQL
 *              connections, the object returned is an instance of this
 *              class.
 * +-MDB2_Driver_pgsql  The MDB2 implementation for PostGreSQL. Inherits MDB2_Driver_Common.
 *              When calling MDB2::factory or MDB2::connect for PostGreSQL
 *              connections, the object returned is an instance of this
 *              class.
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2
{
    /**
     * set option array   in an exiting database object.
     *
     * @param   MDB2_Driver_Common  MDB2 object
     * @param   array   an associative array of option names and their values
     * @param mixed $db
     * @param mixed $options
     *
     * @return mixed MDB2_OK or a PEAR Error object
     */
    public static function setOptions($db, $options)
    {
        if (is_array($options)) {
            foreach ($options as $option => $value) {
                $test = $db->setOption($option, $value);
                if (self::isError($test)) {
                    return $test;
                }
            }
        }

        return MDB2_OK;
    }

    /**
     * Checks if a class exists without triggering __autoload.
     *
     * @param   string  classname
     * @param mixed $classname
     *
     * @return bool true success and false on error
     */
    public static function classExists($classname)
    {
        return class_exists($classname, false);
    }

    /**
     * Loads a PEAR class.
     *
     * @param   string  classname to load
     * @param   bool    if errors should be suppressed
     * @param mixed $class_name
     * @param mixed $debug
     *
     * @return mixed true success or PEAR_Error on failure
     */
    public static function loadClass($class_name, $debug)
    {
        if (!class_exists($class_name)) {
            $msg = "unable to load class '{$class_name}'";

            return self::raiseError(MDB2_ERROR_NOT_FOUND, null, null, $msg);
        }

        return MDB2_OK;
    }

    /**
     * Create a new MDB2 object for the specified database type.
     *
     * @param   mixed   'data source name', see the MDB2::parseDSN
     *                      method for a description of the dsn format.
     *                      Can also be specified as an array of the
     *                      format returned by MDB2::parseDSN.
     * @param   array   an associative array of option names and
     *                            their values
     * @param mixed $dsn
     * @param mixed $options
     *
     * @return mixed a newly created MDB2 object, or false on error
     */
    public static function factory($dsn, $options = false)
    {
        $dsninfo = self::parseDSN($dsn);
        if (empty($dsninfo['phptype'])) {
            return self::raiseError(
                MDB2_ERROR_NOT_FOUND,
                null,
                null,
                'no RDBMS driver specified'
            );
        }
        $class_name = 'MDB2_Driver_' . $dsninfo['phptype'];

        $debug = (!empty($options['debug']));
        $err = self::loadClass($class_name, $debug);
        if (self::isError($err)) {
            return $err;
        }

        $db = new $class_name();
        $db->setDSN($dsninfo);
        $err = self::setOptions($db, $options);
        if (self::isError($err)) {
            return $err;
        }

        return $db;
    }

    /**
     * Create a new MDB2_Driver_* connection object and connect to the specified
     * database.
     *
     * @param mixed $dsn     'data source name', see the MDB2::parseDSN
     *                       method for a description of the dsn format.
     *                       Can also be specified as an array of the
     *                       format returned by MDB2::parseDSN.
     * @param array $options an associative array of option names and
     *                       their values
     *
     * @return mixed a newly created MDB2 connection object, or a MDB2
     *               error object on error
     *
     * @see     MDB2::parseDSN
     */
    public static function connect($dsn, $options = false)
    {
        $db = self::factory($dsn, $options);
        if (self::isError($db)) {
            return $db;
        }

        $err = $db->connect();
        if (self::isError($err)) {
            $dsn = $db->getDSN('string', 'xxx');
            $db->disconnect();
            $err->addUserInfo($dsn);

            return $err;
        }

        return $db;
    }

    /**
     * Returns a MDB2 connection with the requested DSN.
     * A new MDB2 connection object is only created if no object with the
     * requested DSN exists yet.
     *
     * @param   mixed   'data source name', see the MDB2::parseDSN
     *                            method for a description of the dsn format.
     *                            Can also be specified as an array of the
     *                            format returned by MDB2::parseDSN.
     * @param   array   an associative array of option names and
     *                            their values
     * @param mixed|null $dsn
     * @param mixed      $options
     *
     * @return mixed a newly created MDB2 connection object, or a MDB2
     *               error object on error
     *
     * @see     MDB2::parseDSN
     */
    public static function singleton($dsn = null, $options = false)
    {
        if ($dsn) {
            $dsninfo = self::parseDSN($dsn);
            $dsninfo = array_merge($GLOBALS['_MDB2_dsninfo_default'], $dsninfo);
            $keys = array_keys($GLOBALS['_MDB2_databases']);
            for ($i = 0, $j = count($keys); $i < $j; ++$i) {
                if (isset($GLOBALS['_MDB2_databases'][$keys[$i]])) {
                    $tmp_dsn = $GLOBALS['_MDB2_databases'][$keys[$i]]->getDSN('array');
                    if (count(array_diff_assoc($tmp_dsn, $dsninfo)) == 0) {
                        self::setOptions($GLOBALS['_MDB2_databases'][$keys[$i]], $options);

                        return $GLOBALS['_MDB2_databases'][$keys[$i]];
                    }
                }
            }
        } elseif (is_array($GLOBALS['_MDB2_databases']) && reset($GLOBALS['_MDB2_databases'])) {
            return $GLOBALS['_MDB2_databases'][key($GLOBALS['_MDB2_databases'])];
        }
        $db = self::factory($dsn, $options);

        return $db;
    }

    /**
     * It looks like there's a memory leak in array_diff() in PHP 5.1.x,
     * so use this method instead.
     *
     * @see http://pear.php.net/bugs/bug.php?id=11790
     *
     * @param array $arr1
     * @param array $arr2
     *
     * @return bool
     */
    public static function areEquals($arr1, $arr2)
    {
        if (count($arr1) != count($arr2)) {
            return false;
        }
        foreach (array_keys($arr1) as $k) {
            if (!array_key_exists($k, $arr2) || $arr1[$k] != $arr2[$k]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the MDB2 API version.
     *
     * @return string the MDB2 API version number
     */
    public static function apiVersion()
    {
        return '@package_version@';
    }

    /**
     * This method is used to communicate an error and invoke error
     * callbacks etc.  Basically a wrapper for PEAR::raiseError
     * without the message string.
     *
     * @param   mixed  int error code
     * @param   int    error mode, see PEAR_Error docs
     * @param   mixed  If error mode is PEAR_ERROR_TRIGGER, this is the
     *                 error level (E_USER_NOTICE etc).  If error mode is
     *                 PEAR_ERROR_CALLBACK, this is the callback function,
     *                 either as a function name, or as an array of an
     *                 object and method name.  For other error modes this
     *                 parameter is ignored.
     * @param   string Extra debug information.  Defaults to the last
     *                 query and native error code.
     * @param mixed|null $code
     * @param mixed|null $mode
     * @param mixed|null $options
     * @param mixed|null $userinfo
     * @param mixed|null $dummy1
     * @param mixed|null $dummy2
     * @param mixed      $dummy3
     *
     * @return PEAR_Error instance of a PEAR Error object
     *
     * @see     PEAR_Error
     */
    public static function raiseError(
        $code = null,
        $mode = null,
        $options = null,
        $userinfo = null,
        $dummy1 = null,
        $dummy2 = null,
        $dummy3 = false
    ) {
        $pear = new PEAR();

        return $pear->raiseError(null, $code, $mode, $options, $userinfo, 'MDB2_Error', true);
    }

    /**
     * Tell whether a value is a MDB2 error.
     *
     * @param   mixed   the value to test
     * @param   int     if is an error object, return true
     *                        only if $code is a string and
     *                        $db->getMessage() == $code or
     *                        $code is an integer and $db->getCode() == $code
     * @param mixed $data
     *
     * @return bool true if parameter is an error
     */
    public static function isError($data, $code = null)
    {
        if ($data instanceof MDB2_Error) {
            if (null === $code) {
                return true;
            }
            if (is_string($code)) {
                return $data->getMessage() === $code;
            }

            return in_array($data->getCode(), (array) $code);
        }

        return false;
    }

    /**
     * Tell whether a value is a MDB2 connection.
     *
     * @param   mixed   value to test
     * @param mixed $value
     *
     * @return bool whether $value is a MDB2 connection
     */
    public static function isConnection($value)
    {
        return $value instanceof MDB2_Driver_Common;
    }

    /**
     * Tell whether a value is a MDB2 result.
     *
     * @param mixed $value value to test
     *
     * @return bool whether $value is a MDB2 result
     */
    public static function isResult($value)
    {
        return $value instanceof MDB2_Result;
    }

    /**
     * Tell whether a value is a MDB2 result implementing the common interface.
     *
     * @param mixed $value value to test
     *
     * @return bool whether $value is a MDB2 result implementing the common interface
     */
    public static function isResultCommon($value)
    {
        return $value instanceof MDB2_Result_Common;
    }

    /**
     * Tell whether a value is a MDB2 statement interface.
     *
     * @param   mixed   value to test
     * @param mixed $value
     *
     * @return bool whether $value is a MDB2 statement interface
     */
    public static function isStatement($value)
    {
        return $value instanceof MDB2_Statement_Common;
    }

    /**
     * Return a textual error message for a MDB2 error code.
     *
     * @param   array|int   integer error code,
     *                          null to get the current error code-message map,
     *                          or an array with a new error code-message map
     * @param mixed|null $value
     *
     * @return string error message, or false if the error code was
     *                not recognized
     */
    public static function errorMessage($value = null)
    {
        static $errorMessages;

        if (is_array($value)) {
            $errorMessages = $value;

            return MDB2_OK;
        }

        if (!isset($errorMessages)) {
            $errorMessages = [
                MDB2_OK                        => 'no error',
                MDB2_ERROR                     => 'unknown error',
                MDB2_ERROR_ALREADY_EXISTS      => 'already exists',
                MDB2_ERROR_CANNOT_CREATE       => 'can not create',
                MDB2_ERROR_CANNOT_ALTER        => 'can not alter',
                MDB2_ERROR_CANNOT_REPLACE      => 'can not replace',
                MDB2_ERROR_CANNOT_DELETE       => 'can not delete',
                MDB2_ERROR_CANNOT_DROP         => 'can not drop',
                MDB2_ERROR_CONSTRAINT          => 'constraint violation',
                MDB2_ERROR_CONSTRAINT_NOT_NULL => 'null value violates not-null constraint',
                MDB2_ERROR_DIVZERO             => 'division by zero',
                MDB2_ERROR_INVALID             => 'invalid',
                MDB2_ERROR_INVALID_DATE        => 'invalid date or time',
                MDB2_ERROR_INVALID_NUMBER      => 'invalid number',
                MDB2_ERROR_MISMATCH            => 'mismatch',
                MDB2_ERROR_NODBSELECTED        => 'no database selected',
                MDB2_ERROR_NOSUCHFIELD         => 'no such field',
                MDB2_ERROR_NOSUCHTABLE         => 'no such table',
                MDB2_ERROR_NOT_CAPABLE         => 'MDB2 backend not capable',
                MDB2_ERROR_NOT_FOUND           => 'not found',
                MDB2_ERROR_NOT_LOCKED          => 'not locked',
                MDB2_ERROR_SYNTAX              => 'syntax error',
                MDB2_ERROR_UNSUPPORTED         => 'not supported',
                MDB2_ERROR_VALUE_COUNT_ON_ROW  => 'value count on row',
                MDB2_ERROR_INVALID_DSN         => 'invalid DSN',
                MDB2_ERROR_CONNECT_FAILED      => 'connect failed',
                MDB2_ERROR_NEED_MORE_DATA      => 'insufficient data supplied',
                MDB2_ERROR_EXTENSION_NOT_FOUND => 'extension not found',
                MDB2_ERROR_NOSUCHDB            => 'no such database',
                MDB2_ERROR_ACCESS_VIOLATION    => 'insufficient permissions',
                MDB2_ERROR_LOADMODULE          => 'error while including on demand module',
                MDB2_ERROR_TRUNCATED           => 'truncated',
                MDB2_ERROR_DEADLOCK            => 'deadlock detected',
                MDB2_ERROR_NO_PERMISSION       => 'no permission',
                MDB2_ERROR_DISCONNECT_FAILED   => 'disconnect failed',
            ];
        }

        if (null === $value) {
            return $errorMessages;
        }

        if (self::isError($value)) {
            $value = $value->getCode();
        }

        return $errorMessages[$value] ?? $errorMessages[MDB2_ERROR];
    }

    /**
     * Parse a data source name.
     *
     * Additional keys can be added by appending a URI query string to the
     * end of the DSN.
     *
     * The format of the supplied DSN is in its fullest form:
     * <code>
     *  phptype(dbsyntax)://username:password@protocol+hostspec/database?option=8&another=true
     * </code>
     *
     * Most variations are allowed:
     * <code>
     *  phptype://username:password@protocol+hostspec:110//usr/db_file.db?mode=0644
     *  phptype://username:password@hostspec/database_name
     *  phptype://username:password@hostspec
     *  phptype://username@hostspec
     *  phptype://hostspec/database
     *  phptype://hostspec
     *  phptype(dbsyntax)
     *  phptype
     * </code>
     *
     * @param   string  Data Source Name to be parsed
     * @param mixed $dsn
     *
     * @return array an associative array with the following keys:
     *               + phptype:  Database backend used in PHP (mysql, odbc etc.)
     *               + dbsyntax: Database used with regards to SQL syntax etc.
     *               + protocol: Communication protocol to use (tcp, unix etc.)
     *               + hostspec: Host specification (hostname[:port])
     *               + database: Database to use on the DBMS server
     *               + username: User name for login
     *               + password: Password for login
     *
     * @author  Tomas V.V.Cox <cox@idecnet.com>
     */
    public static function parseDSN($dsn)
    {
        $parsed = $GLOBALS['_MDB2_dsninfo_default'];

        if (is_array($dsn)) {
            $dsn = array_merge($parsed, $dsn);
            if (!$dsn['dbsyntax']) {
                $dsn['dbsyntax'] = $dsn['phptype'];
            }

            return $dsn;
        }

        // Find phptype and dbsyntax
        if (($pos = strpos($dsn, '://')) !== false) {
            $str = substr($dsn, 0, $pos);
            $dsn = substr($dsn, $pos + 3);
        } else {
            $str = $dsn;
            $dsn = null;
        }

        // Get phptype and dbsyntax
        // $str => phptype(dbsyntax)
        if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
            $parsed['phptype'] = $arr[1];
            $parsed['dbsyntax'] = !$arr[2] ? $arr[1] : $arr[2];
        } else {
            $parsed['phptype'] = $str;
            $parsed['dbsyntax'] = $str;
        }

        if ($dsn == '') {
            return $parsed;
        }

        // Get (if found): username and password
        // $dsn => username:password@protocol+hostspec/database
        if (($at = strrpos($dsn, '@')) !== false) {
            $str = substr($dsn, 0, $at);
            $dsn = substr($dsn, $at + 1);
            if (($pos = strpos($str, ':')) !== false) {
                $parsed['username'] = rawurldecode(substr($str, 0, $pos));
                $parsed['password'] = rawurldecode(substr($str, $pos + 1));
            } else {
                $parsed['username'] = rawurldecode($str);
            }
        }

        // Find protocol and hostspec

        if (preg_match('|^([^(]+)\((.*?)\)/?(.*?)$|', $dsn, $match)) {
            // $dsn => proto(proto_opts)/database
            $proto = $match[1];
            $proto_opts = $match[2] ?: false;
            $dsn = $match[3];
        } else {
            // $dsn => protocol+hostspec/database (old format)
            if (str_contains($dsn, '+')) {
                [$proto, $dsn] = explode('+', $dsn, 2);
            }
            if (str_starts_with($dsn, '//')
                && str_contains(substr($dsn, 2), '/')
                && $parsed['phptype'] == 'oci8'
            ) {
                // oracle's "Easy Connect" syntax:
                // "username/password@[//]host[:port][/service_name]"
                // e.g. "scott/tiger@//mymachine:1521/oracle"
                $proto_opts = $dsn;
                $pos = strrpos($proto_opts, '/');
                $dsn = substr($proto_opts, $pos + 1);
                $proto_opts = substr($proto_opts, 0, $pos);
            } elseif (str_contains($dsn, '/')) {
                [$proto_opts, $dsn] = explode('/', $dsn, 2);
            } else {
                $proto_opts = $dsn;
                $dsn = null;
            }
        }

        // process the different protocol options
        $parsed['protocol'] = (!empty($proto)) ? $proto : 'tcp';
        $proto_opts = rawurldecode($proto_opts);
        if (str_contains($proto_opts, ':')) {
            [$proto_opts, $parsed['port']] = explode(':', $proto_opts);
        }
        if ($parsed['protocol'] == 'tcp') {
            $parsed['hostspec'] = $proto_opts;
        } elseif ($parsed['protocol'] == 'unix') {
            $parsed['socket'] = $proto_opts;
        }

        // Get dabase if any
        // $dsn => database
        if ($dsn) {
            if (($pos = strpos($dsn, '?')) === false) {
                // /database
                $parsed['database'] = rawurldecode($dsn);
            } else {
                // /database?param1=value1&param2=value2
                $parsed['database'] = rawurldecode(substr($dsn, 0, $pos));
                $dsn = substr($dsn, $pos + 1);
                if (str_contains($dsn, '&')) {
                    $opts = explode('&', $dsn);
                } else {
                    // database?param1=value1
                    $opts = [$dsn];
                }
                foreach ($opts as $opt) {
                    [$key, $value] = explode('=', $opt);
                    if (!array_key_exists($key, $parsed) || false === $parsed[$key]) {
                        // don't allow params overwrite
                        $parsed[$key] = rawurldecode($value);
                    }
                }
            }
        }

        return $parsed;
    }
}

/**
 * Close any open transactions form persistent connections.
 */
function MDB2_closeOpenTransactions(): void
{
    reset($GLOBALS['_MDB2_databases']);
    while (next($GLOBALS['_MDB2_databases'])) {
        $key = key($GLOBALS['_MDB2_databases']);
        if ($GLOBALS['_MDB2_databases'][$key]->opened_persistent
            && $GLOBALS['_MDB2_databases'][$key]->in_transaction
        ) {
            $GLOBALS['_MDB2_databases'][$key]->rollback();
        }
    }
}

/**
 * default debug output handler.
 *
 * @param   object  reference to an MDB2 database object
 * @param   string  usually the method name that triggered the debug call:
 *                  for example 'query', 'prepare', 'execute', 'parameters',
 *                  'beginTransaction', 'commit', 'rollback'
 * @param   string  message that should be appended to the debug variable
 * @param   array   contains context information about the debug() call
 *                  common keys are: is_manip, time, result etc
 * @param mixed $db
 * @param mixed $scope
 * @param mixed $message
 * @param mixed $context
 *
 * @return string|void optionally return a modified message, this allows
 *                     rewriting a query before being issued or prepared
 */
function MDB2_defaultDebugOutput(&$db, $scope, $message, $context = [])
{
    $db->debug_output .= $scope . '(' . $db->db_index . '): ';
    $db->debug_output .= $message . $db->getOption('log_line_break');

    return $message;
}
