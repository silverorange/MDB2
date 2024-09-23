<?php

// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2006 Lukas Smith, Lorenzo Alberton                |
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
// | Author: Lorenzo Alberton <l dot alberton at quipo dot it>            |
// +----------------------------------------------------------------------+
//
// $Id$

require_once dirname(__DIR__) . '/autoload.inc';

/**
 * A test callback function to be used in the test class below for
 * ensuring that custom datatype callback features are handled
 * correctly.
 *
 * @param MDB2   $db          the MDB2 database resource object
 * @param string $method      The name of the MDB2_Driver_Datatype_Common method
 *                            the callback function was called from. One of
 *                            "getValidTypes", "convertResult", "getDeclaration",
 *                            "compareDefinition", "quote" and "mapPrepareDatatype".
 *                            See {@link MDB2_Driver_Datatype_Common} for the
 *                            details of what each method does.
 * @param array  $aParameters an array of parameters, being the parameters that
 *                            were passed to the method calling the callback
 *                            function
 *
 * @return mixed Returns the appropriate value depending on the method that
 *               called the function. See {@link MDB2_Driver_Datatype_Common}
 *               for details of the expected return values of the five possible
 *               calling methods.
 */
function datatype_test_callback(&$db, $method, $aParameters)
{
    // Ensure the datatype module is loaded
    if (is_null($db->datatype)) {
        $db->loadModule('Datatype', null, true);
    }
    // Lowercase method names for PHP4/PHP5 compatibility
    $method = mb_strtolower($method);
    switch ($method) {
        // For all cases, return a string that identifies that the
        // callback method was able to call to the appropriate point
        case 'getvalidtypes':
            return 'datatype_test_callback::getvalidtypes';

        case 'convertresult':
            return 'datatype_test_callback::convertresult';

        case 'getdeclaration':
            return 'datatype_test_callback::getdeclaration';

        case 'comparedefinition':
            return 'datatype_test_callback::comparedefinition';

        case 'quote':
            return 'datatype_test_callback::quote';

        case 'mappreparedatatype':
            return 'datatype_test_callback::mappreparedatatype';
    }
}

/**
 * A test callback function to be used in the test class below for
 * ensuring that custom nativetype to datatype mapping is handled
 * correctly.
 *
 * @param MDB2  $db      the MDB2 database reource object
 * @param array $aFields The standard array of fields produced from the
 *                       MySQL command "SHOW COLUMNS". See
 *                       {@link http://dev.mysql.com/doc/refman/5.0/en/describe.html}
 *                       for more details on the format of the fields.
 *                       "type"      The nativetype column type
 *                       "null"      "YES" or "NO"
 *                       "key"       "PRI", "UNI", "MUL", or null
 *                       "default"   The default value of the column
 *                       "extra"     "auto_increment", or null
 *
 * @return array Returns an array of the following items:
 *               0 => An array of possible MDB2 datatypes. As this is
 *               a custom type, always has one entry, "test".
 *               1 => The length of the type, if defined by the nativetype,
 *               otherwise null.
 *               2 => A boolean value indicating the "unsigned" nature of numeric
 *               fields. Always null in this case, as this custom test
 *               type is not numeric.
 *               3 => A boolean value indicating the "fixed" nature of text
 *               fields. Always bull in this case, as this custom test
 *               type is not textual.
 */
function nativetype_test_callback($db, $aFields)
{
    // Prepare the type array
    $aType = [];
    $aType[] = 'test';
    // Can the length of the field be found?
    $length = null;
    $start = mb_strpos($aFields['type'], '(');
    $end = mb_strpos($aFields['type'], ')');
    if ($start && $end) {
        $start++;
        $chars = $end - $start;
        $length = mb_substr($aFields['type'], $start, $chars);
    }
    // No unsigned value needed
    $unsigned = null;
    // No fixed value needed
    $fixed = null;

    return [$aType, $length, $unsigned, $fixed];
}

/**
 * @internal
 *
 * @coversNothing
 */
