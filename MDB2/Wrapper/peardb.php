<?php
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2006 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith                                         |
// | All rights reserved.                                                 |
// +----------------------------------------------------------------------+
// | MDB2 is a merge of PEAR DB and Metabases that provides a unified DB  |
// | API as well as database abstraction for PHP applications.            |
// | This LICENSE is in the BSD license style.                            |
// |                                                                      |
// | Redistribution and use in source and binary forms, with or without   |
// | modification, are permitted provided that the following conditions   |
// | are met:                                                             |
// |                                                                      |
// | Redistributions of source code must retain the above copyright       |
// | notice, this list of conditions and the following disclaimer.        |
// |                                                                      |
// | Redistributions in binary form must reproduce the above copyright    |
// | notice, this list of conditions and the following disclaimer in the  |
// | documentation and/or other materials provided with the distribution. |
// |                                                                      |
// | Neither the name of Manuel Lemos, Tomas V.V.Cox, Stig. S. Bakken,    |
// | Lukas Smith nor the names of his contributors may be used to endorse |
// | or promote products derived from this software without specific prior|
// | written permission.                                                  |
// |                                                                      |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
// | FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE      |
// | REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
// | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS|
// |  OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED  |
// | AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT          |
// | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY|
// | WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE          |
// | POSSIBILITY OF SUCH DAMAGE.                                          |
// +----------------------------------------------------------------------+
// | Author: Lukas Smith <smith@pooteeweet.org>                           |
// +----------------------------------------------------------------------+
//
// $Id$
//

/**
 * Wrapper that makes MDB2 behave like PEAR DB
 * WARNING: this wrapper is broken and unmaintained
 *
 * @package  MDB2
 * @category Database
 * @author    Lukas Smith <smith@pooteeweet.org>
 */

require_once 'MDB2.php';

/*
 * The method mapErrorCode in each MDB2_dbtype implementation maps
 * native error codes to one of these.
 *
 * If you add an error code here, make sure you also add a textual
 * version of it in DB::errorMessage().
 */

const DB_OK                        = MDB2_OK;
const DB_ERROR                     = MDB2_ERROR;
const DB_ERROR_SYNTAX              = MDB2_ERROR_SYNTAX;
const DB_ERROR_CONSTRAINT          = MDB2_ERROR_CONSTRAINT;
const DB_ERROR_NOT_FOUND           = MDB2_ERROR_NOT_FOUND;
const DB_ERROR_ALREADY_EXISTS      = MDB2_ERROR_ALREADY_EXISTS;
const DB_ERROR_UNSUPPORTED         = MDB2_ERROR_UNSUPPORTED;
const DB_ERROR_MISMATCH            = MDB2_ERROR_MISMATCH;
const DB_ERROR_INVALID             = MDB2_ERROR_INVALID;
const DB_ERROR_NOT_CAPABLE         = MDB2_ERROR_NOT_CAPABLE;
const DB_ERROR_TRUNCATED           = MDB2_ERROR_TRUNCATED;
const DB_ERROR_INVALID_NUMBER      = MDB2_ERROR_INVALID_NUMBER;
const DB_ERROR_INVALID_DATE        = MDB2_ERROR_INVALID_DATE;
const DB_ERROR_DIVZERO             = MDB2_ERROR_DIVZERO;
const DB_ERROR_NODBSELECTED        = MDB2_ERROR_NODBSELECTED;
const DB_ERROR_CANNOT_CREATE       = MDB2_ERROR_CANNOT_CREATE;
const DB_ERROR_CANNOT_DROP         = MDB2_ERROR_CANNOT_DROP;
const DB_ERROR_NOSUCHTABLE         = MDB2_ERROR_NOSUCHTABLE;
const DB_ERROR_NOSUCHFIELD         = MDB2_ERROR_NOSUCHFIELD;
const DB_ERROR_NEED_MORE_DATA      = MDB2_ERROR_NEED_MORE_DATA;
const DB_ERROR_NOT_LOCKED          = MDB2_ERROR_NOT_LOCKED;
const DB_ERROR_VALUE_COUNT_ON_ROW  = MDB2_ERROR_VALUE_COUNT_ON_ROW;
const DB_ERROR_INVALID_DSN         = MDB2_ERROR_INVALID_DSN;
const DB_ERROR_CONNECT_FAILED      = MDB2_ERROR_CONNECT_FAILED;
const DB_ERROR_EXTENSION_NOT_FOUND = MDB2_ERROR_EXTENSION_NOT_FOUND;
const DB_ERROR_ACCESS_VIOLATION    = MDB2_ERROR_ACCESS_VIOLATION;
const DB_ERROR_NOSUCHDB            = MDB2_ERROR_NOSUCHDB;

