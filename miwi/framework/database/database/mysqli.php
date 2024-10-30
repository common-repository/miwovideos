<?php
/*
* @package		Miwi Framework
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @copyright	Copyright (C) 2005-2012 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('MIWI') or die('MIWI');

MLoader::register('MDatabaseMySQL', dirname(__FILE__) . '/mysql.php');
MLoader::register('MDatabaseQueryMySQLi', dirname(__FILE__) . '/mysqliquery.php');
MLoader::register('MDatabaseExporterMySQLi', dirname(__FILE__) . '/mysqliexporter.php');
MLoader::register('MDatabaseImporterMySQLi', dirname(__FILE__) . '/mysqliimporter.php');

class MDatabaseMySQLi extends MDatabaseMySQL {

    public $name = 'mysqli';

    protected function __construct($options) {
        // Get some basic values from the options.
        $options['host']     = (isset($options['host'])) ? $options['host'] : 'localhost';
        $options['user']     = (isset($options['user'])) ? $options['user'] : 'root';
        $options['password'] = (isset($options['password'])) ? $options['password'] : '';
        $options['database'] = (isset($options['database'])) ? $options['database'] : '';
        $options['select']   = (isset($options['select'])) ? (bool)$options['select'] : true;
        $options['port']     = null;
        $options['socket']   = null;

        /*
         * Unlike mysql_connect(), mysqli_connect() takes the port and socket as separate arguments. Therefore, we
         * have to extract them from the host string.
         */
        $tmp = substr(strstr($options['host'], ':'), 1);
        if (!empty($tmp)) {
            // Get the port number or socket name
            if (is_numeric($tmp)) {
                $options['port'] = $tmp;
            }
            else {
                $options['socket'] = $tmp;
            }

            // Extract the host name only
            $options['host'] = substr($options['host'], 0, strlen($options['host']) - (strlen($tmp) + 1));

            // This will take care of the following notation: ":3306"
            if ($options['host'] == '') {
                $options['host'] = 'localhost';
            }
        }

        // Make sure the MySQLi extension for PHP is installed and enabled.
        if (!function_exists('mysqli_connect')) {

            // Legacy error handling switch based on the MError::$legacy switch.
            // @deprecated  12.1
            if (MError::$legacy) {
                $this->errorNum = 1;
                $this->errorMsg = MText::_('MLIB_DATABASE_ERROR_ADAPTER_MYSQLI');
                return;
            }
            else {
                throw new MDatabaseException(MText::_('MLIB_DATABASE_ERROR_ADAPTER_MYSQLI'));
            }
        }

        $this->connection = @mysqli_connect(
            $options['host'], $options['user'], $options['password'], null, $options['port'], $options['socket']
        );

        // Attempt to connect to the server.
        if (!$this->connection) {
            // Legacy error handling switch based on the MError::$legacy switch.
            // @deprecated  12.1
            if (MError::$legacy) {
                $this->errorNum = 2;
                $this->errorMsg = MText::_('MLIB_DATABASE_ERROR_CONNECT_MYSQL');
                return;
            }
            else {
                throw new MDatabaseException(MText::_('MLIB_DATABASE_ERROR_CONNECT_MYSQL'));
            }
        }

        // Finalize initialisation
        MDatabase::__construct($options);

        // Set sql_mode to non_strict mode
        mysqli_query($this->connection, "SET @@SESSION.sql_mode = '';");

        // If auto-select is enabled select the given database.
        if ($options['select'] && !empty($options['database'])) {
            $this->select($options['database']);
        }
    }

    public function __destruct() {
        if (is_callable(array($this->connection, 'close'))) {
            mysqli_close($this->connection);
        }
    }

    public function escape($text, $extra = false) {
        $result = mysqli_real_escape_string($this->getConnection(), $text);

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }

    public static function test() {
        return (function_exists('mysqli_connect'));
    }

    public function connected() {
        if (is_object($this->connection)) {
            return mysqli_ping($this->connection);
        }

        return false;
    }

    public function getAffectedRows() {
        return mysqli_affected_rows($this->connection);
    }

    public function getExporter() {
        // Make sure we have an exporter class for this driver.
        if (!class_exists('MDatabaseExporterMySQLi')) {
            throw new MDatabaseException(MText::_('MLIB_DATABASE_ERROR_MISSING_EXPORTER'));
        }

        $o = new MDatabaseExporterMySQLi;
        $o->setDbo($this);

        return $o;
    }

    public function getImporter() {
        // Make sure we have an importer class for this driver.
        if (!class_exists('MDatabaseImporterMySQLi')) {
            throw new MDatabaseException(MText::_('MLIB_DATABASE_ERROR_MISSING_IMPORTER'));
        }

        $o = new MDatabaseImporterMySQLi;
        $o->setDbo($this);

        return $o;
    }

    public function getNumRows($cursor = null) {
        return mysqli_num_rows($cursor ? $cursor : $this->cursor);
    }

    public function getQuery($new = false) {
        if ($new) {
            // Make sure we have a query class for this driver.
            if (!class_exists('MDatabaseQueryMySQLi')) {
                throw new MDatabaseException(MText::_('MLIB_DATABASE_ERROR_MISSING_QUERY'));
            }
            return new MDatabaseQueryMySQLi($this);
        }
        else {
            return $this->sql;
        }
    }

    public function getVersion() {
        return mysqli_get_server_info($this->connection);
    }

    public function hasUTF() {
        MLog::add('MDatabaseMySQLi::hasUTF() is deprecated.', MLog::WARNING, 'deprecated');
        return true;
    }

    public function insertid() {
        return mysqli_insert_id($this->connection);
    }

    public function execute() {
        if (!is_object($this->connection)) {
            // Legacy error handling switch based on the MError::$legacy switch.
            // @deprecated  12.1
            if (MError::$legacy) {
                if ($this->debug) {
                    MError::raiseError(500, 'MDatabaseMySQLi::query: ' . $this->errorNum . ' - ' . $this->errorMsg);
                }
                return false;
            }
            else {
                MLog::add(MText::sprintf('MLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), MLog::ERROR, 'database');
                throw new MDatabaseException($this->errorMsg, $this->errorNum);
            }
        }

        // Take a local copy so that we don't modify the original query and cause issues later
        $sql = $this->replacePrefix((string)$this->sql);
        if ($this->limit > 0 || $this->offset > 0) {
            $sql .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
        }

        // If debugging is enabled then let's log the query.
        if ($this->debug) {
            // Increment the query counter and add the query to the object queue.
            $this->count++;
            $this->log[] = $sql;

            MLog::add($sql, MLog::DEBUG, 'databasequery');
        }

        // Reset the error values.
        $this->errorNum = 0;
        $this->errorMsg = '';

        // Execute the query.
        $this->cursor = mysqli_query($this->connection, $sql);

        // If an error occurred handle it.
        if (!$this->cursor) {
            $this->errorNum = (int)mysqli_errno($this->connection);
            $this->errorMsg = (string)mysqli_error($this->connection) . ' SQL=' . $sql;

            // Legacy error handling switch based on the MError::$legacy switch.
            // @deprecated  12.1
            if (MError::$legacy) {
                if ($this->debug) {
                    MError::raiseError(500, 'MDatabaseMySQLi::query: ' . $this->errorNum . ' - ' . $this->errorMsg);
                }
                return false;
            }
            else {
                MLog::add(MText::sprintf('MLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), MLog::ERROR, 'databasequery');
                throw new MDatabaseException($this->errorMsg, $this->errorNum);
            }
        }

        return $this->cursor;
    }

    public function select($database) {
        if (!$database) {
            return false;
        }

        if (!mysqli_select_db($this->connection, $database)) {
            // Legacy error handling switch based on the MError::$legacy switch.
            // @deprecated  12.1
            if (MError::$legacy) {
                $this->errorNum = 3;
                $this->errorMsg = MText::_('MLIB_DATABASE_ERROR_DATABASE_CONNECT');
                return false;
            }
            else {
                throw new MDatabaseException(MText::_('MLIB_DATABASE_ERROR_DATABASE_CONNECT'));
            }
        }

        return true;
    }

    public function setUTF() {
        mysqli_set_charset($this->connection, 'utf8');
    }

    protected function fetchArray($cursor = null) {
        return mysqli_fetch_row($cursor ? $cursor : $this->cursor);
    }

    protected function fetchAssoc($cursor = null) {
        return mysqli_fetch_assoc($cursor ? $cursor : $this->cursor);
    }

    protected function fetchObject($cursor = null, $class = 'stdClass') {
        return mysqli_fetch_object($cursor ? $cursor : $this->cursor, $class);
    }

    protected function freeResult($cursor = null) {
        mysqli_free_result($cursor ? $cursor : $this->cursor);
    }

    public function queryBatch($abortOnError = true, $transactionSafe = false) {
        // Deprecation warning.
        MLog::add('MDatabaseMySQLi::queryBatch() is deprecated.', MLog::WARNING, 'deprecated');

        $sql            = $this->replacePrefix((string)$this->sql);
        $this->errorNum = 0;
        $this->errorMsg = '';

        // If the batch is meant to be transaction safe then we need to wrap it in a transaction.
        if ($transactionSafe) {
            $sql = 'START TRANSACTION;' . rtrim($sql, "; \t\r\n\0") . '; COMMIT;';
        }
        $queries = $this->splitSql($sql);
        $error   = 0;
        foreach ($queries as $query) {
            $query = trim($query);
            if ($query != '') {
                $this->cursor = mysqli_query($this->connection, $query);
                if ($this->debug) {
                    $this->count++;
                    $this->log[] = $query;
                }
                if (!$this->cursor) {
                    $error = 1;
                    $this->errorNum .= mysqli_errno($this->connection) . ' ';
                    $this->errorMsg .= mysqli_error($this->connection) . " SQL=$query <br />";
                    if ($abortOnError) {
                        return $this->cursor;
                    }
                }
            }
        }
        return $error ? false : true;
    }
}