class Standard_DatatypeTest extends Standard_Abstract
{
    // Test table name (it is dynamically created/dropped)
    public $table = 'datatypetable';

    /**
     * Can not use setUp() because we are using a dataProvider to get multiple
     * MDB2 objects per test.
     *
     * @param array $ci an associative array with two elements.  The "dsn"
     *                  element must contain an array of DSN information.
     *                  The "options" element must be an array of connection
     *                  options.
     */
    protected function manualSetUp($ci)
    {
        parent::manualSetUp($ci);

        $this->db->loadModule('Manager', null, true);
        $this->fields = [
            'id' => [
                'type'     => 'integer',
                'unsigned' => true,
                'notnull'  => true,
                'default'  => 0,
            ],
            'textfield' => [
                'type'   => 'text',
                'length' => 12,
            ],
            'booleanfield' => [
                'type' => 'boolean',
            ],
            'decimalfield' => [
                'type' => 'decimal',
            ],
            'floatfield' => [
                'type' => 'float',
            ],
            'datefield' => [
                'type' => 'date',
            ],
            'timefield' => [
                'type' => 'time',
            ],
            'timestampfield' => [
                'type' => 'timestamp',
            ],
        ];
        if (!$this->tableExists($this->table)) {
            $this->db->manager->createTable($this->table, $this->fields);
        }
    }

    /**
     * The teardown method to clean up the testing environment.
     */
    public function tearDown()
    {
        if (!$this->db || MDB2::isError($this->db)) {
            return;
        }
        if ($this->tableExists($this->table)) {
            $this->db->manager->dropTable($this->table);
        }
        parent::tearDown();
    }

    /**
     * Get the types of each field given its name.
     *
     * @param array $names list of field names
     *
     * @return array $types list of matching field types
     *
     * @dataProvider provider
     */
    public function getFieldTypes($names)
    {
        $types = [];
        foreach ($names as $name) {
            foreach ($this->fields as $fieldname => $field) {
                if ($name == $fieldname) {
                    $types[$name] = $field['type'];
                }
            }
        }

        return $types;
    }