const DB_WARNING           = -1000;
const DB_WARNING_READ_ONLY = -1001;

const DB_PARAM_SCALAR = 1;
const DB_PARAM_OPAQUE = 2;
const DB_PARAM_MISC   = 3;

const DB_BINMODE_PASSTHRU = 1;
const DB_BINMODE_RETURN   = 2;
const DB_BINMODE_CONVERT  = 3;

const DB_FETCHMODE_DEFAULT = MDB2_FETCHMODE_DEFAULT;
const DB_FETCHMODE_ORDERED = MDB2_FETCHMODE_ORDERED;
const DB_FETCHMODE_ASSOC   = MDB2_FETCHMODE_ASSOC;
const DB_FETCHMODE_OBJECT  = MDB2_FETCHMODE_OBJECT;
const DB_FETCHMODE_FLIPPED = MDB2_FETCHMODE_FLIPPED;

const DB_GETMODE_ORDERED = DB_FETCHMODE_ORDERED;
const DB_GETMODE_ASSOC   = DB_FETCHMODE_ASSOC;
const DB_GETMODE_FLIPPED = DB_FETCHMODE_FLIPPED;

require_once 'MDB2/Extended.php';
const DB_AUTOQUERY_INSERT = MDB2_AUTOQUERY_INSERT;
const DB_AUTOQUERY_UPDATE = MDB2_AUTOQUERY_UPDATE;

require_once 'MDB2/Driver/Reverse/Common.php';
const DB_TABLEINFO_ORDER      = MDB2_TABLEINFO_ORDER;
const DB_TABLEINFO_ORDERTABLE = MDB2_TABLEINFO_ORDERTABLE;
const DB_TABLEINFO_FULL       = MDB2_TABLEINFO_FULL;

const DB_PORTABILITY_NONE          = MDB2_PORTABILITY_NONE;
const DB_PORTABILITY_LOWERCASE     = MDB2_PORTABILITY_FIX_CASE;
const DB_PORTABILITY_RTRIM         = MDB2_PORTABILITY_RTRIM;
const DB_PORTABILITY_DELETE_COUNT  = MDB2_PORTABILITY_DELETE_COUNT;
const DB_PORTABILITY_NUMROWS       = MDB2_PORTABILITY_NUMROWS;
const DB_PORTABILITY_ERRORS        = MDB2_PORTABILITY_ERRORS;
const DB_PORTABILITY_NULL_TO_EMPTY = MDB2_PORTABILITY_EMPTY_TO_NULL;
const DB_PORTABILITY_ALL           = MDB2_PORTABILITY_ALL;

