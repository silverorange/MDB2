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
 * The common result class for MDB2 result objects.
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2_Result_Common extends MDB2_Result
{
    // {{{ Variables (Properties)

    public $db;
    public $result;
    public $rownum = -1;
    public $types = [];
    public $types_assoc = [];
    public $values = [];
    public $offset;
    public $offset_count = 0;
    public $limit;
    public $column_names;

    // }}}
    // {{{ constructor: function __construct($db, &$result, $limit = 0, $offset = 0)

    /**
     * Constructor.
     *
     * @param mixed $db
     * @param mixed $result
     * @param mixed $limit
     * @param mixed $offset
     */
    public function __construct($db, &$result, $limit = 0, $offset = 0)
    {
        $this->db = $db;
        $this->result = $result;
        $this->offset = $offset;
        $this->limit = max(0, $limit - 1);
    }

    // }}}
    // {{{ function setResultTypes($types)

    /**
     * Define the list of types to be associated with the columns of a given
     * result set.
     *
     * This function may be called before invoking fetchRow(), fetchOne(),
     * fetchCol() and fetchAll() so that the necessary data type
     * conversions are performed on the data to be retrieved by them. If this
     * function is not called, the type of all result set columns is assumed
     * to be text, thus leading to not perform any conversions.
     *
     * @param   array   variable that lists the
     *       data types to be expected in the result set columns. If this array
     *       contains less types than the number of columns that are returned
     *       in the result set, the remaining columns are assumed to be of the
     *       type text. Currently, the types clob and blob are not fully
     *       supported.
     * @param mixed $types
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function setResultTypes($types)
    {
        $load = $this->db->loadModule('Datatype', null, true);
        if (MDB2::isError($load)) {
            return $load;
        }
        $types = $this->db->datatype->checkResultTypes($types);
        if (MDB2::isError($types)) {
            return $types;
        }
        foreach ($types as $key => $value) {
            if (is_numeric($key)) {
                $this->types[$key] = $value;
            } else {
                $this->types_assoc[$key] = $value;
            }
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ function seek($rownum = 0)

    /**
     * Seek to a specific row in a result set.
     *
     * @param   int     number of the row where the data can be found
     * @param mixed $rownum
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function seek($rownum = 0)
    {
        $target_rownum = $rownum - 1;
        if ($this->rownum > $target_rownum) {
            return MDB2::raiseError(
                MDB2_ERROR_UNSUPPORTED,
                null,
                null,
                'seeking to previous rows not implemented',
                __FUNCTION__
            );
        }
        while ($this->rownum < $target_rownum) {
            $this->fetchRow();
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ function fetchRow($fetchmode = MDB2_FETCHMODE_DEFAULT, $rownum = null)

    /**
     * Fetch and return a row of data.
     *
     * @param   int     how the array data should be indexed
     * @param   int     number of the row where the data can be found
     * @param mixed      $fetchmode
     * @param mixed|null $rownum
     *
     * @return int data array on success, a MDB2 error on failure
     */
    public function fetchRow($fetchmode = MDB2_FETCHMODE_DEFAULT, $rownum = null)
    {
        return MDB2::raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
    // {{{ function fetchOne($colnum = 0)

    /**
     * fetch single column from the next row from a result set.
     *
     * @param int|string the column number (or name) to fetch
     * @param int        number of the row where the data can be found
     * @param mixed      $colnum
     * @param mixed|null $rownum
     *
     * @return string data on success, a MDB2 error on failure
     */
    public function fetchOne($colnum = 0, $rownum = null)
    {
        $fetchmode = is_numeric($colnum) ? MDB2_FETCHMODE_ORDERED : MDB2_FETCHMODE_ASSOC;
        $row = $this->fetchRow($fetchmode, $rownum);
        if (!is_array($row) || MDB2::isError($row)) {
            return $row;
        }
        if (!array_key_exists($colnum, $row)) {
            return MDB2::raiseError(
                MDB2_ERROR_TRUNCATED,
                null,
                null,
                'column is not defined in the result set: ' . $colnum,
                __FUNCTION__
            );
        }

        return $row[$colnum];
    }

    // }}}
    // {{{ function fetchCol($colnum = 0)

    /**
     * Fetch and return a column from the current row pointer position.
     *
     * @param int|string the column number (or name) to fetch
     * @param mixed $colnum
     *
     * @return mixed data array on success, a MDB2 error on failure
     */
    public function fetchCol($colnum = 0)
    {
        $column = [];
        $fetchmode = is_numeric($colnum) ? MDB2_FETCHMODE_ORDERED : MDB2_FETCHMODE_ASSOC;
        $row = $this->fetchRow($fetchmode);
        if (is_array($row)) {
            if (!array_key_exists($colnum, $row)) {
                return MDB2::raiseError(
                    MDB2_ERROR_TRUNCATED,
                    null,
                    null,
                    'column is not defined in the result set: ' . $colnum,
                    __FUNCTION__
                );
            }
            do {
                $column[] = $row[$colnum];
            } while (is_array($row = $this->fetchRow($fetchmode)));
        }
        if (MDB2::isError($row)) {
            return $row;
        }

        return $column;
    }

    // }}}
    // {{{ function fetchAll($fetchmode = MDB2_FETCHMODE_DEFAULT, $rekey = false, $force_array = false, $group = false)

    /**
     * Fetch and return all rows from the current row pointer position.
     *
     * @param int $fetchmode the fetch mode to use:
     *                       + MDB2_FETCHMODE_ORDERED
     *                       + MDB2_FETCHMODE_ASSOC
     *                       + MDB2_FETCHMODE_ORDERED | MDB2_FETCHMODE_FLIPPED
     *                       + MDB2_FETCHMODE_ASSOC | MDB2_FETCHMODE_FLIPPED
     * @param   bool    if set to true, the $all will have the first
     *       column as its first dimension
     * @param   bool    used only when the query returns exactly
     *       two columns. If true, the values of the returned array will be
     *       one-element arrays instead of scalars.
     * @param   bool    if true, the values of the returned array is
     *       wrapped in another array.  If the same key value (in the first
     *       column) repeats itself, the values will be appended to this array
     *       instead of overwriting the existing values.
     * @param mixed $rekey
     * @param mixed $force_array
     * @param mixed $group
     *
     * @return mixed data array on success, a MDB2 error on failure
     *
     * @see     getAssoc()
     */
    public function fetchAll(
        $fetchmode = MDB2_FETCHMODE_DEFAULT,
        $rekey = false,
        $force_array = false,
        $group = false
    ) {
        $all = [];
        $row = $this->fetchRow($fetchmode);
        if (MDB2::isError($row)) {
            return $row;
        }
        if (!$row) {
            return $all;
        }

        $shift_array = $rekey ? false : null;
        if (null !== $shift_array) {
            if (is_object($row)) {
                $colnum = count(get_object_vars($row));
            } else {
                $colnum = count($row);
            }
            if ($colnum < 2) {
                return MDB2::raiseError(
                    MDB2_ERROR_TRUNCATED,
                    null,
                    null,
                    'rekey feature requires atleast 2 column',
                    __FUNCTION__
                );
            }
            $shift_array = (!$force_array && $colnum == 2);
        }

        if ($rekey) {
            do {
                if (is_object($row)) {
                    $arr = get_object_vars($row);
                    $key = reset($arr);
                    unset($row->{$key});
                } else {
                    if ($fetchmode == MDB2_FETCHMODE_ASSOC
                        || $fetchmode == MDB2_FETCHMODE_OBJECT
                    ) {
                        $key = reset($row);
                        unset($row[key($row)]);
                    } else {
                        $key = array_shift($row);
                    }
                    if ($shift_array) {
                        $row = array_shift($row);
                    }
                }
                if ($group) {
                    $all[$key][] = $row;
                } else {
                    $all[$key] = $row;
                }
            } while ($row = $this->fetchRow($fetchmode));
        } elseif ($fetchmode == MDB2_FETCHMODE_FLIPPED) {
            do {
                foreach ($row as $key => $val) {
                    $all[$key][] = $val;
                }
            } while ($row = $this->fetchRow($fetchmode));
        } else {
            do {
                $all[] = $row;
            } while ($row = $this->fetchRow($fetchmode));
        }

        return $all;
    }

    // }}}
    // {{{ function rowCount()

    /**
     * Returns the actual row number that was last fetched (count from 0).
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->rownum + 1;
    }

    // }}}
    // {{{ function numRows()

    /**
     * Returns the number of rows in a result object.
     *
     * @return mixed MDB2 Error Object or the number of rows
     */
    public function numRows()
    {
        return MDB2::raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
    // {{{ function nextResult()

    /**
     * Move the internal result pointer to the next available result.
     *
     * @return true on success, false if there is no more result set or an error object on failure
     */
    public function nextResult()
    {
        return MDB2::raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
    // {{{ function getColumnNames()

    /**
     * Retrieve the names of columns returned by the DBMS in a query result or
     * from the cache.
     *
     * @param   bool    if set to true the values are the column names,
     *                  otherwise the names of the columns are the keys
     * @param mixed $flip
     *
     * @return mixed Array variable that holds the names of columns or an
     *               MDB2 error on failure.
     *               Some DBMS may not return any columns when the result set
     *               does not contain any rows.
     */
    public function getColumnNames($flip = false)
    {
        if (!isset($this->column_names)) {
            $result = $this->getColumnNamesInternal();
            if (MDB2::isError($result)) {
                return $result;
            }
            $this->column_names = $result;
        }
        if ($flip) {
            return array_flip($this->column_names);
        }

        return $this->column_names;
    }

    // }}}
    // {{{ function getColumnNamesInternal()

    /**
     * Retrieve the names of columns returned by the DBMS in a query result.
     *
     * @return mixed Array variable that holds the names of columns as keys
     *               or an MDB2 error on failure.
     *               Some DBMS may not return any columns when the result set
     *               does not contain any rows.
     */
    protected function getColumnNamesInternal()
    {
        return MDB2::raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
    // {{{ function numCols()

    /**
     * Count the number of columns returned by the DBMS in a query result.
     *
     * @return mixed integer value with the number of columns, a MDB2 error
     *               on failure
     */
    public function numCols()
    {
        return MDB2::raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implemented',
            __FUNCTION__
        );
    }

    // }}}
    // {{{ function getResource()

    /**
     * return the resource associated with the result object.
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->result;
    }

    // }}}
    // {{{ function bindColumn($column, &$value, $type = null)

    /**
     * Set bind variable to a column.
     *
     * @param   int     column number or name
     * @param   mixed   variable reference
     * @param   string  specifies the type of the field
     * @param mixed      $column
     * @param mixed      $value
     * @param mixed|null $type
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function bindColumn($column, &$value, $type = null)
    {
        if (!is_numeric($column)) {
            $column_names = $this->getColumnNames();
            if ($this->db->options['portability'] & MDB2_PORTABILITY_FIX_CASE) {
                if ($this->db->options['field_case'] == CASE_LOWER) {
                    $column = strtolower($column);
                } else {
                    $column = strtoupper($column);
                }
            }
            $column = $column_names[$column];
        }
        $this->values[$column] = &$value;
        if (null !== $type) {
            $this->types[$column] = $type;
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ function assignBindColumns($row)

    /**
     * Bind a variable to a value in the result row.
     *
     * @param   array   row data
     * @param mixed $row
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    protected function assignBindColumns($row)
    {
        $row = array_values($row);
        foreach ($row as $column => $value) {
            if (array_key_exists($column, $this->values)) {
                $this->values[$column] = $value;
            }
        }

        return MDB2_OK;
    }

    // }}}
    // {{{ function free()

    /**
     * Free the internal resources associated with result.
     *
     * @return bool true on success, false if result is invalid
     */
    public function free()
    {
        $this->result = false;

        return MDB2_OK;
    }

    // }}}
}
