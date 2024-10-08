<?php

// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006-2007 Lorenzo Alberton                             |
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
// | Author: Lorenzo Alberton <l.alberton@quipo.it>                       |
// +----------------------------------------------------------------------+
//
// $Id$

class Nonstandard_Oci8Helper extends Nonstandard_Base
{
    public $trigger_body = '';
    public $when_clause = 'new.id > 0';

    public function createTrigger($trigger_name, $table_name)
    {
        $this->trigger_body = 'BEGIN INSERT INTO ' . $table_name
            . ' (id, somename, somedescription) VALUES'
            . ' (:new.id+1, :new.somename, :new.somedescription); END ' . $trigger_name . ';';
        $query = 'CREATE OR REPLACE TRIGGER ' . $trigger_name
                . ' AFTER UPDATE ON ' . $table_name
                . ' FOR EACH ROW WHEN (' . $this->when_clause . ') '
                . $this->trigger_body;

        return $this->db->exec($query);
    }

    public function checkTrigger($trigger_name, $table_name, $def)
    {
        parent::checkTrigger($trigger_name, $table_name, $def);
        $this->test->assertEquals($this->trigger_body, $def['trigger_body']);
        $this->test->assertEquals($this->when_clause, $def['when_clause']);
    }

    public function dropTrigger($trigger_name, $table_name)
    {
        return $this->db->exec('DROP TRIGGER ' . $trigger_name);
    }

    public function createFunction($name)
    {
        $query = 'CREATE FUNCTION ' . $name . '(a IN INT, b IN INT)
RETURN INT AS
BEGIN
    RETURN a + b;
END;';

        return $this->db->exec($query);
    }
}