/**
 * Wrapper that makes MDB2 behave like PEAR DB
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class DB
{
    /**
     * DB::factory()
     */
    public static function factory($type)
    {
        $db = MDB2::factory($type);
        if (MDB2::isError($db)) {
            return $db;
        }
        $obj = new MDB2_PEARProxy($db);
        return $obj;
    }

    /**
     * DB::connect()
     */
    public static function connect($dsn, $options = false)
    {
        if (!is_array($options) && $options) {
            $options = array('persistent' => true);
        }
        $db = MDB2::connect($dsn, $options);
        if (MDB2::isError($db)) {
            return $db;
        }
        $obj = new MDB2_PEARProxy($db);
        return $obj;
    }

    /**
     * DB::apiVersion()
     */
    public static function apiVersion()
    {
        return 2;
    }

    /**
     * DB::isError()
     */
    public static function isError($value)
    {
        return MDB2::isError($value);
    }

    /**
     * DB::isManip()
     */
    public static function isManip($query)
    {
        if (!is_string($query)) {
            return false;
        }

        $manips = 'INSERT|UPDATE|DELETE|REPLACE|'
               . 'CREATE|DROP|'
               . 'LOAD DATA|SELECT .* INTO|COPY|'
               . 'ALTER|GRANT|REVOKE|SET|'
               . 'LOCK|UNLOCK';
        if (preg_match('/^\s*"?('.$manips.')\s+/i', $query)) {
            return true;
        }
        return false;
    }

    /**
     * DB::errorMessage()
     */
    public static function errorMessage($value)
    {
        return MDB2::errorMessage($value);
    }

    /**
     * DB::parseDSN()
     */
    public static function parseDSN($dsn)
    {
        return MDB2::parseDSN($dsn);
    }

    /**
     * DB::assertExtension()
     */
    public static function assertExtension($name)
    {
        if (!extension_loaded($name)) {
            $dlext = OS_WINDOWS ? '.dll' : '.so';
            @dl($name . $dlext);
        }
        return extension_loaded($name);
    }
}

/**
 * MDB2_Error implements a class for reporting portable database error
 * messages.
 *
 * @package MDB2
 * @category Database
 * @author  Stig Bakken <ssb@fast.no>
 */
class DB_Error extends PEAR_Error
{
    public function __construct($code = DB_ERROR, $mode = PEAR_ERROR_RETURN,
        $level = E_USER_NOTICE, $debuginfo = null)
    {
        if (is_int($code)) {
            parent::__construct('DB Error: ' . DB::errorMessage($code), $code, $mode, $level, $debuginfo);
        } else {
            parent::__construct("DB Error: $code", DB_ERROR, $mode, $level, $debuginfo);
        }
    }
}

/**
 * Wrapper that makes MDB2 behave like PEAR DB
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class DB_result extends MDB2_Result_Common
{
    var $result;
    var $row_counter = null;
    var $limit_from  = null;
    var $limit_count = null;

    /**
     * Constructor
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * DB_result::fetchRow()
     */
    public function fetchRow($fetchmode = MDB2_FETCHMODE_DEFAULT, $rownum = null)
    {
        $arr = $this->result->fetchRow($fetchmode, $rownum);
        if ($this->result->db->options['portability'] & DB_PORTABILITY_NULL_TO_EMPTY) {
            $this->_convertNullArrayValuesToEmpty($arr);
        }
        return $arr;
    }

    /**
     * DB_result::fetchInto()
     */
    public function fetchInto(&$arr, $fetchmode = MDB2_FETCHMODE_DEFAULT, $rownum = null)
    {
        $arr = $this->fetchRow($fetchmode, $rownum);
        if ($this->result->db->options['portability'] & DB_PORTABILITY_NULL_TO_EMPTY) {
            $this->_convertNullArrayValuesToEmpty($arr);
        }
        return DB_OK;
    }

    /**
     * DB_result::_convertNullArrayValuesToEmpty()
     */
    protected function _convertNullArrayValuesToEmpty(&$array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_null($value)) {
                    $array[$key] = '';
                }
            }
        }
    }

    /**
     * DB_result::numCols()
     */
    public function numCols()
    {
        return $this->result->numCols();
    }

    /**
     * DB_result::numRows()
     */
    public function numRows()
    {
        return $this->result->numRows();
    }

    /**
     * DB_result::nextResult()
     */
    public function nextResult()
    {
        return $this->result->nextResult();
    }

    /**
     * DB_result::free()
     */
    public function free()
    {
        $err = $this->result->free();
        if (MDB2::isError($err)) {
            return $err;
        }
        $this->result = false;
        return true;
    }

    /**
     * DB_result::free()
     */
    public function tableInfo($mode = null)
    {
        $this->result->db->loadModule('Reverse', null, true);
        return $this->result->db->reverse->tableInfo($this->result, $mode);
    }

    /**
     * DB_result::getRowCounter()
     */
    public function getRowCounter()
    {
        return $this->result->rowCount()+1+$this->result->offset;
    }
}

