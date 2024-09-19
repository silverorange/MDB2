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
 * The common statement class for MDB2 statement objects.
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2_Statement_Common
{
    // {{{ Variables (Properties)

    public $db;
    public $statement;
    public $query;
    public $result_types;
    public $types;
    public $values = [];
    public $limit;
    public $offset;
    public $is_manip;

    // }}}
    // {{{ constructor: function __construct($db, $statement, $positions, $query, $types, $result_types, $is_manip = false, $limit = null, $offset = null)

    /**
     * Constructor.
     *
     * @param mixed      $db
     * @param mixed      $statement
     * @param mixed      $positions
     * @param mixed      $query
     * @param mixed      $types
     * @param mixed      $result_types
     * @param mixed      $is_manip
     * @param mixed|null $limit
     * @param mixed|null $offset
     */
    public function __construct($db, $statement, $positions, $query, $types, $result_types, $is_manip = false, $limit = null, $offset = null)
    {
        $this->db = $db;
        $this->statement = $statement;
        $this->positions = $positions;
        $this->query = $query;
        $this->types = (array) $types;
        $this->result_types = (array) $result_types;
        $this->limit = $limit;
        $this->is_manip = $is_manip;
        $this->offset = $offset;
    }

    // }}}
    // {{{ function bindValue($parameter, &$value, $type = null)

    /**
     * Set the value of a parameter of a prepared query.
     *
     * @param   int     the order number of the parameter in the query
     *       statement. The order number of the first parameter is 1.
     * @param   mixed   value that is meant to be assigned to specified
     *       parameter. The type of the value depends on the $type argument.
     * @param   string  specifies the type of the field
     * @param mixed $parameter
     * @param mixed $value
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function bindValue($parameter, $value, $type = null)
    {
        if (!is_numeric($parameter)) {
            if (strpos($parameter, ':') === 0) {
                $parameter = substr($parameter, 1);
            }
        }
        if (!in_array($parameter, $this->positions)) {
            return MDB2::raiseError(
                MDB2_ERROR_NOT_FOUND,
                null,
                null,
                'Unable to bind to missing placeholder: ' . $parameter,
                __FUNCTION__
            );
        }
        $this->values[$parameter] = $value;
        if (null !== $type) {
            $this->types[$parameter] = $type;
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ function bindValueArray($values, $types = null)

    /**
     * Set the values of multiple a parameter of a prepared query in bulk.
     *
     * @param   array   specifies all necessary information
     *       for bindValue() the array elements must use keys corresponding to
     *       the number of the position of the parameter
     * @param   array   specifies the types of the fields
     * @param mixed      $values
     * @param mixed|null $types
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     *
     * @see     bindParam()
     */
    public function bindValueArray($values, $types = null)
    {
        $types = is_array($types) ? array_values($types) : array_fill(0, count($values), null);
        $parameters = array_keys($values);
        $this->db->pushErrorHandling(PEAR_ERROR_RETURN);
        $this->db->expectError(MDB2_ERROR_NOT_FOUND);
        foreach ($parameters as $key => $parameter) {
            $err = $this->bindValue($parameter, $values[$parameter], $types[$key]);
            if (MDB2::isError($err)) {
                if ($err->getCode() == MDB2_ERROR_NOT_FOUND) {
                    // ignore (extra value for missing placeholder)
                    continue;
                }
                $this->db->popExpect();
                $this->db->popErrorHandling();

                return $err;
            }
        }
        $this->db->popExpect();
        $this->db->popErrorHandling();

        return MDB2_OK;
    }

    // }}}
    // {{{ function bindParam($parameter, &$value, $type = null)

    /**
     * Bind a variable to a parameter of a prepared query.
     *
     * @param   int     the order number of the parameter in the query
     *       statement. The order number of the first parameter is 1.
     * @param   mixed   variable that is meant to be bound to specified
     *       parameter. The type of the value depends on the $type argument.
     * @param   string  specifies the type of the field
     * @param mixed $parameter
     * @param mixed $value
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function bindParam($parameter, &$value, $type = null)
    {
        if (!is_numeric($parameter)) {
            if (strpos($parameter, ':') === 0) {
                $parameter = substr($parameter, 1);
            }
        }
        if (!in_array($parameter, $this->positions)) {
            return MDB2::raiseError(
                MDB2_ERROR_NOT_FOUND,
                null,
                null,
                'Unable to bind to missing placeholder: ' . $parameter,
                __FUNCTION__
            );
        }
        $this->values[$parameter] = &$value;
        if (null !== $type) {
            $this->types[$parameter] = $type;
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ function bindParamArray(&$values, $types = null)

    /**
     * Bind the variables of multiple a parameter of a prepared query in bulk.
     *
     * @param   array   specifies all necessary information
     *       for bindParam() the array elements must use keys corresponding to
     *       the number of the position of the parameter
     * @param   array   specifies the types of the fields
     * @param mixed      $values
     * @param mixed|null $types
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     *
     * @see     bindParam()
     */
    public function bindParamArray(&$values, $types = null)
    {
        $types = is_array($types) ? array_values($types) : array_fill(0, count($values), null);
        $parameters = array_keys($values);
        foreach ($parameters as $key => $parameter) {
            $err = $this->bindParam($parameter, $values[$parameter], $types[$key]);
            if (MDB2::isError($err)) {
                return $err;
            }
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ function execute($values = null, $result_class = true, $result_wrap_class = false)

    /**
     * Execute a prepared query statement.
     *
     * @param array specifies all necessary information
     *              for bindParam() the array elements must use keys corresponding
     *              to the number of the position of the parameter
     * @param mixed specifies which result class to use
     * @param mixed specifies which class to wrap results in
     * @param mixed|null $values
     * @param mixed      $result_class
     * @param mixed      $result_wrap_class
     *
     * @return mixed MDB2_Result or integer (affected rows) on success,
     *               a MDB2 error on failure
     */
    public function execute($values = null, $result_class = true, $result_wrap_class = false)
    {
        if (null === $this->positions) {
            return MDB2::raiseError(
                MDB2_ERROR,
                null,
                null,
                'Prepared statement has already been freed',
                __FUNCTION__
            );
        }

        $values = (array) $values;
        if (!empty($values)) {
            $err = $this->bindValueArray($values);
            if (MDB2::isError($err)) {
                return MDB2::raiseError(
                    MDB2_ERROR,
                    null,
                    null,
                    'Binding Values failed with message: ' . $err->getMessage(),
                    __FUNCTION__
                );
            }
        }

        return $this->executeInternal($result_class, $result_wrap_class);
    }

    // }}}
    // {{{ function executeInternal($result_class = true, $result_wrap_class = false)

    /**
     * Execute a prepared query statement helper method.
     *
     * @param   mixed   specifies which result class to use
     * @param   mixed   specifies which class to wrap results in
     * @param mixed $result_class
     * @param mixed $result_wrap_class
     *
     * @return mixed MDB2_Result or integer (affected rows) on success,
     *               a MDB2 error on failure
     */
    protected function executeInternal($result_class = true, $result_wrap_class = false)
    {
        $this->last_query = $this->query;
        $query = '';
        $last_position = 0;
        foreach ($this->positions as $current_position => $parameter) {
            if (!array_key_exists($parameter, $this->values)) {
                return MDB2::raiseError(
                    MDB2_ERROR_NOT_FOUND,
                    null,
                    null,
                    'Unable to bind to missing placeholder: ' . $parameter,
                    __FUNCTION__
                );
            }
            $value = $this->values[$parameter];
            $query .= substr($this->query, $last_position, $current_position - $last_position);
            if (!isset($value)) {
                $value_quoted = 'NULL';
            } else {
                $type = !empty($this->types[$parameter]) ? $this->types[$parameter] : null;
                $value_quoted = $this->db->quote($value, $type);
                if (MDB2::isError($value_quoted)) {
                    return $value_quoted;
                }
            }
            $query .= $value_quoted;
            $last_position = $current_position + 1;
        }
        $query .= substr($this->query, $last_position);

        $this->db->offset = $this->offset;
        $this->db->limit = $this->limit;
        if ($this->is_manip) {
            $result = $this->db->exec($query);
        } else {
            $result = $this->db->query($query, $this->result_types, $result_class, $result_wrap_class);
        }

        return $result;
    }

    // }}}
    // {{{ function free()

    /**
     * Release resources allocated for the specified prepared query.
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function free()
    {
        if (null === $this->positions) {
            return MDB2::raiseError(
                MDB2_ERROR,
                null,
                null,
                'Prepared statement has already been freed',
                __FUNCTION__
            );
        }

        $this->statement = null;
        $this->positions = null;
        $this->query = null;
        $this->types = null;
        $this->result_types = null;
        $this->limit = null;
        $this->is_manip = null;
        $this->offset = null;
        $this->values = null;

        return MDB2_OK;
    }

    // }}}
}
