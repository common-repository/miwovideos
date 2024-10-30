<?php
/*
* @package		Miwi Framework
* @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
* @copyright	Copyright (C) 2005-2012 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('MIWI') or die('MIWI');

require_once dirname(__FILE__) . '/mysqlimporter.php';

class MDatabaseImporterMySQLi extends MDatabaseImporterMySQL {

    public function check() {
        // Check if the db connector has been set.
        if (!($this->db instanceof MDatabaseMySqli)) {
            throw new Exception('MPLATFORM_ERROR_DATABASE_CONNECTOR_WRONG_TYPE');
        }

        // Check if the tables have been specified.
        if (empty($this->from)) {
            throw new Exception('MPLATFORM_ERROR_NO_TABLES_SPECIFIED');
        }

        return $this;
    }

    public function setDbo(MDatabaseMySQLi $db) {
        $this->db = $db;

        return $this;
    }
}
