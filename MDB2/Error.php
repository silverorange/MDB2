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
 * +----------------------------------------------------------------------+
 */

/**
 * MDB2_Error implements a class for reporting portable database error
 * messages.
 *
 * @category Database
 * @package  MDB2
 * @author   Stig Bakken <ssb@fast.no>
 * @license  http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
class MDB2_Error extends PEAR_Error
{
    // {{{ constructor: function MDB2_Error($code = MDB2_ERROR, $mode = PEAR_ERROR_RETURN, $level = E_USER_NOTICE, $debuginfo = null)

    /**
     * MDB2_Error constructor.
     *
     * @param   mixed   MDB2 error code, or string with error message.
     * @param   int     what 'error mode' to operate in
     * @param   int     what error level to use for $mode & PEAR_ERROR_TRIGGER
     * @param   mixed   additional debug info, such as the last query
     */
    public function __construct(
        $code = MDB2_ERROR,
        $mode = PEAR_ERROR_RETURN,
        $level = E_USER_NOTICE,
        $debuginfo = null,
        $dummy = null
    ) {
        if (null === $code) {
            $code = MDB2_ERROR;
        }

        parent::__construct(
            'MDB2 Error: '.MDB2::errorMessage($code),
            $code,
            $mode,
            $level,
            $debuginfo
        );
    }

    // }}}
}

?>