class DB_row
{
    public function __construct($arr)
    {
        for (reset($arr); $key = key($arr); next($arr)) {
            $this->$key = $arr[$key];
        }
    }
}

class MDB2_PEARProxy extends PEAR
{
    protected $db_object;
    protected $phptype;
    protected $connection;
    protected $dsn;
    protected $autocommit = true;

    /**
     * Constructor
     */
    public function __construct($db_object)
    {
        $this->db_object = $db_object;
        parent::__construct('DB_Error');
        $this->db_object->setOption('seqcol_name', 'id');
        $this->db_object->setOption('result_wrap_class', 'DB_result');
        $this->phptype = $this->db_object->phptype;
        $this->connection = $this->db_object->getConnection();
        $this->dsn = $this->db_object->getDSN();
    }

    /**
     * MDB2_PEARProxy::connect()
     */
    public function connect($dsninfo, $persistent = false)
    {
        $this->options['persistent'] = $persistent;
        return $this->db_object->connect();
    }

    /**
     * MDB2_PEARProxy::disconnect()
     */
    public function disconnect()
    {
        return $this->db_object->disconnect();
    }

    /**
     * MDB2_PEARProxy::toString()
     */
    public function toString()
    {
        return $this->db_object->__toString();
    }

    /**
     * MDB2_PEARProxy::quoteString()
     */
    public function quoteString($string)
    {
        $string = $this->quote($string);
        if ($string[0] == "'") {
            return substr($string, 1, -1);
        }
        return $string;
    }

    /**
     * MDB2_PEARProxy::quote()
     */
    public function quote($string)
    {
        if (is_null($string)) {
            return 'NULL';
        }
        return $this->db_object->quote($string);
    }

    /**
     * MDB2_PEARProxy::quote()
     */
    public function escapeSimple($str)
    {
        return $this->db_object->escape($str);
    }

    /**
     * MDB2_PEARProxy::quoteSmart()
     */
    public function quoteSmart($in)
    {
        if (is_int($in) || is_double($in)) {
            return $in;
        } elseif (is_bool($in)) {
            return $in ? 1 : 0;
        } elseif (is_null($in)) {
            return 'NULL';
        } else {
            return "'" . $this->escapeSimple($in) . "'";
        }
    }

    /**
     * MDB2_PEARProxy::quoteIdentifier()
     */
    public function quoteIdentifier($string)
    {
        return $this->db_object->quoteIdentifier($string, false);
    }

    /**
     * MDB2_PEARProxy::provides()
     * @todo map?
     */
    public function provides($feature)
    {
        return $this->db_object->support($feature);
    }

    /**
     * MDB2_PEARProxy::errorCode()
     */
    public function errorCode($nativecode)
    {
        return $this->db_object->errorCode($nativecode);
    }

    /**
     * MDB2_PEARProxy::errorMessage()
     */
    public function errorMessage($dbcode)
    {
        return $this->db_object->errorMessage($dbcode);
    }

    /**
     * MDB2_PEARProxy::raiseError()
     */
    public function raiseError($code = MDB2_ERROR, $mode = null, $options = null,
        $userinfo = null, $nativecode = null)
    {
        return $this->db_object->raiseError($code, $mode, $options, $userinfo, $nativecode);
    }

    /**
     * MDB2_PEARProxy::setFetchMode()
     */
    public function setFetchMode($fetchmode, $object_class = 'stdClass')
    {
        return $this->db_object->setFetchMode($fetchmode, $object_class);
    }

    /**
     * MDB2_PEARProxy::setOption()
     */
    public function setOption($option, $value)
    {
        return $this->db_object->setOption($option, $value);
    }

    /**
     * MDB2_PEARProxy::getOption()
     */
    public function getOption($option)
    {
        return $this->db_object->getOption($option);
    }