    /**
     * Insert the values into the sample table.
     *
     * @param array $values associative array (name => value)
     *
     * @dataProvider provider
     */
    public function insertValues($values)
    {
        $types = $this->getFieldTypes(array_keys($values));

        $result = $this->db->exec('DELETE FROM ' . $this->table);
        if (MDB2::isError($result)) {
            $this->assertTrue(false, 'Error emptying table: ' . $result->getMessage());
        }

        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', array_keys($values)),
            implode(', ', array_fill(0, count($values), '?'))
        );
        $stmt = $this->db->prepare($query, array_values($types), MDB2_PREPARE_MANIP);
        if (MDB2::isError($stmt)) {
            $this->assertTrue(false, 'Error creating prepared query: ' . $stmt->getMessage());
        }
        $result = $stmt->execute(array_values($values));
        if (MDB2::isError($result)) {
            $this->assertTrue(false, 'Error executing prepared query: ' . $result->getMessage());
        }
        $stmt->free();
    }

    /**
     * Select the inserted row from the db and check the inserted values.
     *
     * @param array $values associative array (name => value) of inserted data
     *
     * @dataProvider provider
     */
    public function selectAndCheck($values)
    {
        $types = $this->getFieldTypes(array_keys($values));

        $query = 'SELECT ' . implode(', ', array_keys($values)) . ' FROM ' . $this->table;
        $result = $this->db->queryRow($query, $types, MDB2_FETCHMODE_ASSOC);
        foreach ($values as $name => $value) {
            $this->assertEquals($result[$name], $values[$name], 'Error in ' . $types[$name] . ' value: incorrect conversion');
        }
    }

    /**
     * Test the TEXT datatype for incorrect conversions.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     * @param mixed $emulate_prepared
     */
    public function testTextDataType($ci, $emulate_prepared = false)
    {
        $this->manualSetUp($ci);

        if ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', true);
        }

        $data = [
            'id'        => 1,
            'textfield' => 'test',
        ];
        $this->insertValues($data);
        $this->selectAndCheck($data);

        if (!$emulate_prepared && !$this->db->getOption('emulate_prepared')) {
            $this->testTextDataType($ci, true);
        } elseif ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', false);
        }
    }

    /**
     * Test the DECIMAL datatype for incorrect conversions.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     * @param mixed $emulate_prepared
     */
    public function testDecimalDataType($ci, $emulate_prepared = false)
    {
        $this->manualSetUp($ci);

        if ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', true);
        }

        $data = [
            'id'           => 1,
            'decimalfield' => 10.35,
        ];
        $this->insertValues($data);
        $this->selectAndCheck($data);

        $old_locale = setlocale(LC_NUMERIC, 0);
        if (OS_UNIX) {
            setlocale(LC_NUMERIC, 'de_DE@euro', 'de_DE', 'de', 'ge');
        } else {
            setlocale(LC_NUMERIC, 'de_DE@euro', 'de_DE', 'deu_deu');
        }

        $this->insertValues($data);
        $this->selectAndCheck($data);

        setlocale(LC_NUMERIC, $old_locale);

        $expected = 10.35;

        $actual = $this->db->quote($expected, 'decimal');
        $this->assertEquals($expected, $actual);

        $non_us = number_format($expected, 2, ',', '');
        $actual = $this->db->quote($non_us, 'decimal');
        $this->assertEquals($expected, $actual);

        $expected = 1000.35;

        $non_us = '1,000.35';
        $actual = $this->db->quote($non_us, 'decimal');
        $this->assertEquals($expected, $actual);

        $non_us = '1000,35';
        $actual = $this->db->quote($non_us, 'decimal');
        $this->assertEquals($expected, $actual);

        $non_us = '1.000,35';
        $actual = $this->db->quote($non_us, 'decimal');
        $this->assertEquals($expected, $actual);

        // test quoting with invalid chars
        $val = '100.3abc";d@a[\\';
        $this->assertEquals(100.3, $this->db->quote($val, 'decimal'));

        if (!$emulate_prepared && !$this->db->getOption('emulate_prepared')) {
            $this->testDecimalDataType($ci, true);
        } elseif ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', false);
        }
    }

    /**
     * Test the FLOAT datatype for incorrect conversions.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     * @param mixed $emulate_prepared
     */
    public function testFloatDataType($ci, $emulate_prepared = false)
    {
        $this->manualSetUp($ci);

        if ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', true);
        }

        $data = [
            'id'         => 1,
            'floatfield' => 10.35,
        ];
        $this->insertValues($data);
        $this->selectAndCheck($data);

        $old_locale = setlocale(LC_NUMERIC, 0);
        if (OS_UNIX) {
            setlocale(LC_NUMERIC, 'de_DE@euro', 'de_DE', 'de', 'ge');
        } else {
            setlocale(LC_NUMERIC, 'de_DE@euro', 'de_DE', 'deu_deu');
        }

        $this->insertValues($data);
        $this->selectAndCheck($data);

        setlocale(LC_NUMERIC, $old_locale);

        $data['floatfield'] = '1.035e+1';
        $this->insertValues($data);
        $this->selectAndCheck($data);

        $data['floatfield'] = '1.035E+01';
        $this->insertValues($data);
        $this->selectAndCheck($data);

        $expected = '1.035E+01';
        $non_us = '1,035e+1';
        $actual = $this->db->quote($non_us, 'float');
        $this->assertEquals($expected, $actual);

        $expected = 10.35;

        $actual = $this->db->quote($expected, 'float');
        $this->assertEquals($expected, $actual);

        $non_us = number_format($expected, 2, ',', '');
        $actual = $this->db->quote($non_us, 'float');
        $this->assertEquals($expected, $actual);

        $expected = 1000.35;

        $non_us = '1,000.35';
        $actual = $this->db->quote($non_us, 'float');
        $this->assertEquals($expected, $actual);

        $non_us = '1000,35';
        $actual = $this->db->quote($non_us, 'float');
        $this->assertEquals($expected, $actual);

        $non_us = '1.000,35';
        $actual = $this->db->quote($non_us, 'float');
        $this->assertEquals($expected, $actual);

        // test quoting with invalid chars
        $val = '100.3abc";d@a[\\';
        $this->assertEquals(100.3, $this->db->quote($val, 'float'));

        if (!$emulate_prepared && !$this->db->getOption('emulate_prepared')) {
            $this->testFloatDataType($ci, true);
        } elseif ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', false);
        }
    }

    /**
     * Test the BOOLEAN datatype for incorrect conversions.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     * @param mixed $emulate_prepared
     */
    public function testBooleanDataType($ci, $emulate_prepared = false)
    {
        $this->manualSetUp($ci);

        if ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', true);
        }

        $data = [
            'id'           => 1,
            'booleanfield' => true,
        ];
        $this->insertValues($data);
        $this->selectAndCheck($data);

        $data['booleanfield'] = false;
        $this->insertValues($data);
        $this->selectAndCheck($data);

        if (!$emulate_prepared && !$this->db->getOption('emulate_prepared')) {
            $this->testBooleanDataType($ci, true);
        } elseif ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', false);
        }
    }

    /**
     * Test the DATE datatype for incorrect conversions.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     * @param mixed $emulate_prepared
     */
    public function testDateDataType($ci, $emulate_prepared = false)
    {
        $this->manualSetUp($ci);

        if ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', true);
        }

        $data = [
            'id'        => 1,
            'datefield' => date('Y-m-d'),
        ];
        $this->insertValues($data);
        $this->selectAndCheck($data);

        if (!$emulate_prepared && !$this->db->getOption('emulate_prepared')) {
            $this->testDateDataType($ci, true);
        } elseif ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', false);
        }
    }

    /**
     * Test the TIME datatype for incorrect conversions.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     * @param mixed $emulate_prepared
     */
    public function testTimeDataType($ci, $emulate_prepared = false)
    {
        $this->manualSetUp($ci);

        if ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', true);
        }

        $data = [
            'id'        => 1,
            'timefield' => date('H:i:s'),
        ];
        $this->insertValues($data);
        $this->selectAndCheck($data);

        if (!$emulate_prepared && !$this->db->getOption('emulate_prepared')) {
            $this->testTimeDataType($ci, true);
        } elseif ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', false);
        }
    }

    /**
     * Test the TIMESTAMP datatype for incorrect conversions.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     * @param mixed $emulate_prepared
     */
    public function testTimestampDataType($ci, $emulate_prepared = false)
    {
        $this->manualSetUp($ci);

        if ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', true);
        }

        $data = [
            'id'             => 1,
            'timestampfield' => date('Y-m-d H:i:s'),
        ];
        $this->insertValues($data);
        $this->selectAndCheck($data);

        if (!$emulate_prepared && !$this->db->getOption('emulate_prepared')) {
            $this->testTimestampDataType($ci, true);
        } elseif ($emulate_prepared) {
            $this->db->setOption('emulate_prepared', false);
        }
    }

    /**
     * Tests escaping of text values with special characters.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testEscapeSequences($ci)
    {
        $this->manualSetUp($ci);

        $test_strings = [
            "'",
            '"',
            '\\',
            '%',
            '_',
            "''",
            '""',
            '\\\\',
            "\\'\\'",
            '\"\"',
        ];

        $this->clearTables();
        foreach ($test_strings as $key => $string) {
            $value = $this->db->quote($string, 'text');
            $query = "INSERT INTO {$this->table_users} (user_name,user_id) VALUES ({$value}, {$key})";
            $result = $this->db->exec($query);

            if (MDB2::isError($result)) {
                $this->assertTrue(false, 'Error executing insert query' . $result->getMessage());
            }

            $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE user_id = ' . $key;
            $value = $this->db->queryOne($query, 'text');

            if (MDB2::isError($value)) {
                $this->assertTrue(false, 'Error executing select query' . $value->getMessage());
            }

            $this->assertEquals($string, $value, "the value retrieved for field \"user_name\" doesn't match what was stored");
        }
    }

    /**
     * Tests escaping of text pattern strings with special characters.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testPatternSequences($ci)
    {
        $this->manualSetUp($ci);

        $case_sensitive_expect = match ($this->db->phptype) {
            'sqlite' => 3,
            default  => 2,
        };

        $test_strings = [
            'Foo',
            'FOO',
            'foo',
        ];

        $this->clearTables();
        foreach ($test_strings as $key => $string) {
            $value = $this->db->quote($string, 'text');
            $query = "INSERT INTO {$this->table_users} (user_name,user_id) VALUES ({$value}, {$key})";
            $result = $this->db->exec($query);
            if (MDB2::isError($result)) {
                $this->assertTrue(false, 'Error executing insert query' . $result->getMessage());
            }
        }

        $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE '
            . $this->db->datatype->matchPattern(['F', '%'], 'LIKE', 'user_name');
        $values = $this->db->queryCol($query, 'text');
        $this->assertEquals($case_sensitive_expect, count($values), 'case sensitive search was expected to return 2 rows but returned: ' . count($values));

        // NOTE: if this test fails on mysql, it's due to table/field having
        // binary collation.
        $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE '
            . $this->db->datatype->matchPattern(['foo'], 'ILIKE', 'user_name');
        $values = $this->db->queryCol($query, 'text');
        $this->assertEquals(3, count($values), 'case insensitive search was expected to return 3 rows but returned: ' . count($values));

        $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE '
            . $this->db->datatype->matchPattern([1 => '_', 'o', '%'], 'LIKE', 'user_name');
        $values = $this->db->queryCol($query, 'text');
        $this->assertEquals($case_sensitive_expect, count($values), 'case sensitive search was expected to return 2 rows but returned: ' . count($values));

        $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE '
            . $this->db->datatype->matchPattern([1 => '_', 'o', '%'], 'ILIKE', 'user_name');
        $values = $this->db->queryCol($query, 'text');
        $this->assertEquals(3, count($values), 'case insensitive search was expected to return 3 rows but returned: ' . count($values));
    }

    /**
     * Tests escaping of text pattern strings with special characters.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testEscapePatternSequences($ci)
    {
        $this->manualSetUp($ci);

        if (!$this->supported('pattern_escaping')) {
            return;
        }

        $test_strings = [
            '%',
            '_',
            '%_',
            '_%',
            '%Foo%',
            '%Foo_',
            'Foo%123',
            'Foo_123',
            '_Foo%',
            '_Foo_',
            "%'",
            "_'",
            "'%",
            "'_",
            "'%'",
            "'_'",
        ];

        $this->clearTables();
        foreach ($test_strings as $key => $string) {
            $value = $this->db->quote($string, 'text');
            $query = "INSERT INTO {$this->table_users} (user_name,user_id) VALUES ({$value}, {$key})";
            $result = $this->db->exec($query);
            if (MDB2::isError($result)) {
                $this->assertTrue(false, 'Error executing insert query' . $result->getMessage());
            }

            $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE user_name LIKE ' . $this->db->quote($string, 'text', true, true);
            $value = $this->db->queryOne($query, 'text');
            if (MDB2::isError($value)) {
                $this->assertTrue(false, 'Error executing select query' . $value->getMessage());
            }

            $this->assertEquals($string, $value, "the value retrieved for field \"user_name\" doesn't match what was stored");
        }

        $this->db->loadModule('Datatype', null, true);
        $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE user_name LIKE ' . $this->db->datatype->matchPattern(['Foo%', '_', '23']);
        $value = $this->db->queryOne($query, 'text');
        $this->assertEquals('Foo%123', $value, "the value retrieved for field \"user_name\" doesn't match what was stored");

        $query = 'SELECT user_name FROM ' . $this->table_users . ' WHERE user_name LIKE ' . $this->db->datatype->matchPattern([1 => '_', 'oo', '%']);
        $value = $this->db->queryOne($query, 'text');
        $this->assertEquals('Foo', mb_substr($value, 0, 3), "the value retrieved for field \"user_name\" doesn't match what was stored");
    }

    /**
     * A method to test that the MDB2_Driver_Datatype_Common::getValidTypes()
     * method returns the correct data array.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testGetValidTypes($ci)
    {
        $this->manualSetUp($ci);

        $this->db->loadModule('Datatype', null, true);
        // Test with just the default MDB2 datatypes.
        $aExpected = $this->db->datatype->valid_default_values;
        $aResult = $this->db->datatype->getValidTypes();
        $this->assertEquals($aExpected, $aResult, 'getValidTypes');

        // Test with a custom datatype
        $this->db->setOption('datatype_map', ['test' => 'test']);
        $this->db->setOption('datatype_map_callback', ['test' => 'datatype_test_callback']);
        $aExpected = array_merge(
            $this->db->datatype->valid_default_values,
            ['test' => 'datatype_test_callback::getvalidtypes']
        );
        $aResult = $this->db->datatype->getValidTypes();
        $this->assertEquals($aExpected, $aResult, 'getValidTypes');
        unset($this->db->options['datatype_map'], $this->db->options['datatype_map_callback']);
    }

    /**
     * A method to test that the MDB2_Driver_Datatype_Common::convertResult()
     * method returns correctly converted column data.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testConvertResult($ci)
    {
        $this->manualSetUp($ci);

        $this->db->loadModule('Datatype', null, true);
        // Test with an MDB2 datatype, eg. "text"
        $value = 'text';
        $type = 'text';
        $result = $this->db->datatype->convertResult($value, $type);
        $this->assertEquals($value, $result, 'convertResult');

        // Test with a custom datatype
        $this->db->setOption('datatype_map', ['test' => 'test']);
        $this->db->setOption('datatype_map_callback', ['test' => 'datatype_test_callback']);
        $value = 'text';
        $type = 'test';
        $result = $this->db->datatype->convertResult($value, $type);

        // Do this to avoid memory exhaustion by PHPUnit.
        if (MDB2::isError($result)) {
            $this->fail($result->getUserInfo());
        } else {
            $this->assertEquals('datatype_test_callback::convertresult', $result, 'mapPrepareDatatype');
        }

        unset($this->db->options['datatype_map'], $this->db->options['datatype_map_callback']);
    }

    /**
     * A method to test that the MDB2_Driver_Datatype_Common::getDeclaration()
     * method returns correctly formatted SQL for declaring columns.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testGetDeclaration($ci)
    {
        $this->manualSetUp($ci);

        $this->db->loadModule('Datatype', null, true);
        // Test with an MDB2 datatype, eg. "integer"
        $name = 'column';
        $type = 'integer';
        $field = ['type' => 'integer'];
        $result = $this->db->datatype->getDeclaration($type, $name, $field);
        $actual_type = $this->db->phptype == 'sqlite' ? 'INTEGER' : 'INT';
        $default = $this->db->phptype == 'mssql' ? ' NULL' : ' DEFAULT NULL';

        // Do this to avoid memory exhaustion by PHPUnit.
        if (MDB2::isError($result)) {
            $this->fail($result->getUserInfo());
        } else {
            $this->assertEquals('column ' . $actual_type . $default, $result, 'getDeclaration');
        }

        // Test with a custom datatype
        $this->db->setOption('datatype_map', ['test' => 'test']);
        $this->db->setOption('datatype_map_callback', ['test' => 'datatype_test_callback']);
        $name = 'column';
        $type = 'test';
        $field = ['type' => 'test'];
        $result = $this->db->datatype->getDeclaration($type, $name, $field);

        // Do this to avoid memory exhaustion by PHPUnit.
        if (MDB2::isError($result)) {
            $this->fail($result->getUserInfo());
        } else {
            $this->assertEquals('datatype_test_callback::getdeclaration', $result, 'getDeclaration');
        }

        // Test with a custom datatype without datatype_map_callback function #1
        $name = 'address';
        $type = 'text';
        $field = [
            'name'    => 'company_addr',
            'type'    => 'text',
            'notnull' => 'true',
        ];
        $this->db->setOption('datatype_map', [$name => $type]);

        // Do this to avoid memory exhaustion by PHPUnit.
        if (MDB2::isError($result)) {
            $this->fail($result->getUserInfo());
        } else {
            $result = $this->db->datatype->getDeclaration($field['type'], $field['name'], $field);
        }

        $notnull = ' NOT NULL';
        $expected = $field['name'] . ' ' . $this->db->datatype->getTypeDeclaration(['type' => $type]) . $notnull;
        $this->assertEquals($expected, $result);

        // Test with a custom datatype without datatype_map_callback function #2
        $name = 'address';
        $type = 'text';
        $field = [
            'name' => 'company_addr',
            'type' => 'address',
        ];
        $this->db->setOption('datatype_map', [$name => $type]);

        // Do this to avoid memory exhaustion by PHPUnit.
        if (MDB2::isError($result)) {
            $this->fail($result->getUserInfo());
        } else {
            $result = $this->db->datatype->getDeclaration($field['type'], $field['name'], $field);
        }

        $default = $this->db->phptype == 'mssql' ? ' NULL' : '';
        $expected = $field['name'] . ' ' . $this->db->datatype->getTypeDeclaration(['type' => $type]) . $default;
        $this->assertEquals($expected, $result);
        unset($this->db->options['datatype_map'], $this->db->options['datatype_map_callback']);
    }

    /**
     * A method to test that the MDB2_Driver_Datatype_Common::compareDefinition()
     * method.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testCompareDefinition($ci)
    {
        $this->manualSetUp($ci);

        // Test with an MDB2 datatype, eg. "text"
        $aPrevious = [
            'type'   => 'text',
            'length' => 4,
        ];
        $aCurrent = [
            'type'   => 'text',
            'length' => 5,
        ];
        $aResult = $this->db->datatype->compareDefinition($aCurrent, $aPrevious);
        $this->assertTrue(is_array($aResult), 'compareDefinition');
        $this->assertEquals(1, count($aResult), 'compareDefinition');
        $this->assertTrue($aResult['length'], 'compareDefinition');

        // Test with a custom datatype
        $this->db->setOption('datatype_map', ['test' => 'test']);
        $this->db->setOption('datatype_map_callback', ['test' => 'datatype_test_callback']);
        $aPrevious = [
            'type' => 'test',
        ];
        $aCurrent = [
            'type' => 'test',
        ];
        $result = $this->db->datatype->compareDefinition($aCurrent, $aPrevious);

        // Do this to avoid memory exhaustion by PHPUnit.
        if (MDB2::isError($result)) {
            $this->fail($result->getUserInfo());
        } else {
            $this->assertEquals('datatype_test_callback::comparedefinition', $result, 'compareDefinition');
        }

        unset($this->db->options['datatype_map'], $this->db->options['datatype_map_callback']);
    }

    /**
     * A method to test that the MDB2_Driver_Datatype_Common::quote()
     * method returns correctly quoted column data.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testQuote($ci)
    {
        $this->manualSetUp($ci);

        $this->db->loadModule('Datatype', null, true);
        // Test with an MDB2 datatype, eg. "text"
        $value = 'text';
        $type = 'text';
        $result = $this->db->datatype->quote($value, $type);
        $this->assertEquals("'{$value}'", $result, 'quote');

        // Test with a custom datatype
        $this->db->setOption('datatype_map', ['test' => 'test']);
        $this->db->setOption('datatype_map_callback', ['test' => 'datatype_test_callback']);
        $value = 'text';
        $type = 'test';
        $result = $this->db->datatype->quote($value, $type);

        // Do this to avoid memory exhaustion by PHPUnit.
        if (MDB2::isError($result)) {
            $this->fail($result->getUserInfo());
        } else {
            $this->assertEquals('datatype_test_callback::quote', $result, 'quote');
        }

        unset($this->db->options['datatype_map'], $this->db->options['datatype_map_callback']);
    }

    /**
     * A method to test that the MDB2_Driver_Datatype_Common::mapPrepareDatatype()
     * method returns the correct data type.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testMapPrepareDatatype($ci)
    {
        $this->manualSetUp($ci);

        $this->db->loadModule('Datatype', null, true);
        // Test with an MDB2 datatype, eg. "text"
        $type = 'text';
        $result = $this->db->datatype->mapPrepareDatatype($type);
        if ($this->db->phptype == 'mysqli') {
            $type = 's';
        }
        $this->assertEquals($type, $result, 'mapPrepareDatatype');

        // Test with a custom datatype
        $this->db->setOption('datatype_map', ['test' => 'test']);
        $this->db->setOption('datatype_map_callback', ['test' => 'datatype_test_callback']);
        $type = 'test';
        $result = $this->db->datatype->mapPrepareDatatype($type);
        $this->assertEquals('datatype_test_callback::mappreparedatatype', $result, 'mapPrepareDatatype');
        unset($this->db->options['datatype_map'], $this->db->options['datatype_map_callback']);
    }

    /**
     * A method to test that the MDB2_Driver_Datatype_Common::mapNativeDatatype()
     * method returns the correct MDB2 datatype from a given nativetype.
     *
     * @dataProvider provider
     *
     * @param mixed $ci
     */
    public function testMapNativeDatatype($ci)
    {
        $this->manualSetUp($ci);

        $this->db->loadModule('Datatype', null, true);
        // Test with an common nativetype, eg. "text"
        $field = [
            'type'   => 'int',
            'length' => 8,
        ];
        if (in_array($this->db->phptype, ['ibase', 'oci8'])) {
            $field['type'] = 'integer';
        }
        $expected_length = 8;
        if (in_array($this->db->phptype, ['mysql', 'mysqli', 'pgsql', 'sqlite', 'mssql'])) {
            $expected_length = 4;
        }
        $result = $this->db->datatype->mapNativeDatatype($field);
        if (MDB2::isError($result)) {
            $this->assertTrue(false, 'mapNativeDatatype: ' . $result->getUserInfo());
        } else {
            $this->assertTrue(is_array($result), 'mapNativeDatatype');
            $this->assertEquals(4, count($result), 'mapNativeDatatype');
            $this->assertEquals('integer', $result[0][0], 'mapNativeDatatype');
            $this->assertEquals($expected_length, $result[1], 'mapNativeDatatype');
        }

        // Test with a custom nativetype mapping
        $this->db->setOption('nativetype_map_callback', ['test' => 'nativetype_test_callback']);
        $field = [
            'type' => 'test',
        ];
        $result = $this->db->datatype->mapNativeDatatype($field);
        $this->assertTrue(is_array($result), 'mapNativeDatatype');
        $this->assertEquals(4, count($result), 'mapNativeDatatype');
        $this->assertEquals('test', $result[0][0], 'mapNativeDatatype');
        $this->assertNull($result[1], 'mapNativeDatatype');
        $this->assertNull($result[2], 'mapNativeDatatype');
        $this->assertNull($result[3], 'mapNativeDatatype');
        $field = [
            'type' => 'test(10)',
        ];
        $result = $this->db->datatype->mapNativeDatatype($field);
        $this->assertTrue(is_array($result), 'mapNativeDatatype');
        $this->assertEquals(count($result), 4, 'mapNativeDatatype');
        $this->assertEquals($result[0][0], 'test', 'mapNativeDatatype');
        $this->assertEquals($result[1], 10, 'mapNativeDatatype');
        $this->assertNull($result[2], 'mapNativeDatatype');
        $this->assertNull($result[3], 'mapNativeDatatype');
        unset($this->db->options['nativetype_map_callback']);
    }
}
