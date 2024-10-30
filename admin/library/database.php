<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Database class, extends MDatabase
class MiwoDB {

	protected static $_dbo;

	protected function __construct() {
		self::getDBO();
	}

	public static function getInstance() {
		static $instance;
		
		if (!isset($instance)) {
			$instance = new MiwoDB();
		}

		return $instance;
	}

	public static function getDBO() {
		if (!isset(self::$_dbo)) {
			self::$_dbo = MFactory::getDBO();
		}
	}
	
	# Quote
	public static function quote($text, $escaped = true) {
		self::getDBO();
		$result = self::$_dbo->Quote($text, $escaped);
		
		self::showError();
	
		return $result;
	}
	
	# Escape
	public static function getEscaped($text, $extra = false) {
		self::getDBO();
		
		if (version_compare(MVERSION, '1.6.0', 'ge')) {
			$result = self::$_dbo->escape($text, $extra);
		}
		else {
			$result = self::$_dbo->getEscaped($text, $extra);
		}
		
		self::showError();
	
		return $result;
	}
	
	# Run
	public static function query($query) {
		return self::execute($query);
	}

	# Run
	public static function execute($query) {
		# Run query
		self::getDBO();

		self::$_dbo->setQuery($query);
		$result = self::$_dbo->execute();

		self::showError();

		return $result;
	}
	
	# Single value result
	public static function loadResult($query) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query);
		$result = self::$_dbo->loadResult();
		
		self::showError();

		return $result;
	}
	
	# Single row results
	public static function loadRow($query) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query);
		$result = self::$_dbo->loadRow();
		
		self::showError();

		return $result;
	}
	
	public static function loadAssoc($query) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query);
		$result = self::$_dbo->loadAssoc();
		
		self::showError();

		return $result;
	}
	
	public static function loadObject($query) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query);
		$result = self::$_dbo->loadObject();
		
		self::showError();

		return $result;
	}
	
	# Single column results
	public static function loadColumn($query, $index = 0) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query);

	    $result = self::$_dbo->loadColumn($index);
		
		self::showError();

		return $result;
	}

	# Single column results
	public static function loadResultArray($query, $index = 0) {
		return self::loadColumn($query, $index);
	}

	# Multi-Row results
	public static function loadRowList($query, $offset = 0, $limit = 0) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query, $offset, $limit);
		$result = self::$_dbo->loadRowList();
		
		self::showError();

		return $result;
	}
	
	public static function loadAssocList($query, $key = '', $offset = 0, $limit = 0) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query, $offset, $limit);
		$result = self::$_dbo->loadAssocList($key);
		
		self::showError();

		return $result;
	}

	public static function loadObjectList($query, $key = '', $offset = 0, $limit = 0) {
		# Run query
		self::getDBO();
		self::$_dbo->setQuery($query, $offset, $limit);
		$result = self::$_dbo->loadObjectList($key);
		
		self::showError();

		return $result;
	}

	public static function insertid() {
		# Run query
		self::getDBO();

		$result = self::$_dbo->insertid();

		self::showError();

		return $result;
	}
	
	protected static function showError() {
        $config = MiwoVideos::getConfig();
		if ($config->get('show_db_errors') == 1 && self::$_dbo->getErrorNum()) {
			throw new Exception(__METHOD__.' failed. ('.self::$_dbo->getErrorMsg().')');
		}
	}
}