    /**
     * MDB2_PEARProxy::prepare()
     */
    public function prepare($query, $types = array())
    {
        // parse for ! and &
        $result_types = DB::isManip($query) ? MDB2_PREPARE_MANIP : MDB2_PREPARE_RESULT;
        return $this->db_object->prepare($query, $types, $result_types);
    }

    /**
     * MDB2_PEARProxy::autoPrepare()
     */
    public function autoPrepare($table, $table_fields, $mode = MDB2_AUTOQUERY_INSERT, $where = false, $types = array())
    {
        $this->db_object->loadModule('Extended', null, false);
        return $this->db_object->extended->autoPrepare($table, $table_fields, $mode, $where, $types);
    }

    /**
     * MDB2_PEARProxy::autoExecute()
     */
    public function autoExecute($table, $fields_values, $mode, $where = '', $types = array())
    {
        $this->db_object->loadModule('Extended', null, false);
        return $this->db_object->extended->autoExecute($table, $fields_values, $mode, $where, $types);
    }

    /**
     * MDB2_PEARProxy::buildManipSQL()
     */
    public function buildManipSQL($table, $table_fields, $mode, $where = false)
    {
        $this->db_object->loadModule('Extended', null, false);
        return $this->db_object->extended->buildManipSQL($table, $table_fields, $mode, $where);
    }

    /**
     * MDB2_PEARProxy::execute()
     */
    public function execute($stmt, $data = false)
    {
        $stmt->bindValueArray($data);
        return $stmt->execute();
    }

    /**
     * MDB2_PEARProxy::executeMultiple()
     */
    public function executeMultiple($stmt, $data)
    {
        $this->db_object->loadModule('Extended', null, false);
        return $this->db_object->extended->executeMultiple($stmt, null, $data);
    }

    /**
     * MDB2_PEARProxy::query()
     */
    public function query($query, $params = array())
    {
        if (sizeof($params) > 0) {
            $sth = $this->db_object->prepare($query);
            if (MDB2::isError($sth)) {
                return $sth;
            }
            if (!is_array($params)) {
                $params = array($params);
            }
            $sth->bindValueArray($params);
            return $sth->execute();
        }
        if (DB::isManip($query)) {
            // PEAR::DB return DB_OK on success or DB_Error object on failure
            $result =& $this->db_object->exec($query);
            if (MDB2::isError($result)) {
                return $result;
           }
            return DB_OK;
        }
        return $this->db_object->query($query);
    }

    /**
     * MDB2_PEARProxy::simpleQuery()
     */
    public function simpleQuery($query)
    {
        if (DB::isManip($query)) {
            return $this->db_object->exec($query);
        }
        $result = $this->db_object->query($query);
        if (MDB2::isError($result) || $result === MDB2_OK) {
            return $result;
        } else {
            return $result->getResource();
        }
    }

    /**
     * MDB2_PEARProxy::modifyLimitQuery()
     */
    public function modifyLimitQuery($query, $from, $count, $params = array())
    {
        $is_manip = DB::isManip($query);
        $query = $this->db_object->_modifyQuery($query, $is_manip, $count, $from);
        return $query;
    }

    /**
     * MDB2_PEARProxy::limitQuery()
     */
    public function limitQuery($query, $from, $count, $params = array())
    {
        $result = $this->db_object->setLimit($count, $from);
        if (MDB2::isError($result)) {
            return $result;
        }
        $result = $this->query($query, $params);
        return $result;
    }

