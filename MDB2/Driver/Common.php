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
 */

/**
 * MDB2_Driver_Common: Base class that is extended by each MDB2 driver.
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2_Driver_Common implements Stringable
{
    /**
     * @var MDB2_Driver_Datatype_Common
     */
    public $datatype;

    /**
     * @var MDB2_Extended
     */
    public $extended;

    /**
     * @var MDB2_Driver_Function_Common
     */
    public $function;

    /**
     * @var MDB2_Driver_Manager_Common
     */
    public $manager;

    /**
     * @var MDB2_Driver_Native_Commonn
     */
    public $native;

    /**
     * @var MDB2_Driver_Reverse_Common
     */
    public $reverse;

    /**
     * index of the MDB2 object within the $GLOBALS['_MDB2_databases'] array.
     *
     * @var int
     */
    public $db_index = 0;

    /**
     * DSN used for the next query.
     *
     * @var array
     */
    public $dsn = [];

    /**
     * DSN that was used to create the current connection.
     *
     * @var array
     */
    public $connected_dsn = [];

    /**
     * connection resource.
     *
     * @var mixed
     */
    public $connection = 0;

    /**
     * if the current opened connection is a persistent connection.
     *
     * @var bool
     */
    public $opened_persistent;

    /**
     * the name of the database for the next query.
     *
     * @var string
     */
    public $database_name = '';

    /**
     * the name of the database currently selected.
     *
     * @var string
     */
    public $connected_database_name = '';

    /**
     * server version information.
     *
     * @var string
     */
    public $connected_server_info = '';

    /**
     * list of all supported features of the given driver.
     *
     * @var array
     */
    public $supported = [
        'sequences'            => false,
        'indexes'              => false,
        'affected_rows'        => false,
        'summary_functions'    => false,
        'order_by_text'        => false,
        'transactions'         => false,
        'savepoints'           => false,
        'current_id'           => false,
        'limit_queries'        => false,
        'LOBs'                 => false,
        'replace'              => false,
        'sub_selects'          => false,
        'triggers'             => false,
        'auto_increment'       => false,
        'primary_key'          => false,
        'result_introspection' => false,
        'prepared_statements'  => false,
        'identifier_quoting'   => false,
        'pattern_escaping'     => false,
        'new_link'             => false,
    ];

    /**
     * Array of supported options that can be passed to the MDB2 instance.
     *
     * The options can be set during object creation, using
     * MDB2::connect(), MDB2::factory() or MDB2::singleton(). The options can
     * also be set after the object is created, using MDB2::setOptions() or
     * MDB2_Driver_Common::setOption().
     * The list of available option includes:
     * <ul>
     *  <li>$options['ssl'] -> boolean: determines if ssl should be used for connections</li>
     *  <li>$options['field_case'] -> CASE_LOWER|CASE_UPPER: determines what case to force on field/table names</li>
     *  <li>$options['disable_query'] -> boolean: determines if queries should be executed</li>
     *  <li>$options['result_class'] -> string: class used for result sets</li>
     *  <li>$options['buffered_result_class'] -> string: class used for buffered result sets</li>
     *  <li>$options['result_wrap_class'] -> string: class used to wrap result sets into</li>
     *  <li>$options['result_buffering'] -> boolean should results be buffered or not?</li>
     *  <li>$options['fetch_class'] -> string: class to use when fetch mode object is used</li>
     *  <li>$options['persistent'] -> boolean: persistent connection?</li>
     *  <li>$options['debug'] -> integer: numeric debug level</li>
     *  <li>$options['debug_handler'] -> string: function/method that captures debug messages</li>
     *  <li>$options['debug_expanded_output'] -> bool: BC option to determine if more context information should be send to the debug handler</li>
     *  <li>$options['default_text_field_length'] -> integer: default text field length to use</li>
     *  <li>$options['lob_buffer_length'] -> integer: LOB buffer length</li>
     *  <li>$options['log_line_break'] -> string: line-break format</li>
     *  <li>$options['idxname_format'] -> string: pattern for index name</li>
     *  <li>$options['seqname_format'] -> string: pattern for sequence name</li>
     *  <li>$options['savepoint_format'] -> string: pattern for auto generated savepoint names</li>
     *  <li>$options['statement_format'] -> string: pattern for prepared statement names</li>
     *  <li>$options['seqcol_name'] -> string: sequence column name</li>
     *  <li>$options['quote_identifier'] -> boolean: if identifier quoting should be done when check_option is used</li>
     *  <li>$options['use_transactions'] -> boolean: if transaction use should be enabled</li>
     *  <li>$options['decimal_places'] -> integer: number of decimal places to handle</li>
     *  <li>$options['portability'] -> integer: portability constant</li>
     *  <li>$options['modules'] -> array: short to long module name mapping for __call()</li>
     *  <li>$options['emulate_prepared'] -> boolean: force prepared statements to be emulated</li>
     *  <li>$options['datatype_map'] -> array: map user defined datatypes to other primitive datatypes</li>
     *  <li>$options['datatype_map_callback'] -> array: callback function/method that should be called</li>
     *  <li>$options['bindname_format'] -> string: regular expression pattern for named parameters</li>
     *  <li>$options['multi_query'] -> boolean: determines if queries returning multiple result sets should be executed</li>
     *  <li>$options['max_identifiers_length'] -> integer: max identifier length</li>
     *  <li>$options['default_fk_action_onupdate'] -> string: default FOREIGN KEY ON UPDATE action ['RESTRICT'|'NO ACTION'|'SET DEFAULT'|'SET NULL'|'CASCADE']</li>
     *  <li>$options['default_fk_action_ondelete'] -> string: default FOREIGN KEY ON DELETE action ['RESTRICT'|'NO ACTION'|'SET DEFAULT'|'SET NULL'|'CASCADE']</li>
     * </ul>
     *
     * @var array
     *
     * @see     MDB2::connect()
     * @see     MDB2::factory()
     * @see     MDB2::singleton()
     * @see     MDB2_Driver_Common::setOption()
     */
    public $options = [
        'ssl'                       => false,
        'field_case'                => CASE_LOWER,
        'disable_query'             => false,
        'result_class'              => 'MDB2_Result_%s',
        'buffered_result_class'     => 'MDB2_BufferedResult_%s',
        'result_wrap_class'         => false,
        'result_buffering'          => true,
        'fetch_class'               => 'stdClass',
        'persistent'                => false,
        'debug'                     => 0,
        'debug_handler'             => 'MDB2_defaultDebugOutput',
        'debug_expanded_output'     => false,
        'default_text_field_length' => 4096,
        'lob_buffer_length'         => 8192,
        'log_line_break'            => "\n",
        'idxname_format'            => '%s_idx',
        'seqname_format'            => '%s_seq',
        'savepoint_format'          => 'MDB2_SAVEPOINT_%s',
        'statement_format'          => 'MDB2_STATEMENT_%1$s_%2$s',
        'seqcol_name'               => 'sequence',
        'quote_identifier'          => false,
        'use_transactions'          => true,
        'decimal_places'            => 2,
        'portability'               => MDB2_PORTABILITY_ALL,
        'modules'                   => [
            'ex' => 'Extended',
            'dt' => 'Datatype',
            'mg' => 'Manager',
            'rv' => 'Reverse',
            'na' => 'Native',
            'fc' => 'Function',
        ],
        'emulate_prepared'           => false,
        'datatype_map'               => [],
        'datatype_map_callback'      => [],
        'nativetype_map_callback'    => [],
        'lob_allow_url_include'      => false,
        'bindname_format'            => '(?:\d+)|(?:[a-zA-Z][a-zA-Z0-9_]*)',
        'max_identifiers_length'     => 30,
        'default_fk_action_onupdate' => 'RESTRICT',
        'default_fk_action_ondelete' => 'RESTRICT',
    ];

    /**
     * string array.
     *
     * @var string
     */
    public $string_quoting = [
        'start'          => "'",
        'end'            => "'",
        'escape'         => false,
        'escape_pattern' => false,
    ];

    /**
     * identifier quoting.
     *
     * @var array
     */
    public $identifier_quoting = [
        'start'  => '"',
        'end'    => '"',
        'escape' => '"',
    ];

    /**
     * sql comments.
     *
     * @var array
     */
    public $sql_comments = [
        ['start' => '--', 'end' => "\n", 'escape' => false],
        ['start' => '/*', 'end' => '*/', 'escape' => false],
    ];

    /**
     * column alias keyword.
     *
     * @var string
     */
    public $as_keyword = ' AS ';

    /**
     * warnings.
     *
     * @var array
     */
    public $warnings = [];

    /**
     * string with the debugging information.
     *
     * @var string
     */
    public $debug_output = '';

    /**
     * determine if there is an open transaction.
     *
     * @var bool
     */
    public $in_transaction = false;

    /**
     * the smart transaction nesting depth.
     *
     * @var int
     */
    public $nested_transaction_counter;

    /**
     * result offset used in the next query.
     *
     * @var int
     */
    public $offset = 0;

    /**
     * result limit used in the next query.
     *
     * @var int
     */
    public $limit = 0;

    /**
     * Database backend used in PHP (mysql, odbc etc.).
     *
     * @var string
     */
    public $phptype;

    /**
     * Database used with regards to SQL syntax etc.
     *
     * @var string
     */
    public $dbsyntax;

    /**
     * the last query sent to the driver.
     *
     * @var string
     */
    public $last_query;

    /**
     * the default fetchmode used.
     *
     * @var int
     */
    public $fetchmode = MDB2_FETCHMODE_ORDERED;

    /**
     * comparision wildcards.
     *
     * @var array
     */
    protected $wildcards = ['%', '_'];

    /**
     * the first error that occured inside a nested transaction.
     *
     * @var bool|MDB2_Error
     */
    protected $has_transaction_error = false;

    /**
     * array of module instances.
     *
     * @var array
     */
    protected $modules = [];

    /**
     * determines of the PHP4 destructor emulation has been enabled yet.
     *
     * @var array
     */
    protected $destructor_registered = true;

    /**
     * @var PEAR
     */
    protected $pear;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $db_index = array_key_last($GLOBALS['_MDB2_databases']) + 1;
        $GLOBALS['_MDB2_databases'][$db_index] = &$this;
        $this->db_index = $db_index;
        $this->pear = new PEAR();
    }

    /**
     *  Destructor.
     */
    public function __destruct()
    {
        $this->disconnect(false);
    }

    /**
     * Free the internal references so that the instance can be destroyed.
     *
     * @return bool true on success, false if result is invalid
     */
    public function free()
    {
        unset($GLOBALS['_MDB2_databases'][$this->db_index], $this->db_index);

        return MDB2_OK;
    }

    /**
     * String conversation.
     *
     * @return string representation of the object
     */
    public function __toString(): string
    {
        $info = static::class;
        $info .= ': (phptype = ' . $this->phptype . ', dbsyntax = ' . $this->dbsyntax . ')';
        if ($this->connection) {
            $info .= ' [connected]';
        }

        return $info;
    }

    /**
     * This method is used to collect information about an error.
     *
     * @param   mixed   error code or resource
     * @param mixed|null $error
     *
     * @return array with MDB2 errorcode, native error code, native message
     */
    public function errorInfo($error = null)
    {
        return [$error, null, null];
    }

    /**
     * This method is used to communicate an error and invoke error
     * callbacks etc.  Basically a wrapper for PEAR::raiseError
     * without the message string.
     *
     * @param mixed  $code     integer error code, or a PEAR error object (all
     *                         other parameters are ignored if this parameter is
     *                         an object
     * @param int    $mode     error mode, see PEAR_Error docs
     * @param mixed  $options  If error mode is PEAR_ERROR_TRIGGER, this is the
     *                         error level (E_USER_NOTICE etc). If error mode is
     *                         PEAR_ERROR_CALLBACK, this is the callback function,
     *                         either as a function name, or as an array of an
     *                         object and method name. For other error modes this
     *                         parameter is ignored.
     * @param string $userinfo Extra debug information. Defaults to the last
     *                         query and native error code.
     * @param string $method   name of the method that triggered the error
     * @param string $dummy1   not used
     * @param bool   $dummy2   not used
     *
     * @return PEAR_Error instance of a PEAR Error object
     *
     * @see    PEAR_Error
     */
    public function raiseError(
        $code = null,
        $mode = null,
        $options = null,
        $userinfo = null,
        $method = null,
        $dummy1 = null,
        $dummy2 = false
    ) {
        $userinfo = "[Error message: {$userinfo}]\n";
        // The error is yet a MDB2 error object
        if (MDB2::isError($code)) {
            // because we use the static PEAR::raiseError, our global
            // handler should be used if it is set
            if ((null === $mode) && !empty($this->_default_error_mode)) {
                $mode = $this->_default_error_mode;
                $options = $this->_default_error_options;
            }
            if (null === $userinfo) {
                $userinfo = $code->getUserinfo();
            }
            $code = $code->getCode();
        } elseif ($code == MDB2_ERROR_NOT_FOUND) {
            // extension not loaded: don't call $this->errorInfo() or the script
            // will die
        } elseif (isset($this->connection)) {
            if (!empty($this->last_query)) {
                $userinfo .= "[Last executed query: {$this->last_query}]\n";
            }
            $native_errno = $native_msg = null;
            [$code, $native_errno, $native_msg] = $this->errorInfo($code);
            if ((null !== $native_errno) && $native_errno !== '') {
                $userinfo .= "[Native code: {$native_errno}]\n";
            }
            if ((null !== $native_msg) && $native_msg !== '') {
                $userinfo .= '[Native message: ' . strip_tags($native_msg) . "]\n";
            }
            if (null !== $method) {
                $userinfo = $method . ': ' . $userinfo;
            }
        }

        $err = $this->pear->raiseError(null, $code, $mode, $options, $userinfo, 'MDB2_Error', true);
        if ($err->getMode() !== PEAR_ERROR_RETURN
            && isset($this->nested_transaction_counter) && !$this->has_transaction_error
        ) {
            $this->has_transaction_error = $err;
        }

        return $err;
    }

    /**
     * reset the warning array.
     */
    public function resetWarnings()
    {
        $this->warnings = [];
    }

    /**
     * Get all warnings in reverse order.
     * This means that the last warning is the first element in the array.
     *
     * @return array with warnings
     *
     * @see     resetWarnings()
     */
    public function getWarnings()
    {
        return array_reverse($this->warnings);
    }

    /**
     * Sets which fetch mode should be used by default on queries
     * on this connection.
     *
     * @param   int     MDB2_FETCHMODE_ORDERED, MDB2_FETCHMODE_ASSOC
     *                               or MDB2_FETCHMODE_OBJECT
     * @param   string  the class name of the object to be returned
     *                               by the fetch methods when the
     *                               MDB2_FETCHMODE_OBJECT mode is selected.
     *                               If no class is specified by default a cast
     *                               to object from the assoc array row will be
     *                               done.  There is also the possibility to use
     *                               and extend the 'MDB2_row' class.
     * @param mixed $fetchmode
     * @param mixed $object_class
     *
     * @return mixed MDB2_OK or MDB2 Error Object
     *
     * @see     MDB2_FETCHMODE_ORDERED, MDB2_FETCHMODE_ASSOC, MDB2_FETCHMODE_OBJECT
     */
    public function setFetchMode($fetchmode, $object_class = 'stdClass')
    {
        switch ($fetchmode) {
            case MDB2_FETCHMODE_OBJECT:
                $this->options['fetch_class'] = $object_class;

                // no break
            case MDB2_FETCHMODE_ORDERED:
            case MDB2_FETCHMODE_ASSOC:
                $this->fetchmode = $fetchmode;
                break;

            default:
                return $this->raiseError(
                    MDB2_ERROR_UNSUPPORTED,
                    null,
                    null,
                    'invalid fetchmode mode',
                    __FUNCTION__
                );
        }

        return MDB2_OK;
    }

    /**
     * set the option for the db class.
     *
     * @param   string  option name
     * @param   mixed   value for the option
     * @param mixed $option
     * @param mixed $value
     *
     * @return mixed MDB2_OK or MDB2 Error Object
     */
    public function setOption($option, $value)
    {
        if (array_key_exists($option, $this->options)) {
            $this->options[$option] = $value;

            return MDB2_OK;
        }

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            "unknown option {$option}",
            __FUNCTION__
        );
    }

    /**
     * Returns the value of an option.
     *
     * @param   string  option name
     * @param mixed $option
     *
     * @return mixed the option value or error object
     */
    public function getOption($option)
    {
        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            "unknown option {$option}",
            __FUNCTION__
        );
    }

    /**
     * set a debug message.
     *
     * @param   string  message that should be appended to the debug variable
     * @param   string  usually the method name that triggered the debug call:
     *                  for example 'query', 'prepare', 'execute', 'parameters',
     *                  'beginTransaction', 'commit', 'rollback'
     * @param   array   contains context information about the debug() call
     *                  common keys are: is_manip, time, result etc
     * @param mixed $message
     * @param mixed $scope
     * @param mixed $context
     */
    public function debug($message, $scope = '', $context = [])
    {
        if ($this->options['debug'] && $this->options['debug_handler']) {
            if (!$this->options['debug_expanded_output']) {
                if (!empty($context['when']) && $context['when'] !== 'pre') {
                    return null;
                }
                $context = empty($context['is_manip']) ? false : $context['is_manip'];
            }

            return call_user_func_array($this->options['debug_handler'], [&$this, $scope, $message, $context]);
        }

        return null;
    }

    /**
     * output debug info.
     *
     * @return string content of the debug_output class variable
     */
    public function getDebugOutput()
    {
        return $this->debug_output;
    }

    /**
     * Quotes a string so it can be safely used in a query. It will quote
     * the text so it can safely be used within a query.
     *
     * @param   string  the input string to quote
     * @param   bool    escape wildcards
     * @param mixed $text
     * @param mixed $escape_wildcards
     *
     * @return string quoted string
     */
    public function escape($text, $escape_wildcards = false)
    {
        if ($escape_wildcards) {
            $text = $this->escapePattern($text);
        }

        return str_replace($this->string_quoting['end'], $this->string_quoting['escape'] . $this->string_quoting['end'], $text);
    }

    /**
     * Quotes pattern (% and _) characters in a string).
     *
     * @param   string  the input string to quote
     * @param mixed $text
     *
     * @return string quoted string
     */
    public function escapePattern($text)
    {
        if ($this->string_quoting['escape_pattern']) {
            $text = str_replace($this->string_quoting['escape_pattern'], $this->string_quoting['escape_pattern'] . $this->string_quoting['escape_pattern'], $text);
            foreach ($this->wildcards as $wildcard) {
                $text = str_replace($wildcard, $this->string_quoting['escape_pattern'] . $wildcard, $text);
            }
        }

        return $text;
    }

    /**
     * Quote a string so it can be safely used as a table or column name.
     *
     * Delimiting style depends on which database driver is being used.
     *
     * NOTE: just because you CAN use delimited identifiers doesn't mean
     * you SHOULD use them.  In general, they end up causing way more
     * problems than they solve.
     *
     * NOTE: if you have table names containing periods, don't use this method
     * (@see bug #11906)
     *
     * Portability is broken by using the following characters inside
     * delimited identifiers:
     *   + backtick (<kbd>`</kbd>) -- due to MySQL
     *   + double quote (<kbd>"</kbd>) -- due to Oracle
     *   + brackets (<kbd>[</kbd> or <kbd>]</kbd>) -- due to Access
     *
     * Delimited identifiers are known to generally work correctly under
     * the following drivers:
     *   + mssql
     *   + mysql
     *   + mysqli
     *   + oci8
     *   + pgsql
     *   + sqlite
     *
     * InterBase doesn't seem to be able to use delimited identifiers
     * via PHP 4.  They work fine under PHP 5.
     *
     * @param   string  identifier name to be quoted
     * @param   bool    check the 'quote_identifier' option
     * @param mixed $str
     * @param mixed $check_option
     *
     * @return string quoted identifier string
     */
    public function quoteIdentifier($str, $check_option = false)
    {
        if ($check_option && !$this->options['quote_identifier']) {
            return $str;
        }
        $str = str_replace($this->identifier_quoting['end'], $this->identifier_quoting['escape'] . $this->identifier_quoting['end'], $str);
        $parts = explode('.', $str);
        foreach (array_keys($parts) as $k) {
            $parts[$k] = $this->identifier_quoting['start'] . $parts[$k] . $this->identifier_quoting['end'];
        }

        return implode('.', $parts);
    }

    /**
     * Gets the string to alias column.
     *
     * @return string to use when aliasing a column
     */
    public function getAsKeyword()
    {
        return $this->as_keyword;
    }

    /**
     * Returns a native connection.
     *
     * @return mixed a valid MDB2 connection object,
     *               or a MDB2 error object on error
     */
    public function getConnection()
    {
        $result = $this->connect();
        if (MDB2::isError($result)) {
            return $result;
        }

        return $this->connection;
    }

    /**
     * Do all necessary conversions on result arrays to fix DBMS quirks.
     *
     * Note: This API is package-private. It is not indended to be part of
     * the public API but is used by MDB2 driver packages.
     *
     * @param   array   the array to be fixed (passed by reference)
     * @param   array   bit-wise addition of the required portability modes
     * @param mixed $row
     * @param mixed $mode
     */
    public function fixResultArrayValues(&$row, $mode)
    {
        switch ($mode) {
            case MDB2_PORTABILITY_EMPTY_TO_NULL:
                foreach ($row as $key => $value) {
                    if ($value === '') {
                        $row[$key] = null;
                    }
                }
                break;

            case MDB2_PORTABILITY_RTRIM:
                foreach ($row as $key => $value) {
                    if (is_string($value)) {
                        $row[$key] = rtrim($value);
                    }
                }
                break;

            case MDB2_PORTABILITY_FIX_ASSOC_FIELD_NAMES:
                $tmp_row = [];
                foreach ($row as $key => $value) {
                    $tmp_row[preg_replace('/^(?:.*\.)?([^.]+)$/', '\1', $key)] = $value;
                }
                $row = $tmp_row;
                break;

            case MDB2_PORTABILITY_RTRIM + MDB2_PORTABILITY_EMPTY_TO_NULL:
                foreach ($row as $key => $value) {
                    if ($value === '') {
                        $row[$key] = null;
                    } elseif (is_string($value)) {
                        $row[$key] = rtrim($value);
                    }
                }
                break;

            case MDB2_PORTABILITY_RTRIM + MDB2_PORTABILITY_FIX_ASSOC_FIELD_NAMES:
                $tmp_row = [];
                foreach ($row as $key => $value) {
                    if (is_string($value)) {
                        $value = rtrim($value);
                    }
                    $tmp_row[preg_replace('/^(?:.*\.)?([^.]+)$/', '\1', $key)] = $value;
                }
                $row = $tmp_row;
                break;

            case MDB2_PORTABILITY_EMPTY_TO_NULL + MDB2_PORTABILITY_FIX_ASSOC_FIELD_NAMES:
                $tmp_row = [];
                foreach ($row as $key => $value) {
                    if ($value === '') {
                        $value = null;
                    }
                    $tmp_row[preg_replace('/^(?:.*\.)?([^.]+)$/', '\1', $key)] = $value;
                }
                $row = $tmp_row;
                break;

            case MDB2_PORTABILITY_RTRIM + MDB2_PORTABILITY_EMPTY_TO_NULL + MDB2_PORTABILITY_FIX_ASSOC_FIELD_NAMES:
                $tmp_row = [];
                foreach ($row as $key => $value) {
                    if ($value === '') {
                        $value = null;
                    } elseif (is_string($value)) {
                        $value = rtrim($value);
                    }
                    $tmp_row[preg_replace('/^(?:.*\.)?([^.]+)$/', '\1', $key)] = $value;
                }
                $row = $tmp_row;
                break;
        }
    }

    /**
     * loads a module.
     *
     * @param   string  name of the module that should be loaded
     *                  (only used for error messages)
     * @param   string  name of the property into which the class will be loaded
     * @param   bool    if the class to load for the module is specific to the
     *                  phptype
     * @param mixed      $module
     * @param mixed|null $property
     * @param mixed|null $phptype_specific
     *
     * @return object on success a reference to the given module is returned
     *                and on failure a PEAR error
     */
    public function loadModule($module, $property = null, $phptype_specific = null)
    {
        if (!$property) {
            $property = strtolower($module);
        }

        if (!isset($this->{$property})) {
            $version = $phptype_specific;
            if ($phptype_specific !== false) {
                $version = true;
                $class_name = 'MDB2_Driver_' . $module . '_' . $this->phptype;
            }

            if ($phptype_specific === false || !class_exists($class_name)) {
                $version = false;
                $class_name = 'MDB2_' . $module;
            }

            $err = MDB2::loadClass($class_name, $this->getOption('debug'));
            if (MDB2::isError($err)) {
                return $err;
            }

            // load module in a specific version
            if ($version) {
                if (method_exists($class_name, 'getClassName')) {
                    $class_name_new = call_user_func([$class_name, 'getClassName'], $this->db_index);
                    if ($class_name != $class_name_new) {
                        $class_name = $class_name_new;
                        $err = MDB2::loadClass($class_name, $this->getOption('debug'));
                        if (MDB2::isError($err)) {
                            return $err;
                        }
                    }
                }
            }

            if (!MDB2::classExists($class_name)) {
                return $this->raiseError(
                    MDB2_ERROR_LOADMODULE,
                    null,
                    null,
                    "unable to load module '{$module}' into property '{$property}'",
                    __FUNCTION__
                );
            }
            $this->{$property} = new $class_name($this->db_index);
            $this->modules[$module] = $this->{$property};
            if ($version) {
                // this will be used in the connect method to determine if the module
                // needs to be loaded with a different version if the server
                // version changed in between connects
                $this->loaded_version_modules[] = $property;
            }
        }

        return $this->{$property};
    }

    /**
     * Calls a module method using the __call magic method.
     *
     * @param   string  method name
     * @param   array   arguments
     * @param mixed $method
     * @param mixed $params
     *
     * @return mixed returned value
     */
    public function __call($method, $params)
    {
        $module = null;
        if (preg_match('/^([a-z]+)([A-Z])(.*)$/', $method, $match)
            && isset($this->options['modules'][$match[1]])
        ) {
            $module = $this->options['modules'][$match[1]];
            $method = strtolower($match[2]) . $match[3];
            if (!isset($this->modules[$module]) || !is_object($this->modules[$module])) {
                $result = $this->loadModule($module);
                if (MDB2::isError($result)) {
                    return $result;
                }
            }
        } else {
            foreach ($this->modules as $key => $foo) {
                if (is_object($this->modules[$key])
                    && method_exists($this->modules[$key], $method)
                ) {
                    $module = $key;
                    break;
                }
            }
        }
        if (null !== $module) {
            return call_user_func_array([&$this->modules[$module], $method], $params);
        }
        trigger_error(sprintf('Call to undefined function: %s::%s().', static::class, $method), E_USER_ERROR);
    }

    /**
     * Start a transaction or set a savepoint.
     *
     * @param   string  name of a savepoint to set
     * @param mixed|null $savepoint
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function beginTransaction($savepoint = null)
    {
        $this->debug('Starting transaction', __FUNCTION__, ['is_manip' => true, 'savepoint' => $savepoint]);

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'transactions are not supported',
            __FUNCTION__
        );
    }

    /**
     * Commit the database changes done during a transaction that is in
     * progress or release a savepoint. This function may only be called when
     * auto-committing is disabled, otherwise it will fail. Therefore, a new
     * transaction is implicitly started after committing the pending changes.
     *
     * @param   string  name of a savepoint to release
     * @param mixed|null $savepoint
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function commit($savepoint = null)
    {
        $this->debug('Committing transaction/savepoint', __FUNCTION__, ['is_manip' => true, 'savepoint' => $savepoint]);

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'commiting transactions is not supported',
            __FUNCTION__
        );
    }

    /**
     * Cancel any database changes done during a transaction or since a specific
     * savepoint that is in progress. This function may only be called when
     * auto-committing is disabled, otherwise it will fail. Therefore, a new
     * transaction is implicitly started after canceling the pending changes.
     *
     * @param   string  name of a savepoint to rollback to
     * @param mixed|null $savepoint
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function rollback($savepoint = null)
    {
        $this->debug('Rolling back transaction/savepoint', __FUNCTION__, ['is_manip' => true, 'savepoint' => $savepoint]);

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'rolling back transactions is not supported',
            __FUNCTION__
        );
    }

    /**
     * If a transaction is currently open.
     *
     * @param   bool    if the nested transaction count should be ignored
     * @param mixed $ignore_nested
     *
     * @return bool|int - an integer with the nesting depth is returned if a
     *                  nested transaction is open
     *                  - true is returned for a normal open transaction
     *                  - false is returned if no transaction is open
     */
    public function inTransaction($ignore_nested = false)
    {
        if (!$ignore_nested && isset($this->nested_transaction_counter)) {
            return $this->nested_transaction_counter;
        }

        return $this->in_transaction;
    }

    /**
     * Set the transacton isolation level.
     *
     * @param   string  standard isolation level
     *                  READ UNCOMMITTED (allows dirty reads)
     *                  READ COMMITTED (prevents dirty reads)
     *                  REPEATABLE READ (prevents nonrepeatable reads)
     *                  SERIALIZABLE (prevents phantom reads)
     * @param   array some transaction options:
     *                  'wait' => 'WAIT' | 'NO WAIT'
     *                  'rw'   => 'READ WRITE' | 'READ ONLY'
     * @param mixed $isolation
     * @param mixed $options
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     *
     * @since   2.1.1
     */
    public function setTransactionIsolation($isolation, $options = [])
    {
        $this->debug('Setting transaction isolation level', __FUNCTION__, ['is_manip' => true]);

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'isolation level setting is not supported',
            __FUNCTION__
        );
    }

    /**
     * Start a nested transaction.
     *
     * @return mixed MDB2_OK on success/savepoint name, a MDB2 error on failure
     *
     * @since   2.1.1
     */
    public function beginNestedTransaction()
    {
        if ($this->in_transaction) {
            ++$this->nested_transaction_counter;
            $savepoint = sprintf($this->options['savepoint_format'], $this->nested_transaction_counter);
            if ($this->supports('savepoints') && $savepoint) {
                return $this->beginTransaction($savepoint);
            }

            return MDB2_OK;
        }
        $this->has_transaction_error = false;
        $result = $this->beginTransaction();
        $this->nested_transaction_counter = 1;

        return $result;
    }

    /**
     * Finish a nested transaction by rolling back if an error occured or
     * committing otherwise.
     *
     * @param   bool    if the transaction should be rolled back regardless
     *                  even if no error was set within the nested transaction
     * @param mixed $force_rollback
     *
     * @return mixed MDB_OK on commit/counter decrementing, false on rollback
     *               and a MDB2 error on failure
     *
     * @since   2.1.1
     */
    public function completeNestedTransaction($force_rollback = false)
    {
        if ($this->nested_transaction_counter > 1) {
            $savepoint = sprintf($this->options['savepoint_format'], $this->nested_transaction_counter);
            if ($this->supports('savepoints') && $savepoint) {
                if ($force_rollback || $this->has_transaction_error) {
                    $result = $this->rollback($savepoint);
                    if (!MDB2::isError($result)) {
                        $result = false;
                        $this->has_transaction_error = false;
                    }
                } else {
                    $result = $this->commit($savepoint);
                }
            } else {
                $result = MDB2_OK;
            }
            --$this->nested_transaction_counter;

            return $result;
        }

        $this->nested_transaction_counter = null;
        $result = MDB2_OK;

        // transaction has not yet been rolled back
        if ($this->in_transaction) {
            if ($force_rollback || $this->has_transaction_error) {
                $result = $this->rollback();
                if (!MDB2::isError($result)) {
                    $result = false;
                }
            } else {
                $result = $this->commit();
            }
        }
        $this->has_transaction_error = false;

        return $result;
    }

    /**
     * Force setting nested transaction to failed.
     *
     * @param   mixed   value to return in getNestededTransactionError()
     * @param   bool    if the transaction should be rolled back immediately
     * @param mixed|null $error
     * @param mixed      $immediately
     *
     * @return bool MDB2_OK
     *
     * @since   2.1.1
     */
    public function failNestedTransaction($error = null, $immediately = false)
    {
        if (null !== $error) {
            $error = $this->has_transaction_error ?: true;
        } elseif (!$error) {
            $error = true;
        }
        $this->has_transaction_error = $error;
        if (!$immediately) {
            return MDB2_OK;
        }

        return $this->rollback();
    }

    /**
     * The first error that occured since the transaction start.
     *
     * @return bool|MDB2_Error MDB2 error object if an error occured or false
     *
     * @since   2.1.1
     */
    public function getNestedTransactionError()
    {
        return $this->has_transaction_error;
    }

    /**
     * Connect to the database.
     *
     * @return true on success, MDB2 Error Object on failure
     */
    public function connect()
    {
        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * check if given database name is exists?
     *
     * @param string $name name of the database that should be checked
     *
     * @return mixed true/false on success, a MDB2 error on failure
     */
    public function databaseExists($name)
    {
        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * Set the charset on the current connection.
     *
     * @param string    charset
     * @param resource  connection handle
     * @param mixed      $charset
     * @param mixed|null $connection
     *
     * @return true on success, MDB2 Error Object on failure
     */
    public function setCharset($charset, $connection = null)
    {
        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * Log out and disconnect from the database.
     *
     * @param bool $force whether the disconnect should be forced even if the
     *                    connection is opened persistently
     *
     * @return mixed true on success, false if not connected and error object on error
     */
    public function disconnect($force = true)
    {
        $this->connection = 0;
        $this->connected_dsn = [];
        $this->connected_database_name = '';
        $this->opened_persistent = null;
        $this->connected_server_info = '';
        $this->in_transaction = null;
        $this->nested_transaction_counter = null;

        return MDB2_OK;
    }

    /**
     * Select a different database.
     *
     * @param   string  name of the database that should be selected
     * @param mixed $name
     *
     * @return string name of the database previously connected to
     */
    public function setDatabase($name)
    {
        $previous_database_name = $this->database_name ?? '';
        $this->database_name = $name;
        if (!empty($this->connected_database_name) && ($this->connected_database_name != $this->database_name)) {
            $this->disconnect(false);
        }

        return $previous_database_name;
    }

    /**
     * Get the current database.
     *
     * @return string name of the database
     */
    public function getDatabase()
    {
        return $this->database_name;
    }

    /**
     * set the DSN.
     *
     * @param   mixed   DSN string or array
     * @param mixed $dsn
     *
     * @return MDB2_OK
     */
    public function setDSN($dsn)
    {
        $dsn_default = $GLOBALS['_MDB2_dsninfo_default'];
        $dsn = MDB2::parseDSN($dsn);
        if (array_key_exists('database', $dsn)) {
            $this->database_name = $dsn['database'];
            unset($dsn['database']);
        }
        $this->dsn = array_merge($dsn_default, $dsn);

        return $this->disconnect(false);
    }

    /**
     * return the DSN as a string.
     *
     * @param   string  format to return ("array", "string")
     * @param   string  string to hide the password with
     * @param mixed $type
     * @param mixed $hidepw
     *
     * @return mixed DSN in the chosen type
     */
    public function getDSN($type = 'string', $hidepw = false)
    {
        $dsn = array_merge($GLOBALS['_MDB2_dsninfo_default'], $this->dsn);
        $dsn['phptype'] = $this->phptype;
        $dsn['database'] = $this->database_name;
        if ($hidepw) {
            $dsn['password'] = $hidepw;
        }
        switch ($type) {
            // expand to include all possible options
            case 'string':
                $dsn = $dsn['phptype'] .
                    ($dsn['dbsyntax'] ? ('(' . $dsn['dbsyntax'] . ')') : '') .
                    '://' . $dsn['username'] . ':' .
                     $dsn['password'] . '@' . $dsn['hostspec'] .
                     ($dsn['port'] ? (':' . $dsn['port']) : '') .
                     '/' . $dsn['database'];
                break;

            case 'array':
            default:
                break;
        }

        return $dsn;
    }

    /**
     * Check if the 'new_link' option is set.
     *
     * @return bool
     */
    protected function isNewLinkSet()
    {
        return isset($this->dsn['new_link'])
            && (
                $this->dsn['new_link'] === true
             || (is_string($this->dsn['new_link']) && preg_match('/^true$/i', $this->dsn['new_link']))
             || (is_numeric($this->dsn['new_link']) && 0 != (int) $this->dsn['new_link'])
            );
    }

    /**
     * execute a query as database administrator.
     *
     * @param   string  the SQL query
     * @param   mixed   array that contains the types of the columns in
     *                        the result set
     * @param   bool    if the query is a manipulation query
     * @param mixed      $query
     * @param mixed|null $types
     * @param mixed      $is_manip
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function standaloneQuery($query, $types = null, $is_manip = false)
    {
        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $query = $this->modifyQuery($query, $is_manip, $limit, $offset);

        $connection = $this->getConnection();
        if (MDB2::isError($connection)) {
            return $connection;
        }

        $result = $this->doQuery($query, $is_manip, $connection, false);
        if (MDB2::isError($result)) {
            return $result;
        }

        if ($is_manip) {
            return $this->affectedRows($connection, $result);
        }

        return $this->wrapResult($result, $types, true, true, $limit, $offset);
    }

    /**
     * Changes a query string for various DBMS specific reasons.
     *
     * @param   string  query to modify
     * @param   bool    if it is a DML query
     * @param   int  limit the number of rows
     * @param   int  start reading from given offset
     * @param mixed $query
     * @param mixed $is_manip
     * @param mixed $limit
     * @param mixed $offset
     *
     * @return string modified query
     */
    protected function modifyQuery($query, $is_manip, $limit, $offset)
    {
        return $query;
    }

    /**
     * Execute a query.
     *
     * @param   string  query
     * @param   bool    if the query is a manipulation query
     * @param   resource connection handle
     * @param   string  database name
     * @param mixed      $query
     * @param mixed      $is_manip
     * @param mixed|null $connection
     * @param mixed|null $database_name
     *
     * @return result or error object
     */
    protected function doQuery($query, $is_manip = false, $connection = null, $database_name = null)
    {
        $this->last_query = $query;
        $result = $this->debug($query, 'query', ['is_manip' => $is_manip, 'when' => 'pre']);
        if ($result) {
            if (MDB2::isError($result)) {
                return $result;
            }
            $query = $result;
        }

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * Returns the number of rows affected.
     *
     * @param   resource result handle
     * @param   resource connection handle
     * @param mixed      $connection
     * @param mixed|null $result
     *
     * @return mixed MDB2 Error Object or the number of rows affected
     */
    protected function affectedRows($connection, $result = null)
    {
        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * Execute a manipulation query to the database and return the number of affected rows.
     *
     * @param   string  the SQL query
     * @param mixed $query
     *
     * @return mixed number of affected rows on success, a MDB2 error on failure
     */
    public function exec($query)
    {
        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $query = $this->modifyQuery($query, true, $limit, $offset);

        $connection = $this->getConnection();
        if (MDB2::isError($connection)) {
            return $connection;
        }

        $result = $this->doQuery($query, true, $connection, $this->database_name);
        if (MDB2::isError($result)) {
            return $result;
        }

        return $this->affectedRows($connection, $result);
    }

    /**
     * Send a query to the database and return any results.
     *
     * @param   string  the SQL query
     * @param   mixed   array that contains the types of the columns in
     *                        the result set
     * @param   mixed   string which specifies which result class to use
     * @param   mixed   string which specifies which class to wrap results in
     * @param mixed      $query
     * @param mixed|null $types
     * @param mixed      $result_class
     * @param mixed      $result_wrap_class
     *
     * @return mixed an MDB2_Result handle on success, a MDB2 error on failure
     */
    public function query($query, $types = null, $result_class = true, $result_wrap_class = true)
    {
        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $query = $this->modifyQuery($query, false, $limit, $offset);

        $connection = $this->getConnection();
        if (MDB2::isError($connection)) {
            return $connection;
        }

        $result = $this->doQuery($query, false, $connection, $this->database_name);
        if (MDB2::isError($result)) {
            return $result;
        }

        return $this->wrapResult($result, $types, $result_class, $result_wrap_class, $limit, $offset);
    }

    /**
     * wrap a result set into the correct class.
     *
     * @param   resource result handle
     * @param   mixed   array that contains the types of the columns in
     *                        the result set
     * @param   mixed   string which specifies which result class to use
     * @param   mixed   string which specifies which class to wrap results in
     * @param   string  number of rows to select
     * @param   string  first row to select
     * @param mixed      $result_resource
     * @param mixed      $types
     * @param mixed      $result_class
     * @param mixed      $result_wrap_class
     * @param mixed|null $limit
     * @param mixed|null $offset
     *
     * @return mixed an MDB2_Result, a MDB2 error on failure
     */
    protected function wrapResult(
        $result_resource,
        $types = [],
        $result_class = true,
        $result_wrap_class = true,
        $limit = null,
        $offset = null
    ) {
        if ($types === true) {
            if ($this->supports('result_introspection')) {
                $this->loadModule('Reverse', null, true);
                $tableInfo = $this->reverse->tableInfo($result_resource);
                if (MDB2::isError($tableInfo)) {
                    return $tableInfo;
                }
                $types = [];
                $types_assoc = [];
                foreach ($tableInfo as $field) {
                    $types[] = $field['mdb2type'];
                    $types_assoc[$field['name']] = $field['mdb2type'];
                }
            } else {
                $types = null;
            }
        }

        if ($result_class === true) {
            $result_class = $this->options['result_buffering']
                ? $this->options['buffered_result_class'] : $this->options['result_class'];
        }

        if ($result_class) {
            $class_name = sprintf($result_class, $this->phptype);
            if (!class_exists($class_name)) {
                return $this->raiseError(
                    MDB2_ERROR_NOT_FOUND,
                    null,
                    null,
                    'result class does not exist ' . $class_name,
                    __FUNCTION__
                );
            }
            $result = new $class_name($this, $result_resource, $limit, $offset);
            if (!MDB2::isResultCommon($result)) {
                return $this->raiseError(
                    MDB2_ERROR_NOT_FOUND,
                    null,
                    null,
                    'result class is not extended from MDB2_Result_Common',
                    __FUNCTION__
                );
            }

            if (!empty($types)) {
                $err = $result->setResultTypes($types);
                if (MDB2::isError($err)) {
                    $result->free();

                    return $err;
                }
            }
            if (!empty($types_assoc)) {
                $err = $result->setResultTypes($types_assoc);
                if (MDB2::isError($err)) {
                    $result->free();

                    return $err;
                }
            }

            if ($result_wrap_class === true) {
                $result_wrap_class = $this->options['result_wrap_class'];
            }
            if ($result_wrap_class) {
                if (!class_exists($result_wrap_class)) {
                    return $this->raiseError(
                        MDB2_ERROR_NOT_FOUND,
                        null,
                        null,
                        'result wrap class does not exist ' . $result_wrap_class,
                        __FUNCTION__
                    );
                }
                $result = new $result_wrap_class($result, $this->fetchmode);
            }

            return $result;
        }

        return $result_resource;
    }

    /**
     * return version information about the server.
     *
     * @param   bool    determines if the raw version string should be returned
     * @param mixed $native
     *
     * @return mixed array with version information or row string
     */
    public function getServerVersion($native = false)
    {
        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * set the range of the next query.
     *
     * @param   string  number of rows to select
     * @param   string  first row to select
     * @param mixed      $limit
     * @param mixed|null $offset
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function setLimit($limit, $offset = null)
    {
        if (!$this->supports('limit_queries')) {
            return $this->raiseError(
                MDB2_ERROR_UNSUPPORTED,
                null,
                null,
                'limit is not supported by this driver',
                __FUNCTION__
            );
        }
        $limit = (int) $limit;
        if ($limit < 0) {
            return $this->raiseError(
                MDB2_ERROR_SYNTAX,
                null,
                null,
                'it was not specified a valid selected range row limit',
                __FUNCTION__
            );
        }
        $this->limit = $limit;
        if (null !== $offset) {
            $offset = (int) $offset;
            if ($offset < 0) {
                return $this->raiseError(
                    MDB2_ERROR_SYNTAX,
                    null,
                    null,
                    'it was not specified a valid first selected range row',
                    __FUNCTION__
                );
            }
            $this->offset = $offset;
        }

        return MDB2_OK;
    }

    /**
     * simple subselect emulation: leaves the query untouched for all RDBMS
     * that support subselects.
     *
     * @param   string  the SQL query for the subselect that may only
     *                      return a column
     * @param   string  determines type of the field
     * @param mixed $query
     * @param mixed $type
     *
     * @return string the query
     */
    public function subSelect($query, $type = false)
    {
        if ($this->supports('sub_selects') === true) {
            return $query;
        }

        if (!$this->supports('sub_selects')) {
            return $this->raiseError(
                MDB2_ERROR_UNSUPPORTED,
                null,
                null,
                'method not implemented',
                __FUNCTION__
            );
        }

        $col = $this->queryCol($query, $type);
        if (MDB2::isError($col)) {
            return $col;
        }
        if (!is_array($col) || count($col) == 0) {
            return 'NULL';
        }
        if ($type) {
            $this->loadModule('Datatype', null, true);

            return $this->datatype->implodeArray($col, $type);
        }

        return implode(', ', $col);
    }

    /**
     * Execute a SQL REPLACE query. A REPLACE query is identical to a INSERT
     * query, except that if there is already a row in the table with the same
     * key field values, the old row is deleted before the new row is inserted.
     *
     * The REPLACE type of query does not make part of the SQL standards. Since
     * practically only MySQL and SQLite implement it natively, this type of
     * query isemulated through this method for other DBMS using standard types
     * of queries inside a transaction to assure the atomicity of the operation.
     *
     * @param   string  name of the table on which the REPLACE query will
     *       be executed
     * @param   array   associative array   that describes the fields and the
     *       values that will be inserted or updated in the specified table. The
     *       indexes of the array are the names of all the fields of the table.
     *       The values of the array are also associative arrays that describe
     *       the values and other properties of the table fields.
     *
     *       Here follows a list of field properties that need to be specified:
     *
     *       value
     *           Value to be assigned to the specified field. This value may be
     *           of specified in database independent type format as this
     *           function can perform the necessary datatype conversions.
     *
     *           Default: this property is required unless the Null property is
     *           set to 1.
     *
     *       type
     *           Name of the type of the field. Currently, all types MDB2
     *           are supported except for clob and blob.
     *
     *           Default: no type conversion
     *
     *       null
     *           bool    property that indicates that the value for this field
     *           should be set to null.
     *
     *           The default value for fields missing in INSERT queries may be
     *           specified the definition of a table. Often, the default value
     *           is already null, but since the REPLACE may be emulated using
     *           an UPDATE query, make sure that all fields of the table are
     *           listed in this function argument array.
     *
     *           Default: 0
     *
     *       key
     *           bool    property that indicates that this field should be
     *           handled as a primary key or at least as part of the compound
     *           unique index of the table that will determine the row that will
     *           updated if it exists or inserted a new row otherwise.
     *
     *           This function will fail if no key field is specified or if the
     *           value of a key field is set to null because fields that are
     *           part of unique index they may not be null.
     *
     *           Default: 0
     * @param mixed $table
     * @param mixed $fields
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function replace($table, $fields)
    {
        if (!$this->supports('replace')) {
            return $this->raiseError(
                MDB2_ERROR_UNSUPPORTED,
                null,
                null,
                'replace query is not supported',
                __FUNCTION__
            );
        }
        $count = count($fields);
        $condition = $values = [];
        for ($colnum = 0, reset($fields); $colnum < $count; next($fields), $colnum++) {
            $name = key($fields);
            if (isset($fields[$name]['null']) && $fields[$name]['null']) {
                $value = 'NULL';
            } else {
                $type = $fields[$name]['type'] ?? null;
                $value = $this->quote($fields[$name]['value'], $type);
            }
            $values[$name] = $value;
            if (isset($fields[$name]['key']) && $fields[$name]['key']) {
                if ($value === 'NULL') {
                    return $this->raiseError(
                        MDB2_ERROR_CANNOT_REPLACE,
                        null,
                        null,
                        'key value ' . $name . ' may not be NULL',
                        __FUNCTION__
                    );
                }
                $condition[] = $this->quoteIdentifier($name, true) . '=' . $value;
            }
        }
        if (empty($condition)) {
            return $this->raiseError(
                MDB2_ERROR_CANNOT_REPLACE,
                null,
                null,
                'not specified which fields are keys',
                __FUNCTION__
            );
        }

        $result = null;
        $in_transaction = $this->in_transaction;
        if (!$in_transaction && MDB2::isError($result = $this->beginTransaction())) {
            return $result;
        }

        $connection = $this->getConnection();
        if (MDB2::isError($connection)) {
            return $connection;
        }

        $condition = ' WHERE ' . implode(' AND ', $condition);
        $query = 'DELETE FROM ' . $this->quoteIdentifier($table, true) . $condition;
        $result = $this->doQuery($query, true, $connection);
        if (!MDB2::isError($result)) {
            $affected_rows = $this->affectedRows($connection, $result);
            $insert = '';
            foreach ($values as $key => $value) {
                $insert .= ($insert ? ', ' : '') . $this->quoteIdentifier($key, true);
            }
            $values = implode(', ', $values);
            $query = 'INSERT INTO ' . $this->quoteIdentifier($table, true) . "({$insert}) VALUES ({$values})";
            $result = $this->doQuery($query, true, $connection);
            if (!MDB2::isError($result)) {
                $affected_rows += $this->affectedRows($connection, $result);
            }
        }

        if (!$in_transaction) {
            if (MDB2::isError($result)) {
                $this->rollback();
            } else {
                $result = $this->commit();
            }
        }

        if (MDB2::isError($result)) {
            return $result;
        }

        return $affected_rows;
    }

    /**
     * Prepares a query for multiple execution with execute().
     * With some database backends, this is emulated.
     * prepare() requires a generic query as string like
     * 'INSERT INTO numbers VALUES(?,?)' or
     * 'INSERT INTO numbers VALUES(:foo,:bar)'.
     * The ? and :name and are placeholders which can be set using
     * bindParam() and the query can be sent off using the execute() method.
     * The allowed format for :name can be set with the 'bindname_format' option.
     *
     * @param   string  the query to prepare
     * @param   mixed   array that contains the types of the placeholders
     * @param   mixed   array that contains the types of the columns in
     *                        the result set or MDB2_PREPARE_RESULT, if set to
     *                        MDB2_PREPARE_MANIP the query is handled as a manipulation query
     * @param   mixed   key (field) value (parameter) pair for all lob placeholders
     * @param mixed      $query
     * @param mixed|null $types
     * @param mixed|null $result_types
     * @param mixed      $lobs
     *
     * @return mixed resource handle for the prepared query on success,
     *               a MDB2 error on failure
     *
     * @see     bindParam, execute
     */
    public function prepare($query, $types = null, $result_types = null, $lobs = [])
    {
        $is_manip = ($result_types === MDB2_PREPARE_MANIP);
        $offset = $this->offset;
        $limit = $this->limit;
        $this->offset = $this->limit = 0;
        $result = $this->debug($query, __FUNCTION__, ['is_manip' => $is_manip, 'when' => 'pre']);
        if ($result) {
            if (MDB2::isError($result)) {
                return $result;
            }
            $query = $result;
        }
        $placeholder_type_guess = $placeholder_type = null;
        $question = '?';
        $colon = ':';
        $positions = [];
        $position = 0;
        while ($position < strlen($query)) {
            $q_position = strpos($query, (string) $question, $position);
            $c_position = strpos($query, (string) $colon, $position);
            if ($q_position && $c_position) {
                $p_position = min($q_position, $c_position);
            } elseif ($q_position) {
                $p_position = $q_position;
            } elseif ($c_position) {
                $p_position = $c_position;
            } else {
                break;
            }
            if (null === $placeholder_type) {
                $placeholder_type_guess = $query[$p_position];
            }

            $new_pos = $this->skipDelimitedStrings($query, $position, $p_position);
            if (MDB2::isError($new_pos)) {
                return $new_pos;
            }
            if ($new_pos != $position) {
                $position = $new_pos;

                continue; // evaluate again starting from the new position
            }

            if ($query[$position] == $placeholder_type_guess) {
                if (null === $placeholder_type) {
                    $placeholder_type = $query[$p_position];
                    $question = $colon = $placeholder_type;
                    if (!empty($types) && is_array($types)) {
                        if ($placeholder_type == ':') {
                            if (is_int(key($types))) {
                                $types_tmp = $types;
                                $types = [];
                                $count = -1;
                            }
                        } else {
                            $types = array_values($types);
                        }
                    }
                }
                if ($placeholder_type == ':') {
                    $regexp = '/^.{' . ($position + 1) . '}(' . $this->options['bindname_format'] . ').*$/s';
                    $parameter = preg_replace($regexp, '\1', $query);
                    if ($parameter === '') {
                        return $this->raiseError(
                            MDB2_ERROR_SYNTAX,
                            null,
                            null,
                            'named parameter name must match "bindname_format" option',
                            __FUNCTION__
                        );
                    }
                    $positions[$p_position] = $parameter;
                    $query = substr_replace($query, '?', $position, strlen($parameter) + 1);
                    // use parameter name in type array
                    if (isset($count, $types_tmp[++$count])) {
                        $types[$parameter] = $types_tmp[$count];
                    }
                } else {
                    $positions[$p_position] = count($positions);
                }
                $position = $p_position + 1;
            } else {
                $position = $p_position;
            }
        }
        $class_name = 'MDB2_Statement_' . $this->phptype;
        $statement = null;
        $obj = new $class_name($this, $statement, $positions, $query, $types, $result_types, $is_manip, $limit, $offset);
        $this->debug($query, __FUNCTION__, ['is_manip' => $is_manip, 'when' => 'post', 'result' => $obj]);

        return $obj;
    }

    /**
     * Utility method, used by prepare() to avoid replacing placeholders within delimited strings.
     * Check if the placeholder is contained within a delimited string.
     * If so, skip it and advance the position, otherwise return the current position,
     * which is valid.
     *
     * @param string $query
     * @param int    $position   current string cursor position
     * @param int    $p_position placeholder position
     *
     * @return mixed integer $new_position on success
     *               MDB2_Error on failure
     */
    protected function skipDelimitedStrings($query, $position, $p_position)
    {
        $ignores = [];
        $ignores[] = $this->string_quoting;
        $ignores[] = $this->identifier_quoting;
        $ignores = array_merge($ignores, $this->sql_comments);

        foreach ($ignores as $ignore) {
            if (!empty($ignore['start'])) {
                if (is_int($start_quote = strpos($query, (string) $ignore['start'], $position)) && $start_quote < $p_position) {
                    $end_quote = $start_quote;
                    do {
                        if (!is_int($end_quote = strpos($query, (string) $ignore['end'], $end_quote + 1))) {
                            if ($ignore['end'] === "\n") {
                                $end_quote = strlen($query) - 1;
                            } else {
                                return $this->raiseError(
                                    MDB2_ERROR_SYNTAX,
                                    null,
                                    null,
                                    'query with an unterminated text string specified',
                                    __FUNCTION__
                                );
                            }
                        }
                    } while ($ignore['escape']
                        && $end_quote - 1 != $start_quote
                        && $query[$end_quote - 1] == $ignore['escape']
                        && ($ignore['escape_pattern'] !== $ignore['escape']
                            || $query[$end_quote - 2] != $ignore['escape'])
                    );

                    return $end_quote + 1;
                }
            }
        }

        return $position;
    }

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param   string  text string value that is intended to be converted
     * @param   string  type to which the value should be converted to
     * @param   bool    quote
     * @param   bool    escape wildcards
     * @param mixed      $value
     * @param mixed|null $type
     * @param mixed      $quote
     * @param mixed      $escape_wildcards
     *
     * @return string text string that represents the given argument value in
     *                a DBMS specific format
     */
    public function quote($value, $type = null, $quote = true, $escape_wildcards = false)
    {
        $result = $this->loadModule('Datatype', null, true);
        if (MDB2::isError($result)) {
            return $result;
        }

        return $this->datatype->quote($value, $type, $quote, $escape_wildcards);
    }

    /**
     * Obtain DBMS specific SQL code portion needed to declare
     * of the given type.
     *
     * @param   string  type to which the value should be converted to
     * @param   string  name the field to be declared
     * @param   string  definition of the field
     * @param mixed $type
     * @param mixed $name
     * @param mixed $field
     *
     * @return string DBMS specific SQL code portion that should be used to
     *                declare the specified field
     */
    public function getDeclaration($type, $name, $field)
    {
        $result = $this->loadModule('Datatype', null, true);
        if (MDB2::isError($result)) {
            return $result;
        }

        return $this->datatype->getDeclaration($type, $name, $field);
    }

    /**
     * Obtain an array of changes that may need to applied.
     *
     * @param   array   new definition
     * @param   array   old definition
     * @param mixed $current
     * @param mixed $previous
     *
     * @return array containing all changes that will need to be applied
     */
    public function compareDefinition($current, $previous)
    {
        $result = $this->loadModule('Datatype', null, true);
        if (MDB2::isError($result)) {
            return $result;
        }

        return $this->datatype->compareDefinition($current, $previous);
    }

    /**
     * Tell whether a DB implementation or its backend extension
     * supports a given feature.
     *
     * @param   string  name of the feature (see the MDB2 class doc)
     * @param mixed $feature
     *
     * @return bool|string if this DB implementation supports a given feature
     *                     false means no, true means native,
     *                     'emulated' means emulated
     */
    public function supports($feature)
    {
        if (array_key_exists($feature, $this->supported)) {
            return $this->supported[$feature];
        }

        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            "unknown support feature {$feature}",
            __FUNCTION__
        );
    }

    /**
     * adds sequence name formatting to a sequence name.
     *
     * @param   string  name of the sequence
     * @param mixed $sqn
     *
     * @return string formatted sequence name
     */
    public function getSequenceName($sqn)
    {
        return sprintf(
            $this->options['seqname_format'],
            preg_replace('/[^a-z0-9_\-\$.]/i', '_', $sqn)
        );
    }

    /**
     * adds index name formatting to a index name.
     *
     * @param   string  name of the index
     * @param mixed $idx
     *
     * @return string formatted index name
     */
    public function getIndexName($idx)
    {
        return sprintf(
            $this->options['idxname_format'],
            preg_replace('/[^a-z0-9_\-\$.]/i', '_', $idx)
        );
    }

    /**
     * Returns the next free id of a sequence.
     *
     * @param   string  name of the sequence
     * @param   bool    when true missing sequences are automatic created
     * @param mixed $seq_name
     * @param mixed $ondemand
     *
     * @return mixed MDB2 Error Object or id
     */
    public function nextID($seq_name, $ondemand = true)
    {
        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * Returns the autoincrement ID if supported or $id or fetches the current
     * ID in a sequence called: $table.(empty($field) ? '' : '_'.$field).
     *
     * @param   string  name of the table into which a new row was inserted
     * @param   string  name of the field into which a new row was inserted
     * @param mixed|null $table
     * @param mixed|null $field
     *
     * @return mixed MDB2 Error Object or id
     */
    public function lastInsertID($table = null, $field = null)
    {
        return $this->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    /**
     * Returns the current id of a sequence.
     *
     * @param   string  name of the sequence
     * @param mixed $seq_name
     *
     * @return mixed MDB2 Error Object or id
     */
    public function currID($seq_name)
    {
        $this->warnings[] = 'database does not support getting current
            sequence value, the sequence value was incremented';

        return $this->nextID($seq_name);
    }

    /**
     * Execute the specified query, fetch the value from the first column of
     * the first row of the result set and then frees
     * the result set.
     *
     * @param string $query  the SELECT query statement to be executed
     * @param string $type   optional argument that specifies the expected
     *                       datatype of the result set field, so that an eventual
     *                       conversion may be performed. The default datatype is
     *                       text, meaning that no conversion is performed
     * @param mixed  $colnum the column number (or name) to fetch
     *
     * @return mixed MDB2_OK or field value on success, a MDB2 error on failure
     */
    public function queryOne($query, $type = null, $colnum = 0)
    {
        $result = $this->query($query, $type);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $one = $result->fetchOne($colnum);
        $result->free();

        return $one;
    }

    /**
     * Execute the specified query, fetch the values from the first
     * row of the result set into an array and then frees
     * the result set.
     *
     * @param   string  the SELECT query statement to be executed
     * @param   array   optional array argument that specifies a list of
     *       expected datatypes of the result set columns, so that the eventual
     *       conversions may be performed. The default list of datatypes is
     *       empty, meaning that no conversion is performed.
     * @param   int     how the array data should be indexed
     * @param mixed      $query
     * @param mixed|null $types
     * @param mixed      $fetchmode
     *
     * @return mixed MDB2_OK or data array on success, a MDB2 error on failure
     */
    public function queryRow($query, $types = null, $fetchmode = MDB2_FETCHMODE_DEFAULT)
    {
        $result = $this->query($query, $types);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $row = $result->fetchRow($fetchmode);
        $result->free();

        return $row;
    }

    /**
     * Execute the specified query, fetch the value from the first column of
     * each row of the result set into an array and then frees the result set.
     *
     * @param string $query  the SELECT query statement to be executed
     * @param string $type   optional argument that specifies the expected
     *                       datatype of the result set field, so that an eventual
     *                       conversion may be performed. The default datatype is text,
     *                       meaning that no conversion is performed
     * @param mixed  $colnum the column number (or name) to fetch
     *
     * @return mixed MDB2_OK or data array on success, a MDB2 error on failure
     */
    public function queryCol($query, $type = null, $colnum = 0)
    {
        $result = $this->query($query, $type);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $col = $result->fetchCol($colnum);
        $result->free();

        return $col;
    }

    /**
     * Execute the specified query, fetch all the rows of the result set into
     * a two dimensional array and then frees the result set.
     *
     * @param   string  the SELECT query statement to be executed
     * @param   array   optional array argument that specifies a list of
     *       expected datatypes of the result set columns, so that the eventual
     *       conversions may be performed. The default list of datatypes is
     *       empty, meaning that no conversion is performed.
     * @param   int     how the array data should be indexed
     * @param   bool    if set to true, the $all will have the first
     *       column as its first dimension
     * @param   bool    used only when the query returns exactly
     *       two columns. If true, the values of the returned array will be
     *       one-element arrays instead of scalars.
     * @param   bool    if true, the values of the returned array is
     *       wrapped in another array.  If the same key value (in the first
     *       column) repeats itself, the values will be appended to this array
     *       instead of overwriting the existing values.
     * @param mixed      $query
     * @param mixed|null $types
     * @param mixed      $fetchmode
     * @param mixed      $rekey
     * @param mixed      $force_array
     * @param mixed      $group
     *
     * @return mixed MDB2_OK or data array on success, a MDB2 error on failure
     */
    public function queryAll(
        $query,
        $types = null,
        $fetchmode = MDB2_FETCHMODE_DEFAULT,
        $rekey = false,
        $force_array = false,
        $group = false
    ) {
        $result = $this->query($query, $types);
        if (!MDB2::isResultCommon($result)) {
            return $result;
        }

        $all = $result->fetchAll($fetchmode, $rekey, $force_array, $group);
        $result->free();

        return $all;
    }

    /**
     * This method deletes all occurences of the specified element from
     * the expected error codes stack.
     *
     * @param mixed $error_code error code that should be deleted
     *
     * @return mixed list of error codes that were deleted or error
     *
     * @uses PEAR::delExpect()
     */
    public function delExpect($error_code)
    {
        return $this->pear->delExpect($error_code);
    }

    /**
     * This method is used to tell which errors you expect to get.
     * Expected errors are always returned with error mode
     * PEAR_ERROR_RETURN.  Expected error codes are stored in a stack,
     * and this method pushes a new element onto it.  The list of
     * expected errors are in effect until they are popped off the
     * stack with the popExpect() method.
     *
     * Note that this method can not be called statically
     *
     * @param mixed $code a single error code or an array of error codes to expect
     *
     * @return int the new depth of the "expected errors" stack
     *
     * @uses PEAR::expectError()
     */
    public function expectError($code = '*')
    {
        return $this->pear->expectError($code);
    }

    /**
     * If you have a class that's mostly/entirely static, and you need static
     * properties, you can use this method to simulate them. Eg. in your method(s)
     * do this: $myVar = &PEAR::getStaticProperty('myclass', 'myVar');
     * You MUST use a reference, or they will not persist!
     *
     * @param string $class The calling classname, to prevent clashes
     * @param string $var   the variable to retrieve
     *
     * @return mixed A reference to the variable. If not set it will be
     *               auto initialised to NULL.
     *
     * @uses PEAR::getStaticProperty()
     */
    public function &getStaticProperty($class, $var)
    {
        $tmp = &$this->pear->getStaticProperty($class, $var);

        return $tmp;
    }

    /**
     * Pop the last error handler used.
     *
     * @return bool Always true
     *
     * @see PEAR::pushErrorHandling
     *
     * @uses PEAR::popErrorHandling()
     */
    public function popErrorHandling()
    {
        return $this->pear->popErrorHandling();
    }

    /**
     * This method pops one element off the expected error codes
     * stack.
     *
     * @return array the list of error codes that were popped
     *
     * @uses PEAR::popExpect()
     */
    public function popExpect()
    {
        return $this->pear->popExpect();
    }

    /**
     * Push a new error handler on top of the error handler options stack. With this
     * you can easily override the actual error handler for some code and restore
     * it later with popErrorHandling.
     *
     * @param mixed $mode    (same as setErrorHandling)
     * @param mixed $options (same as setErrorHandling)
     *
     * @return bool Always true
     *
     * @see PEAR::setErrorHandling
     *
     * @uses PEAR::pushErrorHandling()
     */
    public function pushErrorHandling($mode, $options = null)
    {
        return $this->pear->pushErrorHandling($mode, $options);
    }

    /**
     * Use this function to register a shutdown method for static
     * classes.
     *
     * @param mixed $func The function name (or array of class/method) to call
     * @param mixed $args The arguments to pass to the function
     *
     * @uses PEAR::registerShutdownFunc()
     */
    public function registerShutdownFunc($func, $args = [])
    {
        return $this->pear->registerShutdownFunc($func, $args);
    }

    /**
     * Sets how errors generated by this object should be handled.
     * Can be invoked both in objects and statically.  If called
     * statically, setErrorHandling sets the default behaviour for all
     * PEAR objects.  If called in an object, setErrorHandling sets
     * the default behaviour for that object.
     *
     * @param int   $mode
     *                       One of PEAR_ERROR_RETURN, PEAR_ERROR_PRINT,
     *                       PEAR_ERROR_TRIGGER, PEAR_ERROR_DIE,
     *                       PEAR_ERROR_CALLBACK or PEAR_ERROR_EXCEPTION
     * @param mixed $options
     *                       When $mode is PEAR_ERROR_TRIGGER, this is the error level (one
     *                       of E_USER_NOTICE, E_USER_WARNING or E_USER_ERROR).
     *
     *        When $mode is PEAR_ERROR_CALLBACK, this parameter is expected
     *        to be the callback function or method.  A callback
     *        function is a string with the name of the function, a
     *        callback method is an array of two elements: the element
     *        at index 0 is the object, and the element at index 1 is
     *        the name of the method to call in the object.
     *
     *        When $mode is PEAR_ERROR_PRINT or PEAR_ERROR_DIE, this is
     *        a printf format string used when printing the error
     *        message.
     *
     * @see PEAR_ERROR_RETURN
     * @see PEAR_ERROR_PRINT
     * @see PEAR_ERROR_TRIGGER
     * @see PEAR_ERROR_DIE
     * @see PEAR_ERROR_CALLBACK
     * @see PEAR_ERROR_EXCEPTION
     * @since PHP 4.0.5
     *
     * @uses PEAR::setErrorHandling($mode, $options)
     */
    public function setErrorHandling($mode = null, $options = null)
    {
        return $this->pear->setErrorHandling($mode, $options);
    }

    /**
     * @uses PEAR::staticPopErrorHandling()
     */
    public function staticPopErrorHandling()
    {
        return $this->pear->staticPopErrorHandling();
    }

    /**
     * @uses PEAR::staticPushErrorHandling($mode, $options)
     *
     * @param mixed      $mode
     * @param mixed|null $options
     */
    public function staticPushErrorHandling($mode, $options = null)
    {
        return $this->pear->staticPushErrorHandling($mode, $options);
    }

    /**
     * Simpler form of raiseError with fewer options.  In most cases
     * message, code and userinfo are enough.
     *
     * @param mixed  $message  a text error message or a PEAR error object
     * @param int    $code     a numeric error code (it is up to your class
     *                         to define these if you want to use codes)
     * @param string $userinfo if you need to pass along for example debug
     *                         information, this parameter is meant for that
     *
     * @return object a PEAR error object
     *
     * @see PEAR::raiseError
     *
     * @uses PEAR::&throwError()
     */
    public function throwError($message = null, $code = null, $userinfo = null)
    {
        return $this->pear->throwError($message, $code, $userinfo);
    }
}
