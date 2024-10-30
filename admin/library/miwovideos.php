<?php
/**
 * @package		MiwoVideos
 * @copyright	Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

require_once(dirname(__FILE__).'/initialise.php');

abstract class MiwoVideos {

    public static function &get($filePath, $options = null) {
        static $instances = array();

        $parts = explode('.', $filePath);
        $class = array_pop($parts);

		if (!isset($instances[$class])) {
			require_once(MPATH_MIWOVIDEOS_LIB.'/'.strtolower(str_replace('.', '/', $filePath)).'.php');
			
			$class_name = 'Miwovideos'.ucfirst($class);
			if (class_exists($class_name)) {
                $instances[$class] = new $class_name($options);
            }
		}

		return $instances[$class];
    }

    public static function is30() {
        return self::get('utility')->is30();
   	}

    public static function is31() {
        return self::get('utility')->is31();
   	}

    public static function is32() {
        return self::get('utility')->is32();
   	}

    public static function getConfig($type = null) {
        return self::get('utility')->getConfig($type);
    }

	public static function getTable($name) {
        return self::get('utility')->getTable($name);
	}

    public static function getInput() {
        return new MRequest();
    }

    public static function getButtonClass() {
        return self::get('utility')->getConfig()->get('button_class');
    }

    public static function isDashboard() {
        return self::get('utility')->isDashboard();
    }

    public static function getVideoRoute($id) {
        return 'index.php?option=com_miwovideos&view=video&video_id='. $id;
    }

	public static function getPlugin($name, $folder = 'miwovideos') {
		return self::get('utility')->getPlugin($name, $folder);
	}

    public static function log($message, $priority = null) {
        return self::get('utility')->log($message, $priority);
    }

    public static function agoDateFormat($date) {
        return self::get('utility')->agoDateFormat($date);
    }

    public static function exec($command, &$output = null, &$return_var = null) {
        return self::get('utility')->exec($command, $output, $return_var);
    }
}