    /**
     * MDB2_PEARProxy::_convertNullArrayValuesToEmpty()
     */
    public function _convertNullArrayValuesToEmpty(&$array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_null($value)) {
                    $array[$key] = '';
                }
            }
        }
    }

    /**
     * MDB2_PEARProxy::getOne()
     */
    public function getOne($query, $params = array())
    {
        $result = $this->query($query, $params);
        if (DB::isError($result)) {
            return $result;
        }
        $one = $result->fetchOne();
        if (is_null($one)) {
            $one = '';
        }
        return $one;
    }

    /**
     * MDB2_PEARProxy::getRow()
     */
    public function getRow($query, $params = array(), $fetchmode = MDB2_FETCHMODE_DEFAULT)
    {
        if (!is_array($params)) {
            if (is_array($fetchmode)) {
                if (is_null($params)) {
                    $tmp = DB_FETCHMODE_DEFAULT;
                } else {
                    $tmp = $params;
                }
                $params = $fetchmode;
                $fetchmode = $tmp;
            } elseif (!is_null($params)) {
                $fetchmode = $params;
                $params = array();
            }
        }
        $result = $this->query($query, $params);
        if (DB::isError($result)) {
            return $result;
        }
        return $result->fetchRow($fetchmode);
    }

    /**
     * MDB2_PEARProxy::getCol()
     */
    public function getCol($query, $col = 0, $params = array())
    {
        $result = $this->query($query, $params);
        if (DB::isError($result)) {
            return $result;
        }
        $col = $result->fetchCol($col);
        $this->_convertNullArrayValuesToEmpty($col);
        return $col;
    }

    /**
     * MDB2_PEARProxy::getAssoc()
     */
    public function getAssoc($query, $force_array = false, $params = array(), $fetchmode = MDB2_FETCHMODE_ORDERED, $group = false)
    {
        $result = $this->query($query, $params);
        $all = $result->fetchAll($fetchmode, true, $force_array, $group);
        $first = reset($all);
        if (isset($first) && $this->db_object->options['portability'] & DB_PORTABILITY_NULL_TO_EMPTY) {
            if (is_array($first)) {
                foreach ($all as $key => $arr) {
                    $this->_convertNullArrayValuesToEmpty($all[$key]);
                }
            } elseif (is_object($first)) {
                foreach ($all as $key => $arr) {
                    $tmp = get_object_vars($all[$key]);
                    if (is_array($tmp)) {
                        $this->_convertNullArrayValuesToEmpty($tmp);
                        foreach ($tmp as $key2 => $column) {
                            $all[$key]->{$key2} = $column;
                        }
                    }
                }
            }
        }
        return $all;
    }

    /**
     * MDB2_PEARProxy::getAll()
     */
    public function getAll($query, $params = null, $fetchmode = MDB2_FETCHMODE_DEFAULT)
    {
        if (!is_array($params)) {
            if (is_array($fetchmode)) {
                if (is_null($params)) {
                    $tmp = DB_FETCHMODE_DEFAULT;
                } else {
                    $tmp = $params;
                }
                $params = $fetchmode;
                $fetchmode = $tmp;
            } elseif (!is_null($params)) {
                $fetchmode = $params;
                $params = array();
            }
        }
        $result = $this->query($query, $params);
        if (DB::isError($result)) {
            return $result;
        }
        $all = $result->fetchAll($fetchmode);
        $first = reset($all);
        if (isset($first) && $this->db_object->options['portability'] & DB_PORTABILITY_NULL_TO_EMPTY) {
            if (is_array($first)) {
                foreach ($all as $key => $arr) {
                    $this->_convertNullArrayValuesToEmpty($all[$key]);
                }
            } elseif (is_object($first)) {
                foreach ($all as $key => $arr) {
                    $tmp = get_object_vars($all[$key]);
                    if (is_array($tmp)) {
                        $this->_convertNullArrayValuesToEmpty($tmp);
                        foreach ($tmp as $key2 => $column) {
                            $all[$key]->{$key2} = $column;
                        }
                    }
                }
            }
        }
        return $all;
    }

    /**
     * MDB2_PEARProxy::autoCommit()
     */
    public function autoCommit($onoff = false)
    {
        $this->autocommit = $onoff;
        $in_transaction = $this->db_object->inTransaction();
        if ($onoff) {
            if ($in_transaction) {
                return $this->db_object->commit();
            }
        } elseif (!$in_transaction) {
            return $this->db_object->beginTransaction();
        }
        return DB_OK;
    }

    /**
     * MDB2_PEARProxy::commit()
     */
    public function commit()
    {
        $result = $this->db_object->commit();
        if (!MDB2::isError($result) && !$this->autocommit) {
            $result = $this->db_object->beginTransaction();
        }
        return $result;
    }

    /**
     * MDB2_PEARProxy::rollback()
     */
    public function rollback()
    {
        $result = $this->db_object->rollback();
        if (!MDB2::isError($result) && !$this->autocommit) {
            $result = $this->db_object->beginTransaction();
        }
        return $result;
    }

    /**
     * MDB2_PEARProxy::affectedRows()
     */
    public function affectedRows()
    {
        return $this->db_object->_affectedRows();
    }

    /**
     * MDB2_PEARProxy::errorNative()
     */
    public function errorNative()
    {
        $infos = $this->db_object->errorInfo();
        return $infos[2];
    }

    /**
     * MDB2_PEARProxy::nextId()
     */
    public function nextId($seq_name, $ondemand = true)
    {
        return $this->db_object->nextID($seq_name, $ondemand);
    }

    /**
     * MDB2_PEARProxy::createSequence()
     */
    public function createSequence($seq_name)
    {
        $this->db_object->loadModule('Manager', null, true);
        return $this->db_object->manager->createSequence($seq_name, 1);
    }

    /**
     * MDB2_PEARProxy::dropSequence()
     */
    public function dropSequence($seq_name)
    {
        $this->db_object->loadModule('Manager', null, true);
        return $this->db_object->manager->dropSequence($seq_name);
    }

    /**
     * MDB2_PEARProxy::_wrapResource()
     */
    protected function _wrapResource($result)
    {
        if (is_resource($result)) {
            $result_class = $this->db_object->getOption('result_buffering')
                ? $this->db_object->getOption('buffered_result_class') : $$this->db_object->getOption('result_class');
            $class_name = sprintf($result_class, $this->db_object->phptype);
            $result = new $class_name($this->db_object, $result);
        }
        return $result;
    }

    /**
     * MDB2_PEARProxy::fetchInto()
     */
    public function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        $result = $this->_wrapResource($result);
        if (!is_null($rownum)) {
            $result->seek($rownum);
        }
        $arr = $result->fetchRow($fetchmode);
    }

    /**
     * MDB2_PEARProxy::freePrepared()
     */
    public function freePrepared($prepared)
    {
        return $this->db_object->freePrepared($prepared);
    }

    /**
     * MDB2_PEARProxy::freeResult()
     */
    public function freeResult($result)
    {
        $result = $this->_wrapResource($result);
        return $result->free();
    }

    /**
     * MDB2_PEARProxy::numCols()
     */
    public function numCols($result)
    {
        $result = $this->_wrapResource($result);
        return $result->numCols();
    }

    /**
     * MDB2_PEARProxy::numRows()
     */
    public function numRows($result)
    {
        $result = $this->_wrapResource($result);
        return $result->numRows();
    }

    /**
     * MDB2_PEARProxy::nextResult()
     */
    public function nextResult($result)
    {
        $result = $this->_wrapResource($result);
        return $result->nextResult();
    }

    /**
     * MDB2_PEARProxy::tableInfo()
     */
    public function tableInfo($result, $mode = null)
    {
        $result = $this->_wrapResource($result);
        if (is_string($result) || MDB2::isResultCommon($result)) {
            $this->db_object->loadModule('Reverse', null, true);
            return $this->db_object->reverse->tableInfo($result, $mode);
        }
        return $result->tableInfo($mode);
    }

    /**
     * MDB2_PEARProxy::getTables()
     */
    public function getTables()
    {
        return $this->getListOf('tables');
    }

    /**
     * MDB2_PEARProxy::getListOf()
     */
    public function getListOf($type)
    {
        $this->db_object->loadModule('Manager', null, true);
        switch ($type) {
        case 'tables':
            return $this->db_object->manager->listTables();
        case 'views':
            return $this->db_object->manager->listViews();
        case 'users':
            return $this->db_object->manager->listUsers();
        case 'functions':
            return $this->db_object->manager->listFunctions();
        case 'databases':
            return $this->db_object->manager->listDatabases();
        default:
            return $this->db_object->raiseError(MDB2_ERROR_UNSUPPORTED, null, null, 'Not supported', __FUNCTION__);
        }
    }
}

?>
