<?php

/**
 * +----------------------------------------------------------------------+
 * | PHP version 5                                                        |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1998-2008 Manuel Lemos, Tomas V.V.Cox,                 |
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
 * | Authors: Lukas Smith <smith@pooteeweet.org>                          |
 * |          Lorenzo Alberton <l.alberton@quipo.it>                      |
 * +----------------------------------------------------------------------+.
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @author   Lorenzo Alberton <l.alberton@quipo.it>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */

/**
 * Base class for the management modules that is extended by each MDB2 driver.
 *
 * To load this module in the MDB2 object:
 * $mdb->loadModule('Manager');
 *
 * @category Database
 *
 * @author   Lukas Smith <smith@pooteeweet.org>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2_Driver_Manager_Common extends MDB2_Module_Common
{
    /**
     * Split the "[owner|schema].table" notation into an array.
     *
     * @param string $table [schema and] table name
     *
     * @return array array(schema, table)
     */
    public function splitTableSchema($table)
    {
        $ret = [];
        if (str_contains($table, '.')) {
            return explode('.', $table);
        }

        return [null, $table];
    }

    /**
     * Get declaration of a number of field in bulk.
     *
     * @param array $fields a multidimensional associative array.
     *                      The first dimension determines the field name, while the second
     *                      dimension is keyed with the name of the properties
     *                      of the field being declared as array indexes. Currently, the types
     *                      of supported field properties are as follows:
     *
     *      default
     *          Boolean value to be used as default for this field.
     *
     *      notnull
     *          Boolean flag that indicates whether this field is constrained
     *          to not be set to null.
     *
     * @return mixed string on success, a MDB2 error on failure
     */
    public function getFieldDeclarationList($fields)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        if (!is_array($fields) || empty($fields)) {
            return $db->raiseError(
                MDB2_ERROR_NEED_MORE_DATA,
                null,
                null,
                'missing any fields',
                __FUNCTION__
            );
        }
        foreach ($fields as $field_name => $field) {
            $query = $db->getDeclaration($field['type'], $field_name, $field);
            if (MDB2::isError($query)) {
                return $query;
            }
            $query_fields[] = $query;
        }

        return implode(', ', $query_fields);
    }

    /**
     * Removes any formatting in an sequence name using the 'seqname_format' option.
     *
     * @param string $sqn   string that containts name of a potential sequence
     * @param bool   $check if only formatted sequences should be returned
     *
     * @return string name of the sequence with possible formatting removed
     */
    protected function fixSequenceName($sqn, $check = false)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        $seq_pattern = '/^' . preg_replace('/%s/', '([a-z0-9_]+)', $db->options['seqname_format']) . '$/i';
        $seq_name = preg_replace($seq_pattern, '\1', $sqn);
        if ($seq_name && !strcasecmp($sqn, $db->getSequenceName($seq_name))) {
            return $seq_name;
        }
        if ($check) {
            return false;
        }

        return $sqn;
    }

    /**
     * Removes any formatting in an index name using the 'idxname_format' option.
     *
     * @param string $idx string that containts name of anl index
     *
     * @return string name of the index with eventual formatting removed
     */
    protected function fixIndexName($idx)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        $idx_pattern = '/^' . preg_replace('/%s/', '([a-z0-9_]+)', $db->options['idxname_format']) . '$/i';
        $idx_name = preg_replace($idx_pattern, '\1', $idx);
        if ($idx_name && !strcasecmp($idx, $db->getIndexName($idx_name))) {
            return $idx_name;
        }

        return $idx;
    }

    /**
     * create a new database.
     *
     * @param array $options  array with charset, collation info
     * @param mixed $database
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function createDatabase($database, $options = [])
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

    /**
     * alter an existing database.
     *
     * @param array $options  array with charset, collation info
     * @param mixed $database
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function alterDatabase($database, $options = [])
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

    /**
     * drop an existing database.
     *
     * @param mixed $database
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function dropDatabase($database)
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

    /**
     * Create a basic SQL query for a new table creation.
     *
     * @param string $name    Name of the database that should be created
     * @param array  $fields  Associative array that contains the definition of each field of the new table
     * @param array  $options An associative array of table options
     *
     * @return mixed string (the SQL query) on success, a MDB2 error on failure
     *
     * @see createTable()
     */
    protected function getCreateTableQuery($name, $fields, $options = [])
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        if (!$name) {
            return $db->raiseError(
                MDB2_ERROR_CANNOT_CREATE,
                null,
                null,
                'no valid table name specified',
                __FUNCTION__
            );
        }
        if (empty($fields)) {
            return $db->raiseError(
                MDB2_ERROR_CANNOT_CREATE,
                null,
                null,
                'no fields specified for table "' . $name . '"',
                __FUNCTION__
            );
        }
        $query_fields = $this->getFieldDeclarationList($fields);
        if (MDB2::isError($query_fields)) {
            return $query_fields;
        }
        if (!empty($options['primary'])) {
            $query_fields .= ', PRIMARY KEY (' . implode(', ', array_keys($options['primary'])) . ')';
        }

        $name = $db->quoteIdentifier($name, true);
        $result = 'CREATE ';
        if (!empty($options['temporary'])) {
            $result .= $this->getTemporaryTableQuery();
        }
        $result .= " TABLE {$name} ({$query_fields})";

        return $result;
    }

    /**
     * A method to return the required SQL string that fits between CREATE ... TABLE
     * to create the table as a temporary table.
     *
     * Should be overridden in driver classes to return the correct string for the
     * specific database type.
     *
     * The default is to return the string "TEMPORARY" - this will result in a
     * SQL error for any database that does not support temporary tables, or that
     * requires a different SQL command from "CREATE TEMPORARY TABLE".
     *
     * @return string the string required to be placed between "CREATE" and "TABLE"
     *                to generate a temporary table, if possible
     */
    protected function getTemporaryTableQuery()
    {
        return 'TEMPORARY';
    }

    /**
     * create a new table.
     *
     * @param string $name    Name of the database that should be created
     * @param array  $fields  Associative array that contains the definition of each field of the new table
     *                        The indexes of the array entries are the names of the fields of the table an
     *                        the array entry values are associative arrays like those that are meant to be
     *                        passed with the field definitions to get[Type]Declaration() functions.
     *                        array(
     *                        'id' => array(
     *                        'type' => 'integer',
     *                        'unsigned' => 1
     *                        'notnull' => 1
     *                        'default' => 0
     *                        ),
     *                        'name' => array(
     *                        'type' => 'text',
     *                        'length' => 12
     *                        ),
     *                        'password' => array(
     *                        'type' => 'text',
     *                        'length' => 12
     *                        )
     *                        );
     * @param array  $options An associative array of table options:
     *                        array(
     *                        'comment' => 'Foo',
     *                        'temporary' => true|false,
     *                        );
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function createTable($name, $fields, $options = [])
    {
        $query = $this->getCreateTableQuery($name, $fields, $options);
        if (MDB2::isError($query)) {
            return $query;
        }
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }
        $result = $db->exec($query);
        if (MDB2::isError($result)) {
            return $result;
        }

        return MDB2_OK;
    }

    /**
     * drop an existing table.
     *
     * @param string $name name of the table that should be dropped
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function dropTable($name)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        $name = $db->quoteIdentifier($name, true);
        $result = $db->exec("DROP TABLE {$name}");
        if (MDB2::isError($result)) {
            return $result;
        }

        return MDB2_OK;
    }

    /**
     * Truncate an existing table (if the TRUNCATE TABLE syntax is not supported,
     * it falls back to a DELETE FROM TABLE query).
     *
     * @param string $name name of the table that should be truncated
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function truncateTable($name)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        $name = $db->quoteIdentifier($name, true);
        $result = $db->exec("DELETE FROM {$name}");
        if (MDB2::isError($result)) {
            return $result;
        }

        return MDB2_OK;
    }

    /**
     * Optimize (vacuum) all the tables in the db (or only the specified table)
     * and optionally run ANALYZE.
     *
     * @param string $table   table name (all the tables if empty)
     * @param array  $options an array with driver-specific options:
     *                        - timeout [int] (in seconds) [mssql-only]
     *                        - analyze [boolean] [pgsql and mysql]
     *                        - full [boolean] [pgsql-only]
     *                        - freeze [boolean] [pgsql-only]
     *
     * @return mixed MDB2_OK success, a MDB2 error on failure
     */
    public function vacuum($table = null, $options = [])
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

    /**
     * alter an existing table.
     *
     * @param string $name    name of the table that is intended to be changed
     * @param array  $changes associative array that contains the details of each type
     *                        of change that is intended to be performed. The types of
     *                        changes that are currently supported are defined as follows:
     *
     *                          name
     *
     *                              New name for the table.
     *
     *                          add
     *
     *                              Associative array with the names of fields to be added as
     *                              indexes of the array. The value of each entry of the array
     *                              should be set to another associative array with the properties
     *                              of the fields to be added. The properties of the fields should
     *                              be the same as defined by the MDB2 parser.
     *
     *                          remove
     *
     *                              Associative array with the names of fields to be removed as indexes
     *                              of the array. Currently, the values assigned to each entry are ignored.
     *                              An empty array should be used for future compatibility.
     *
     *                          rename
     *
     *                              Associative array with the names of fields to be renamed as indexes
     *                              of the array. The value of each entry of the array should be set to
     *                              another associative array with the entry named name with the new
     *                              field name and the entry named Declaration that is expected to contain
     *                              the portion of the field declaration already in DBMS specific SQL code
     *                              as it is used in the CREATE TABLE statement.
     *
     *                          change
     *
     *                              Associative array with the names of the fields to be changed as indexes
     *                              of the array. Keep in mind that if it is intended to change either the
     *                              name of a field and any other properties, the change array entries
     *                              should have the new names of the fields as array indexes.
     *
     *                              The value of each entry of the array should be set to another associative
     *                              array with the properties of the fields to that are meant to be changed as
     *                              array entries. These entries should be assigned to the new values of the
     *                              respective properties. The properties of the fields should be the same
     *                              as defined by the MDB2 parser.
     *
     *                          Example
     *                              [
     *                                'name' => 'userlist',
     *                                'add' => [
     *                                  'quota' => [
     *                                    'type' => 'integer',
     *                                    'unsigned' => 1
     *                                  ],
     *                                ],
     *                                'remove' => [
     *                                  'file_limit' => [],
     *                                  'time_limit' => [],
     *                                ],
     *                                'change' => [
     *                                  'name' => [
     *                                    'length' => '20',
     *                                    'definition' => [
     *                                      'type' => 'text',
     *                                      'length' => 20,
     *                                    ],
     *                                  ],
     *                                ],
     *                                'rename' => [
     *                                  'sex' => [
     *                                    'name' => 'gender',
     *                                    'definition' => [
     *                                      'type' => 'text',
     *                                      'length' => 1,
     *                                      'default' => 'M',
     *                                    ],
     *                                  ],
     *                                ],
     *                              ]
     * @param bool $check indicates whether the function should just check if the DBMS driver
     *                    can perform the requested table alterations if the value is true or
     *                    actually perform them otherwise
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function alterTable($name, $changes, $check)
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

    /**
     * list all databases.
     *
     * @return mixed array of database names on success, a MDB2 error on failure
     */
    public function listDatabases()
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        return $db->raiseError(
            MDB2_ERROR_UNSUPPORTED,
            null,
            null,
            'method not implementedd',
            __FUNCTION__
        );
    }

    /**
     * list all users.
     *
     * @return mixed array of user names on success, a MDB2 error on failure
     */
    public function listUsers()
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

    /**
     * list all views in the current database.
     *
     * @param string database, the current is default
     *               NB: not all the drivers can get the view names from
     *               a database other than the current one
     * @param mixed|null $database
     *
     * @return mixed array of view names on success, a MDB2 error on failure
     */
    public function listViews($database = null)
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

    /**
     * list the views in the database that reference a given table.
     *
     * @param string table for which all referenced views should be found
     * @param mixed $table
     *
     * @return mixed array of view names on success, a MDB2 error on failure
     */
    public function listTableViews($table)
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

    /**
     * list all triggers in the database that reference a given table.
     *
     * @param string table for which all referenced triggers should be found
     * @param mixed|null $table
     *
     * @return mixed array of trigger names on success, a MDB2 error on failure
     */
    public function listTableTriggers($table = null)
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

    /**
     * list all functions in the current database.
     *
     * @return mixed array of function names on success, a MDB2 error on failure
     */
    public function listFunctions()
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

    /**
     * list all tables in the current database.
     *
     * @param string database, the current is default.
     *               NB: not all the drivers can get the table names from
     *               a database other than the current one
     * @param mixed|null $database
     *
     * @return mixed array of table names on success, a MDB2 error on failure
     */
    public function listTables($database = null)
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

    /**
     * list all fields in a table in the current database.
     *
     * @param string $table name of table that should be used in method
     *
     * @return mixed array of field names on success, a MDB2 error on failure
     */
    public function listTableFields($table)
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

    /**
     * Get the stucture of a field into an array.
     *
     * @param string $table      name of the table on which the index is to be created
     * @param string $name       name of the index to be created
     * @param array  $definition associative array that defines properties of the index to be created.
     *                           Currently, only one property named FIELDS is supported. This property
     *                           is also an associative with the names of the index fields as array
     *                           indexes. Each entry of this array is set to another type of associative
     *                           array that specifies properties of the index that are specific to
     *                           each field.
     *
     *                                Currently, only the sorting property is supported. It should be used
     *                                 to define the sorting direction of the index. It may be set to either
     *                                 ascending or descending.
     *
     *                                Not all DBMS support index sorting direction configuration. The DBMS
     *                                 drivers of those that do not support it ignore this property. Use the
     *                                 public function supports() to determine whether the DBMS driver can manage indexes.
     *
     *                                 Example
     *                                    array(
     *                                        'fields' => array(
     *                                            'user_name' => array(
     *                                                'sorting' => 'ascending'
     *                                            ),
     *                                            'last_login' => array()
     *                                        )
     *                                    )
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function createIndex($table, $name, $definition)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        $table = $db->quoteIdentifier($table, true);
        $name = $db->quoteIdentifier($db->getIndexName($name), true);
        $query = "CREATE INDEX {$name} ON {$table}";
        $fields = [];
        foreach (array_keys($definition['fields']) as $field) {
            $fields[] = $db->quoteIdentifier($field, true);
        }
        $query .= ' (' . implode(', ', $fields) . ')';
        $result = $db->exec($query);
        if (MDB2::isError($result)) {
            return $result;
        }

        return MDB2_OK;
    }

    /**
     * drop existing index.
     *
     * @param string $table name of table that should be used in method
     * @param string $name  name of the index to be dropped
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function dropIndex($table, $name)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        $name = $db->quoteIdentifier($db->getIndexName($name), true);
        $result = $db->exec("DROP INDEX {$name}");
        if (MDB2::isError($result)) {
            return $result;
        }

        return MDB2_OK;
    }

    /**
     * list all indexes in a table.
     *
     * @param string $table name of table that should be used in method
     *
     * @return mixed array of index names on success, a MDB2 error on failure
     */
    public function listTableIndexes($table)
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

    /**
     * Return the FOREIGN KEY query section dealing with non-standard options
     * as MATCH, INITIALLY DEFERRED, ON UPDATE, ...
     *
     * @param array $definition
     *
     * @return string
     */
    protected function getAdvancedFKOptions($definition)
    {
        return '';
    }

    /**
     * create a constraint on a table.
     *
     * @param string $table      name of the table on which the constraint is to be created
     * @param string $name       name of the constraint to be created
     * @param array  $definition associative array that defines properties of the constraint to be created.
     *                           The full structure of the array looks like this:
     *                           <pre>
     *                           array (
     *                           [primary] => 0
     *                           [unique]  => 0
     *                           [foreign] => 1
     *                           [check]   => 0
     *                           [fields] => array (
     *                           [field1name] => array() // one entry per each field covered
     *                           [field2name] => array() // by the index
     *                           [field3name] => array(
     *                           [sorting]  => ascending
     *                           [position] => 3
     *                           )
     *                           )
     *                           [references] => array(
     *                           [table] => name
     *                           [fields] => array(
     *                           [field1name] => array(  //one entry per each referenced field
     *                           [position] => 1
     *                           )
     *                           )
     *                           )
     *                           [deferrable] => 0
     *                           [initiallydeferred] => 0
     *                           [onupdate] => CASCADE|RESTRICT|SET NULL|SET DEFAULT|NO ACTION
     *                           [ondelete] => CASCADE|RESTRICT|SET NULL|SET DEFAULT|NO ACTION
     *                           [match] => SIMPLE|PARTIAL|FULL
     *                           );
     *                           </pre>
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function createConstraint($table, $name, $definition)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }
        $table = $db->quoteIdentifier($table, true);
        $name = $db->quoteIdentifier($db->getIndexName($name), true);
        $query = "ALTER TABLE {$table} ADD CONSTRAINT {$name}";
        if (!empty($definition['primary'])) {
            $query .= ' PRIMARY KEY';
        } elseif (!empty($definition['unique'])) {
            $query .= ' UNIQUE';
        } elseif (!empty($definition['foreign'])) {
            $query .= ' FOREIGN KEY';
        }
        $fields = [];
        foreach (array_keys($definition['fields']) as $field) {
            $fields[] = $db->quoteIdentifier($field, true);
        }
        $query .= ' (' . implode(', ', $fields) . ')';
        if (!empty($definition['foreign'])) {
            $query .= ' REFERENCES ' . $db->quoteIdentifier($definition['references']['table'], true);
            $referenced_fields = [];
            foreach (array_keys($definition['references']['fields']) as $field) {
                $referenced_fields[] = $db->quoteIdentifier($field, true);
            }
            $query .= ' (' . implode(', ', $referenced_fields) . ')';
            $query .= $this->getAdvancedFKOptions($definition);
        }
        $result = $db->exec($query);
        if (MDB2::isError($result)) {
            return $result;
        }

        return MDB2_OK;
    }

    /**
     * drop existing constraint.
     *
     * @param string $table   name of table that should be used in method
     * @param string $name    name of the constraint to be dropped
     * @param string $primary hint if the constraint is primary
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function dropConstraint($table, $name, $primary = false)
    {
        $db = $this->getDBInstance();
        if (MDB2::isError($db)) {
            return $db;
        }

        $table = $db->quoteIdentifier($table, true);
        $name = $db->quoteIdentifier($db->getIndexName($name), true);
        $result = $db->exec("ALTER TABLE {$table} DROP CONSTRAINT {$name}");
        if (MDB2::isError($result)) {
            return $result;
        }

        return MDB2_OK;
    }

    /**
     * list all constraints in a table.
     *
     * @param string $table name of table that should be used in method
     *
     * @return mixed array of constraint names on success, a MDB2 error on failure
     */
    public function listTableConstraints($table)
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

    /**
     * create sequence.
     *
     * @param string $seq_name name of the sequence to be created
     * @param string $start    start value of the sequence; default is 1
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function createSequence($seq_name, $start = 1)
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

    /**
     * drop existing sequence.
     *
     * @param mixed $name
     *
     * @return mixed MDB2_OK on success, a MDB2 error on failure
     */
    public function dropSequence($name)
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

    /**
     * list all sequences in the current database.
     *
     * @param string database, the current is default
     *               NB: not all the drivers can get the sequence names from
     *               a database other than the current one
     * @param mixed|null $database
     *
     * @return mixed array of sequence names on success, a MDB2 error on failure
     */
    public function listSequences($database = null)
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
}
