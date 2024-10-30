<?php
/**
 * @package        MiwoVideos
 * @copyright      Copyright (C) 2009-2016 Miwisoft, LLC. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');
mimport('framework.filesystem.archive');
mimport('framework.filesystem.path');
mimport('framework.application.component.helper');

# Utility class
class MiwovideosUtility {

	private static $data = array();

	public function __construct() {
		$this->config = $this->getConfig();
		$this->app    = MFactory::getApplication();
	}

	public static function getConfig() {

		if (version_compare(PHP_VERSION, '5.2.0', '<')) {
			MFactory::getApplication()->enqueueMessage(MText::_('MiwoVideos requires PHP 5.2.x to run, please contact your hosting company.'), 'error');
			return false;
		}

		return MComponentHelper::getParams('com_miwovideos');

	}

	public static function is30() {
		static $status;

		if (!isset($status)) {
			if (version_compare(MVERSION, '3.0.0', 'ge')) {
				$status = true;
			}
			else {
				$status = false;
			}
		}

		return $status;
	}

	public static function is31() {
		static $status;

		if (!isset($status)) {
			if (version_compare(MVERSION, '3.1.0', 'ge')) {
				$status = true;
			}
			else {
				$status = false;
			}
		}

		return $status;
	}

	public static function is32() {
		static $status;

		if (!isset($status)) {
			if (version_compare(MVERSION, '3.2.0', 'ge')) {
				$status = true;
			}
			else {
				$status = false;
			}
		}

		return $status;
	}

	public static function getTable($name) {
		static $tables = array();

		if (!isset($tables[ $name ])) {
			MTable::addIncludePath(MPATH_WP_PLG.'/miwovideos/admin/tables');
			$tables[ $name ] = MTable::getInstance($name, 'Table');
		}

		return $tables[ $name ];
	}

	public function set($name, $value) {
		if (!is_array(self::$data)) {
			self::$data = array();
		}

		$previous = self::get($name);

		self::$data[ $name ] = $value;

		return $previous;
	}

	public function get($name, $default = null) {
		if (!is_array(self::$data) || !isset(self::$data[ $name ])) {
			return $default;
		}

		return self::$data[ $name ];
	}

	public function import($path) {
		require_once(MPATH_WP_PLG.'/miwovideos/admin/'.str_replace('.', '/', $path).'.php');
	}

	public function route($link) {
		if ($this->isDashboard()) {
			if (!strstr($link, 'dashboard=1')) {
				$link .= '&dashboard=1';
			}

			$Itemid = MiwoVideos::getInput()->getInt('Itemid', 0);
			if (!empty($Itemid) and !strstr($link, '&Itemid')) {
				$link .= '&Itemid='.$Itemid;
			}
		}

		return MRoute::_($link, false);
	}

	public function isDashboard() {
		$s = false;

		$app       = MFactory::getApplication();
		$view      = MiwoVideos::getInput()->getCmd('view', '');
		$dashboard = MiwoVideos::getInput()->getInt('dashboard', 0);

		if ($this->app->isSite() and ($view == 'dashboard') or ($dashboard == 1)) {
			$s = true;
		}

		return $s;
	}

	public function checkRequirements($src = 'all') {
		if (version_compare(PHP_VERSION, '5.2.0', '<')) {
			$this->app->enqueueMessage(MText::_('MiwoVideos requires PHP 5.2.x to run, please contact your hosting company.'), 'error');
			return false;
		}

		$pid = $this->config->get('pid');
		if (MIWOVIDEOS_PACK != 'lite' and ($src == 'site') and empty($pid)) {
			if (MiwoVideos::get('utility')->is30()) {
				$uri    = (string)MUri::getInstance();
				$return = urlencode(base64_encode($uri));
				$link   = 'return='.$return;
				$link   = MiwoVideos::get('utility')->route('administrator/index.php?option=com_config&view=component&component=com_miwovideos&'.$link);
				MFactory::getApplication()->enqueueMessage(MText::sprintf('COM_MIWOVIDEOS_CPANEL_STATUS_NOTE_PERSONAL_ID', '<a href="'.$link.'">', '</a>'), 'error');
			}
			else {
				MFactory::getApplication()->enqueueMessage(MText::sprintf('COM_MIWOVIDEOS_CPANEL_STATUS_NOTE_PERSONAL_ID', '<a href="'.MRoute::_('wp-admin/admin.php?page=miwovideos&option=com_miwovideos&view=config').'">', '</a>'), 'error');

			}
			return false;
		}

		return true;
	}

	public function getMiwovideosVersion() {
		static $version;

		if (!isset($version)) {
			$version = $this->getXmlText(MPATH_WP_PLG.'/miwovideos/miwovideos.xml', 'version');
		}

		return (string)$version;
	}

	public function getXmlText($file, $variable) {
		mimport('framework.filesystem.file');

		$value = '';

		if (MFile::exists($file)) {
			$xml = simplexml_load_file($file, 'SimpleXMLElement');

			if (is_null($xml) || !($xml instanceof SimpleXMLElement)) {
				return $value;
			}

			$value = $xml->$variable;
		}

		return $value;
	}

	public function getLatestMiwovideosVersion() {
		static $version;

		if (!isset($version)) {
			$version = $this->getRemoteVersion();
		}

		return $version;
	}

	public function getRemoteVersion() {
		$version = '?.?.?';

		$components = $this->getRemoteData('http://miwisoft.com/index.php?option=com_mijoextensions&view=xml&format=xml&catid=5');

		if (!strstr($components, '<?xml version="1.0" encoding="UTF-8" ?>')) {
			return $version;
		}

		$manifest = simplexml_load_string($components, 'SimpleXMLElement');

		if (is_null($manifest)) {
			return $version;
		}

		$category = $manifest->category;
		if (!($category instanceof SimpleXMLElement) or (count($category->children()) == 0)) {
			return $version;
		}

		foreach ($category->children() as $component) {
			$option      = (string)$component->attributes()->option;
			$compability = (string)$component->attributes()->compability;

			if (($option == 'miwovideos') and ($compability == 'wpall')) {
				$version = trim((string)$component->attributes()->version);
				break;
			}
		}

		return $version;
	}

	public function getRemoteData($url) {
		$user_agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)";
		$data       = false;

		# cURL
		if (extension_loaded('curl')) {
			$process = @curl_init($url);

			@curl_setopt($process, CURLOPT_HEADER, false);
			@curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
			@curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
			@curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
			@curl_setopt($process, CURLOPT_AUTOREFERER, true);
			@curl_setopt($process, CURLOPT_FAILONERROR, true);
			@curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
			@curl_setopt($process, CURLOPT_TIMEOUT, 10);
			@curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 10);
			@curl_setopt($process, CURLOPT_MAXREDIRS, 20);

			$data = @curl_exec($process);

			@curl_close($process);

			return $data;
		}

		# fsockopen
		if (function_exists('fsockopen')) {
			$errno  = 0;
			$errstr = '';

			$url_info = parse_url($url);
			if ($url_info['host'] == 'localhost') {
				$url_info['host'] = '127.0.0.1';
			}

			# Open socket connection
			$fsock = @fsockopen($url_info['scheme'].'://'.$url_info['host'], 80, $errno, $errstr, 5);

			if ($fsock) {
				@fputs($fsock, 'GET '.$url_info['path'].(!empty($url_info['query']) ? '?'.$url_info['query'] : '').' HTTP/1.1'."\r\n");
				@fputs($fsock, 'HOST: '.$url_info['host']."\r\n");
				@fputs($fsock, "User-Agent: ".$user_agent."\n");
				@fputs($fsock, 'Connection: close'."\r\n\r\n");

				# Set timeout
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);

				$data          = '';
				$passed_header = false;
				while (!@feof($fsock)) {
					if ($passed_header) {
						$data .= @fread($fsock, 1024);
					}
					else {
						if (@fgets($fsock, 1024) == "\r\n") {
							$passed_header = true;
						}
					}
				}

				#  Clean up
				@fclose($fsock);

				# Return data
				return $data;
			}
		}

		# fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen')) {
			# Set timeout
			if (ini_get('default_socket_timeout') < 5) {
				ini_set('default_socket_timeout', 5);
			}

			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			@ini_set('user_agent', $user_agent);

			$url = str_replace('://localhost', '://127.0.0.1', $url);

			$handle = @fopen($url, 'r');

			if ($handle) {
				$data = '';
				while (!feof($handle)) {
					$data .= @fread($handle, 8192);
				}

				# Clean up
				@fclose($handle);

				# Return data
				return $data;
			}
		}

		# file_get_contents
		if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			@ini_set('user_agent', $user_agent);
			$data = @file_get_contents($url);

			# Return data
			return $data;
		}

		return $data;
	}

	public function renderModules($position) {
		$modules = MModuleHelper::getModules($position);

		if (count($modules) > 0) {
			$renderer = MFactory::getDocument()->loadRenderer('module');
			$attribs  = array('style' => 'xhtml');

			?>
			<div><?php

			foreach ($modules as $mod) {
				echo $renderer->render($mod, $attribs);
			}

			?></div><?php
		}
	}

	public function getMenu() {
		mimport('framework.application.menu');
		$options = array();

		$menu = MMenu::getInstance('site', $options);

		if (MError::isError($menu)) {
			$null = null;
			return $null;
		}

		return $menu;
	}

	public function getCategories($id, $is_video = false) {
		$categories = array();

		if ($is_video == true) {
			$id = $this->getVideoCategory($id)->id;
		}

		while ($id != 0) {
			$cat = $this->getCategory($id);

			if (empty($cat)) {
				break;
			}

			$categories[] = $cat;

			$id = $cat->parent;
		}

		return $categories;
	}

	public function getVideoCategory($id) {
		static $cache = array();

		if (!isset($cache[ $id ])) {
			$cache[ $id ] = MiwoDB::loadObject("SELECT c.* FROM #__miwovideos_categories AS c, #__miwovideos_video_categories AS vc WHERE vc.video_id = {$id} AND c.id = vc.category_id");
		}

		if (!is_object($cache[ $id ])) {
			$row = MiwoVideos::getTable('MiwovideosCategories');
			$row->load($id);

			$cache[ $id ] = $row;
		}

		return $cache[ $id ];
	}

	public function getCategory($id) {
		static $cache = array();

		if (!isset($cache[ $id ])) {
			$cache[ $id ] = MiwoDB::loadObject("SELECT * FROM #__miwovideos_categories WHERE id = {$id} AND published = 1");
		}

		if (!is_object($cache[ $id ])) {
			$row = MiwoVideos::getTable('MiwovideosCategories');
			$row->load($id);

			$cache[ $id ] = $row;
		}

		return $cache[ $id ];
	}

	public function replaceLoop($search, $replace, $text) {
		$count = 0;

		if (!is_string($text)) {
			return $text;
		}

		while ((strpos($text, $search) !== false) && ($count < 10)) {
			$text = str_replace($search, $replace, $text);
			$count++;
		}

		return $text;
	}

	public function getAccessLevels() {
		static $levels;

		if (!isset($levels)) {
			$levels = MiwoDB::loadObjectList("SELECT id, title FROM #__viewlevels", 'id');
		}

		return $levels;
	}

	public function getLanguages() {
		mimport('framework.language.helper');
		$langs = MLanguageHelper::getLanguages('lang_code');

		return $langs;
	}

	public function getFilesizeFromNumber($size) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',').' '.$units[ $power ];
	}

	public function storeConfig($config) {
		$config = $config->toString();

		$db = MFactory::getDBO();
		$db->setQuery('UPDATE #__options SET `option_value` = '.$db->Quote($config).' WHERE `option_name` = "miwovideos"');
		$db->query();
	}

	public function getParam($text, $param) {
		$params = new MRegistry($text);
		return $params->get($param);
	}

	public function storeParams($table, $id, $db_field, $new_params) {
		$row = MiwoVideos::getTable($table);
		if (!$row->load($id)) {
			return false;
		}

		$params = new MRegistry($row->$db_field);

		foreach ($new_params as $name => $value) {
			$params->set($name, $value);
		}

		$row->$db_field = $params->toString();

		if (!$row->check()) {
			return false;
		}

		if (!$row->store()) {
			return false;
		}
	}

	public function setData($table, $id, $db_field, $new_field) {
		$row = MiwoVideos::getTable($table);
		if (!$row->load($id)) {
			return false;
		}
		$row->$db_field = $new_field;

		if (!$row->check()) {
			return false;
		}

		if (!$row->store()) {
			return false;
		}
	}

	public function getPackageFromUpload($userfile) {
		# Make sure that file uploads are enabled in php
		if (!(bool)ini_get('file_uploads')) {
			$this->app->enqueueMessage(MText::_('WARNINSTALLFILE'), 'error');
			return false;
		}

		# Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			$this->app->enqueueMessage(MText::_('WARNINSTALLZLIB'), 'error');
			return false;
		}

		# If there is no uploaded file, we have a problem...
		if (!is_array($userfile)) {
			$this->app->enqueueMessage(MText::_('No file selected'), 'error');
			return false;
		}

		# Check if there was a problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1) {
			$this->app->enqueueMessage(MText::_('WARNINSTALLUPLOADERROR'), 'error');
			return false;
		}

		# Build the appropriate paths
		$JoomlaConfig = MFactory::getConfig();

		$tmp_dest = $JoomlaConfig->get('tmp_path').'/'.$userfile['name'];

		$tmp_src = $userfile['tmp_name'];

		# Move uploaded file
		mimport('framework.filesystem.file');
		$uploaded = MFile::upload($tmp_src, $tmp_dest);

		if (!$uploaded) {
			$this->app->enqueueMessage(MText::_('COM_MIWOVIDEOS_UPGRADE_PERSONAL_ID_3'), 'error');
			return false;
		}

		# Unpack the downloaded package file
		$package = self::unpack($tmp_dest);

		# Delete the package file
		MFile::delete($tmp_dest);

		return $package;
	}

	public function unpack($p_filename) {
		# Path to the archive
		$archivename = $p_filename;

		# Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		# Clean the paths to use for archive extraction
		$extractdir  = MPath::clean(dirname($p_filename).'/'.$tmpdir);
		$archivename = MPath::clean($archivename);

		$package        = array();
		$package['dir'] = $extractdir;

		# do the unpacking of the archive
		$package['res'] = MArchive::extract($archivename, $extractdir);

		return $package;
	}

	public function getMiwovideosIcon($link, $image, $text) {
		$lang = MFactory::getLanguage();

		$div_class = 'class="icon-wrapper"';
		?>
		<div <?php echo $div_class; ?> style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<img src="<?php echo MURL_MIWOVIDEOS; ?>/admin/assets/images/<?php echo $image; ?>" alt="<?php echo $text; ?>"/>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
	<?php
	}

	public function getPackageFromServer($url) {
		# Make sure that file uploads are enabled in php
		if (!(bool)ini_get('file_uploads')) {
			$this->app->enqueueMessage(MText::_('Your PHP settings does not allow uploads'), 'error');
			return false;
		}

		# Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			$this->app->enqueueMessage(MText::_('The PHP extension ZLIB is not loaded, file cannot be unziped'), 'error');
			return false;
		}

		# Get temp path
		$JoomlaConfig = MFactory::getConfig();

		$tmp_dest = $JoomlaConfig->get('tmp_path');

		$url = str_replace('http://miwisoft.com/', '', $url);
		$url = str_replace('https://miwisoft.com/', '', $url);
		$url = 'http://miwisoft.com/'.$url;

		# Grab the package
		$data = $this->getRemoteData($url);

		if (strpos($data, 'Error') !== false) {
			$this->app->enqueueMessage(MText::_('COM_MIWOVIDEOS_UPGRADE_PERSONAL_ID_3'), 'error');
			return false;
		}

		$target = $tmp_dest.'/miwovideos_upgrade.zip';

		# Write buffer to file
		$written = MFile::write($target, $data);

		if (!$written) {
			$this->app->enqueueMessage(MText::_('COM_MIWOVIDEOS_UPGRADE_TEMP_PATH'), 'error');
			return false;
		}

		$p_file = basename($target);

		# Was the package downloaded?
		if (!$p_file) {
			$this->app->enqueueMessage(MText::_('COM_MIWOVIDEOS_UPGRADE_INVALID_PID'), 'error');
			return false;
		}

		# Unpack the downloaded package file
		$package = self::unpack($tmp_dest.'/'.$p_file);

		if (!$package) {
			$this->app->enqueueMessage(MText::_('COM_MIWOVIDEOS_UPGRADE_UNPACKED_ERROR'), 'error');
			return false;
		}

		# Delete the package file
		MFile::delete($tmp_dest.'/'.$p_file);

		return $package;
	}

	public function getUsers() {
		return MiwoDB::LoadObjectList("SELECT * FROM #__users");
	}

	public function triggerContentPlg($text) {
		$item               = new stdClass();
		$item->id           = null;
		$item->rating       = null;
		$item->rating_count = null;
		$item->text         = $text;

		$limitstart = MRequest::getInt('limitstart');

		$this->trigger('onContentPrepare', array(
			'com_miwovideos.video',
			&$item,
			&$this->config,
			$limitstart
		), 'content');

		return $item->text;
	}

	public function trigger($function, $args = array(), $folder = 'miwovideos') {
		mimport('framework.plugin.helper');

		MPluginHelper::importPlugin($folder);
		$dispatcher = MDispatcher::getInstance();
		$result     = $dispatcher->trigger($function, $args);

		return $result;
	}

	public function getPlugin($name, $folder = 'miwovideos') {
		static $cache = array();

		if (!isset($cache[ $folder ][ $name ])) {
			$file = MPATH_MIWI.'/plugins/plg_'.$folder.'_'.$name.'/'.$name.'.php';

			if (!$this->plgEnabled($folder, $name) or !file_exists($file)) {
				$cache[ $folder ][ $name ] = null;

				return $cache[ $folder ][ $name ];
			}

			require_once($file);

			$plugin = MPluginHelper::getPlugin($folder, $name);
			$params = new MRegistry(@$plugin->params);

			$subject = MDispatcher::getInstance();

			$config['name']   = $name;
			$config['type']   = $folder;
			$config['params'] = $params;
			$class            = 'plg'.ucfirst($folder).ucfirst($name);

			$cache[ $folder ][ $name ] = new $class($subject, $config);
		}

		return $cache[ $folder ][ $name ];
	}

	public function plgEnabled($folder, $name) {
		static $status = array();

		if (!isset($status[ $folder ][ $name ])) {
			mimport('framework.plugin.helper');
			$status[ $folder ][ $name ] = MPluginHelper::isEnabled($folder, $name);
		}

		return $status[ $folder ][ $name ];
	}

	public function isAjax($output = '') {
		$is_ajax = false;

		$tmpl   = MRequest::getWord('tmpl');
		$format = MRequest::getWord('format');

		if ($tmpl == 'component' or $format == 'raw') {
			$is_ajax = true;
		}
		else if (!empty($output)) {
			if ($this->isJson($output)) {

				$is_ajax = true;

				MRequest::setVar('format', 'raw');
				MRequest::setVar('tmpl', 'component');
			}
		}

		return $is_ajax;
	}

	public function isJson($string) {
		$status = false;

		if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
			$a      = json_decode($string);
			$status = (json_last_error() == JSON_ERROR_NONE);
		}
		else {
			if (substr($string, 0, 8) == '{"guid":') {
				$status = true;
			}
		}

		return $status;
	}

	public function isMiwosefInstalled() {
		static $status;

		if (!isset($status)) {
			$status = true;

			$file = MPATH_WP_PLG.'/miwosef/admin/library/miwosef.php';
			if (!file_exists($file)) {
				$status = false;

				return $status;
			}

			require_once($file);

			if (Miwosef::getConfig()->mode == 0) {
				$status = false;
			}
		}

		return $status;
	}

	public function isSh404sefInstalled() {
		static $status;

		if (!isset($status)) {
			$status = true;

			$file = MPATH_ADMINISTRATOR.'/components/com_sh404sef/sh404sef.class.php';
			if (!file_exists($file)) {
				$status = false;

				return $status;
			}

			require_once($file);

			if (Sh404sefFactory::getConfig()->Enabled == 0) {
				$status = false;
			}
		}

		return $status;
	}

	public function isJoomsefInstalled() {
		static $status;

		if (!isset($status)) {
			$status = true;

			$file = MPATH_ADMINISTRATOR.'/components/com_sh404sef/classes/config.php';
			if (!file_exists($file)) {
				$status = false;

				return $status;
			}

			require_once($file);

			if (!SEFConfig::getConfig()->enabled) {
				$status = false;
			}
		}

		return $status;
	}

	public function getRadioList($name, $selected, $attrs = '', $id = false, $old_style = false, $text_1 = 'MYES', $text_0 = 'MNO') {
		if (MiwoVideos::is30()) {
			if (empty($attrs)) {
				$attrs = 'class="inputbox" size="2"';
			}

			if ($old_style == true) {
				$arr = array(
					MHtml::_('select.option', 2, MText::_($text_1)),
					MHtml::_('select.option', 1, MText::_($text_0))
				);
			}
			else {
				$arr = array(
					MHtml::_('select.option', 1, MText::_($text_1)),
					MHtml::_('select.option', 0, MText::_($text_0))
				);
			}

			$output = MHtml::_('select.radiolist', $arr, $name, $attrs, 'value', 'text', (int)$selected, $id);

			$html = '<fieldset class="radio btn-group">';
			$html .= str_replace(array('<div class="controls">', '</div>'), '', $output);
			$html .= '</fieldset>';
		}
		else {
			$html = MHtml::_('select.booleanlist', $name, 'class="inputbox"', $selected);
		}

		return $html;
	}

	public function buildCategoryDropdown($selected, $name = "parent", $onChange = true) {
		$db = MFactory::getDBO();
		$db->setQuery("SELECT id, parent, parent AS parent_id, title FROM #__miwovideos_categories");
		$rows = $db->loadObjectList();

		$children = array();
		if ($rows) {
			# first pass - collect children
			foreach ($rows as $v) {
				$pt   = $v->parent;
				$list = @$children[ $pt ] ? $children[ $pt ] : array();
				array_push($list, $v);
				$children[ $pt ] = $list;
			}
		}

		$list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		$options   = array();
		$options[] = MHtml::_('select.option', '0', MText::_('COM_MIWOVIDEOS_SELECT_CATEGORY'));
		foreach ($list as $item) {
			$options[] = MHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'.$item->treename);
		}

		if ($onChange) {
			return MHtml::_('select.genericlist', $options, $name, array(
					'option.text.toHtml' => false,
					'option.text'        => 'text',
					'option.value'       => 'value',
					'list.attr'          => 'class="inputbox" ',
					'list.select'        => $selected
				)
			);
		}
		else {
			return MHtml::_('select.genericlist', $options, $name, array(
					'option.text.toHtml' => false,
					'option.text'        => 'text',
					'option.value'       => 'value',
					'list.attr'          => 'class="inputbox" ',
					'list.select'        => $selected
				)
			);
		}
	}

	public function buildParentCategoryDropdown($row) {
		$db = MFactory::getDBO();

		$sql = "SELECT id, parent, parent AS parent_id, title FROM #__miwovideos_categories WHERE id <> 1";

		if ($row->id) {
			$sql .= ' AND id != '.$row->id;
		}

		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		if (!$row->parent) {
			$row->parent = 0;
		}

		$children = array();
		if ($rows) {
			# first pass - collect children
			foreach ($rows as $v) {
				$pt   = $v->parent;
				$list = @$children[ $pt ] ? $children[ $pt ] : array();
				array_push($list, $v);
				$children[ $pt ] = $list;
			}
		}

		$list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		$options   = array();
		$options[] = MHtml::_('select.option', '0', MText::_('MGLOBAL_ROOT_PARENT'));
		foreach ($list as $item) {
			$options[] = MHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'.$item->treename);
		}

		return MHtml::_('select.genericlist', $options, 'parent', array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => ' class="inputbox" ',
				'list.select'        => $row->parent
			)
		);
	}

	public function resizeImage($srcFile, $desFile, $thumbWidth, $thumbHeight, $quality) {
		$imgTypes = array(
			1  => 'GIF',
			2  => 'JPG',
			3  => 'PNG',
			4  => 'SWF',
			5  => 'PSD',
			6  => 'BMP',
			7  => 'TIFF',
			8  => 'TIFF',
			9  => 'JPC',
			10 => 'JP2',
			11 => 'JPX',
			12 => 'JB2',
			13 => 'SWC',
			14 => 'IFF'
		);
		$imgInfo  = getimagesize($srcFile);

		if ($imgInfo == null) {
			$this->app->enqueueMessage(MText::_('COM_MIWOVIDEOS_IMAGE_NOT_FOUND', 'error'));
			return false;
		}

		$type             = strtoupper($imgTypes[ $imgInfo[2] ]);
		$gdSupportedTypes = array('JPG', 'PNG', 'GIF');

		if (!in_array($type, $gdSupportedTypes)) {
			$this->app->enqueueMessage(MText::_('COM_MIWOVIDEOS_ONLY_SUPPORT_TYPES'), 'error');
			return false;
		}

		$srcWidth  = $imgInfo[0];
		$srcHeight = $imgInfo[1];

		//Should canculate the ration
		$ratio     = max($srcWidth / $thumbWidth, $srcHeight / $thumbHeight, 1.0);
		$desWidth  = (int)$srcWidth / $ratio;
		$desHeight = (int)$srcHeight / $ratio;

		$gd_version = $this->getGDVersion();

		if ($gd_version <= 0) {
			//Simply copy the source to target folder
			mimport('framework.filesystem.file');
			MFile::copy($srcFile, $desFile);
			return false;
		}
		else if ($gd_version == 1) {
			if ($type == 'JPG') {
				$srcImage = imagecreatefromjpeg($srcFile);
			}
			elseif ($type == 'PNG') {
				$srcImage = imagecreatefrompng($srcFile);
			}
			else {
				$srcImage = imagecreatefromgif($srcFile);
			}

			$desImage = imagecreate($desWidth, $desHeight);

			imagecopyresized($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
			imagejpeg($desImage, $desFile, $quality);
			imagedestroy($srcImage);
			imagedestroy($desImage);
		}
		else {
			if (!function_exists('imagecreatefromjpeg')) {
				echo MText::_('GD_LIB_NOT_INSTALLED');
				return false;
			}

			if (!function_exists('imagecreatetruecolor')) {
				echo MText::_('GD2_LIB_NOT_INSTALLED');
				return false;
			}

			if ($type == 'JPG') {
				$srcImage = imagecreatefromjpeg($srcFile);
			}
			elseif ($type == 'PNG') {
				$srcImage = imagecreatefrompng($srcFile);
			}
			else {
				$srcImage = imagecreatefromgif($srcFile);
			}

			if (!$srcImage) {
				echo MText::_('JA_INVALID_IMAGE');
				return false;
			}

			$desImage = imagecreatetruecolor($desWidth, $desHeight);

			imagecopyresampled($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
			imagejpeg($desImage, $desFile, $quality);
			imagedestroy($srcImage);
			imagedestroy($desImage);
		}

		return true;
	}

	public function getGDVersion($user_ver = 0) {
		if (!extension_loaded('gd')) {
			return 0;
		}

		static $gd_ver = 0;

		# just accept the specified setting if it's 1.
		if ($user_ver == 1) {
			$gd_ver = 1;
			return $gd_ver;
		}

		# use static variable if function was cancelled previously.
		if (($user_ver != 2) and ($gd_ver > 0)) {
			return $gd_ver;
		}

		# use the gd_info() function if posible.
		if (function_exists('gd_info')) {
			$ver_info = gd_info();
			$match    = null;
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];

			return $gd_ver;
		}

		# if phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
			if ($user_ver == 2) {
				$gd_ver = 2;
				return $gd_ver;
			}
			else {
				$gd_ver = 1;
				return $gd_ver;
			}
		}

		# ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info  = stristr($info, 'gd version');
		$match = null;
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];

		return $gd_ver;
	}

	public function getUserInputbox($user_id, $field_name = 'user_id') {
		$dashboard = '';
		if ($this->isDashboard()) {
			$dashboard = '&amp;dashboard=1';
		}

		# Initialize variables.
		$html = array();
		$link = MRoute::_('index.php?option=com_miwovideos&view=users&layout=modal&field=user_id'.$dashboard);

		# Initialize some field attributes.
		$attr = ' class="inputbox"';

		# Load the modal behavior script.
		MHtml::_('behavior.modal', 'a.modal_user_id');

		# Build the script.
		$script   = array();
		$script[] = '	function jSelectUser_user_id(id, title) {';
		$script[] = '		var old_id = document.getElementById("'.$field_name.'").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("'.$field_name.'").value = id;';
		$script[] = '			document.getElementById("user_id_name").value = title;';
		$script[] = '		}';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		# Add the script to the document head.
		MFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		# Load the current username if available.
		$table = MTable::getInstance('user');
		if ($user_id) {
			$table->load($user_id);
		}
		else {
			$table->user_login = '';
		}

		# Create a dummy text field with the user name.
		$html[] = '<div class="fltlft">';
		$html[] = '<input type="text" id="user_id_name"'.' value="'.htmlspecialchars($table->user_login, ENT_COMPAT, 'UTF-8').'"'.' disabled="disabled"'.$attr.' />&nbsp;&nbsp;';
		$html[] = '</div>';

		# Create the user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '<div class="blank">';
		$html[] = '<a class="modal_user_id btn button-primary" title="'.MText::_('MLIB_FORM_CHANGE_USER').'"'.' href="'.$link.'"'.' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html[] = '	'.MText::_('MLIB_FORM_CHANGE_USER').'</a>';
		$html[] = '</div>';
		$html[] = '</div>';

		# Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="'.$field_name.'" name="'.$field_name.'" value="'.$user_id.'" />';

		return implode("\n", $html);
	}

	public function getChannelInputbox($channel_id, $field_name = 'created_by') {
		$dashboard = '';
		if ($this->isDashboard()) {
			$dashboard = '&amp;dashboard=1';
		}

		# Initialize variables.
		$html = array();
		$link = MRoute::_('index.php?option=com_miwovideos&view=channels&layout=modal&tmpl=component&field=channel_id'.$dashboard);

		# Initialize some field attributes.
		$attr = ' class="inputbox"';

		# Load the modal behavior script.
		MHtml::_('behavior.modal', 'a.modal_channel_id');

		# Build the script.
		$script   = array();
		$script[] = '	function jSelectChannel_channel_id(id, title) {';
		$script[] = '		var old_id = document.getElementById("'.$field_name.'").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("'.$field_name.'").value = id;';
		$script[] = '			document.getElementById("channel_id_name").value = title;';
		$script[] = '		}';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		# Add the script to the document head.
		MFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		# Load the current username if available.
		$table = MTable::getInstance('MiwovideosChannels', 'Table');
		if ($channel_id) {
			$table->load($channel_id);
		}
		else {
			$table->name = '';
		}

		# Create a dummy text field with the user name.
		$html[] = '<div class="fltlft">';
		$html[] = '<input type="text" id="channel_id_name"'.' value="'.htmlspecialchars($table->title, ENT_COMPAT, 'UTF-8').'"'.' disabled="disabled"'.$attr.' />&nbsp;&nbsp;';
		$html[] = '</div>';

		# Create the user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '<div class="blank">';
		$html[] = '<a class="modal_channel_id btn button-primary" title="'.MText::_('COM_MIWOVIDEOS_CHANGE_CHANNEL').'"'.' href="'.$link.'"'.' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html[] = '	'.MText::_('COM_MIWOVIDEOS_CHANGE_CHANNEL').'</a>';
		$html[] = '</div>';
		$html[] = '</div>';

		# Create the real field, hidden, that stored the channel id.
		if ($channel_id) {
			$html[] = '<input type="hidden" id="'.$field_name.'" name="'.$field_name.'" value="'.$channel_id.'" />';
		}
		else {
			$user_id    = MFactory::getUser()->id;
			$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
			$html[]     = '<input type="hidden" id="'.$field_name.'" name="'.$field_name.'" value="'.$channel_id.'" />';
		}
		return implode("\n", $html);
	}

	function secondsToTime($seconds, $isString = false) {
		$ret = "";
		/*** get the days ***/
		$days = intval(intval($seconds) / (3600 * 24));
		if ($days > 0) {
			$days == 1 ? $ret .= "$days ".MText::_('COM_MIWOVIDEOS_DAY')." " : $ret .= "$days ".MText::_('COM_MIWOVIDEOS_DAYS')." ";
		}
		/*** get the hours ***/
		$hours = (intval($seconds) / 3600) % 24;
		if ($isString) {
			if ($hours > 0) {
				$hours == 1 ? $ret .= "$hours ".MText::_('COM_MIWOVIDEOS_HOUR')." " : $ret .= "$hours ".MText::_('COM_MIWOVIDEOS_HOURS')." ";
			}
		}
		else {
			if ($hours > 0) {
				$ret .= $hours;
			}
		}

		/*** get the minutes ***/
		$minutes = (intval($seconds) / 60) % 60;
		if ($isString) {
			if ($minutes > 0 && $days == 0) {
				$minutes == 1 ? $ret .= "$minutes ".MText::_('COM_MIWOVIDEOS_MINUTE')." " : $ret .= "$minutes ".MText::_('COM_MIWOVIDEOS_MINUTES')." ";
			}
		}
		else {
			if ($minutes >= 0 and $minutes < 10 and !empty($ret)) {
				$ret .= ':0'.$minutes;
			}
			elseif (empty($ret)) {
				$ret .= $minutes;
			}
			elseif (!empty($ret) and $minutes >= 10) {
				$ret .= ':'.$minutes;
			}
		}

		/*** get the seconds ***/
		$seconds = intval($seconds) % 60;
		if ($isString) {
			if ($seconds > 0 && $days == 0 && $hours == 0) {
				$seconds == 1 ? $ret .= "$seconds ".MText::_('COM_MIWOVIDEOS_SECOND')." " : $ret .= "$seconds ".MText::_('COM_MIWOVIDEOS_SECONDS')." ";
			}
		}
		else {
			if ($seconds >= 0 and $seconds < 10) {
				$ret .= ':0'.$seconds;
			}
			else {
				$ret .= ':'.$seconds;
			}
		}

		return $ret;
	}

	function redirectWithReturn() {
			    $login_url = wp_login_url($_SERVER['HTTP_REFERER'], true);
        return htmlspecialchars_decode(MRoute::_($login_url));	}

	public function getActiveUrl() {
		return $this->cleanUrl(MFactory::getURI()->toString());
	}

	public function cleanUrl($url) {
		$url = $this->cleanText($url);

		$bad_chars = array('#', '>', '<', '\\', '="', 'px;', 'onmouseover=');
		$url       = trim(str_replace($bad_chars, '', $url));

		mimport('framework.filter.input');
		MFilterInput::getInstance(array('br', 'i', 'em', 'b', 'strong'), array(), 0, 0, 1)->clean($url);

		return $url;
	}

	public function cleanText($text) {
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		$text = preg_replace('#<script[^>]*>.*?</script>#si', ' ', $text);
		$text = preg_replace('#<style[^>]*>.*?</style>#si', ' ', $text);
		$text = preg_replace('#<!.*?(--|]])>#si', ' ', $text);
		$text = preg_replace('#<[^>]*>#i', ' ', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace("'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text);

		$text = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', ' ', $text);

		$text = preg_replace('/\s\s+/', ' ', $text);
		$text = preg_replace('/\n\n+/s', ' ', $text);
		$text = preg_replace('/\s/u', ' ', $text);

		$text = strip_tags($text);

		return $text;
	}

	public function getHost($url) {
		$host    = null;
		$pattern = '`.*?((http|https|ftp)://[\w#$&+,\/:;=?@.-]+)[^\w#$&+,\/:;=?@.-]*?`i';
		if (preg_match($pattern, $url, $matches)) {
			$url        = $matches[1];
			$host       = parse_url($url, PHP_URL_HOST);
			$patterns[] = '#^www\.|\.com$#';
			$patterns[] = '#^v\.|\.com$#'; //v.youku.com
			$patterns[] = '#^www\.|\.co$#'; // vine.co
			$patterns[] = '#^www\.|\.tv$#'; // wat.tv, blip.tv
			$patterns[] = '#\.|\.com$#'; //video.foxnews.com
			$patterns[] = '#^www\.|\.be$#'; //youtu.be
			$host       = preg_replace($patterns, '$1', $host);
			if ($host == 'youtu') { //youtu.be
				$host = 'youtube';
			}

			if (strpos($host, 'vidsio') !== false) {
				$host = 'sproutvideo';
			}

			if (strpos($host, 'amazonaws') !== false) {
				$host = null;
			}
		}

		return $host;
	}

	public function getVideoSize($id, $src) {
		if (strpos($src, 'http://') !== false or strpos($src, 'https://') !== false) {
			$location = $src;
			$file_headers = get_headers($src);
			if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
				MiwoVideos::log(MText::_('COM_MIWOVIDEOS_ERROR_SOURCE_VIDEO_NOT_EXIST'));
				return false;
			}
		}
		else {
			$location = MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$id.'/orig/'.$src;
			if (!file_exists($location)) {
				MiwoVideos::log(MText::_('COM_MIWOVIDEOS_ERROR_SOURCE_VIDEO_NOT_EXIST'));
				return false;
			}
		}

		if (substr(PHP_OS, 0, 3) == "WIN") {
			$command = "\"".$this->config->get('ffmpeg_path', '/usr/bin/ffmpeg')."\" -i $location 2>&1";
			MiwoVideos::exec($command, $output);
		}
		else {
			$command = $this->config->get('ffmpeg_path', '/usr/bin/ffmpeg')." -i $location 2>&1";
			MiwoVideos::exec($command, $output);
		}

		MiwoVideos::log('FFmpeg : '.$command);
		MiwoVideos::log($output);

		$flatoutput = is_array($output) ? implode("\n", $output) : $output;
		if (empty($flatoutput)) {
			MiwoVideos::log('Flatoutput is empty');
			return false;
		}
		else {
			$pos = strpos($flatoutput, "No such file or directory");
			if ($pos !== false) {
				MiwoVideos::log('No such file or directory');
				return false;
			}

			$pos = strpos($flatoutput, "not found");
			if ($pos !== false) {
				MiwoVideos::log('Not found');
				return false;
			}

			$pos = strpos($flatoutput, "Permission denied");
			if ($pos !== false) {
				MiwoVideos::log('Permission denied');
				return false;
			}
		}
		$input_height = 0;

		// Get original size
		if (preg_match('/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tbr/', implode("\n", $output), $matches)) {
			$input_height = $matches[2];
		}
		elseif (preg_match('/Stream.*Video:.* (\d+)x(\d+).* (\d+\.\d+|\d+) tb/', implode("\n", $output), $matches)) {
			$input_height = $matches[2];
		}

		$sizes      = array(1080, 720, 480, 360, 240);
		$i          = 0;
		foreach ($sizes as $size) {
			if ($input_height >= $size) {
				switch ($i) {
					case 0:
						$input_size = 1080;
						break;
					case 1:
						$input_size = 720;
						break;
					case 2:
						$input_size = 480;
						break;
					case 3:
						$input_size = 360;
						break;
					case 4:
						$input_size = 240;
						break;
					default:
						break;
				}
				return $input_size;
			}
			$i++;
		}
		if (empty($input_size)) {
			return 120;
		}
	}

	public function getSize($process_type) {
		$size = null;
		switch ($process_type) {
			case 7:
			case 12:
			case 17:
			case 26:
				$size = 240;
				break;
			case 8:
			case 13:
			case 18:
			case 27:
				$size = 360;
				break;
			case 9:
			case 14:
			case 19:
			case 28:
				$size = 480;
				break;
			case 10:
			case 15:
			case 20:
			case 29:
				$size = 720;
				break;
			case 11:
			case 16:
			case 21:
			case 30:
				$size = 1080;
				break;
			case 100: // HTML5 type
				//$size = $this->getVideoSize();
				break;
		}
		return $size;
	}

	public function getThumbPath($id, $view, $thumb, $size = null, $type = 'url') {
		if (empty($thumb)) {
			if ($view == 'channels') {
				$ret = '/miwovideos/images/'.$view.'/default/default.jpg';
			}
			else {
				if (empty($size)) {
					$size = $this->getThumbSize($this->config->get('thumb_size'));
				}
				$ret = '/miwovideos/images/default/default'.$size.'.jpg';
			}
		}
		else {
			if (empty($size)) {
				$size = $this->getThumbSize($this->config->get('thumb_size'));
			}
			if (file_exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$view.'/'.$id.'/'.$size.'/'.$thumb)) {
				$ret = '/miwovideos/images/'.$view.'/'.$id.'/'.$size.'/'.$thumb;
			}
			else {
				if (strpos($thumb, 'http://') !== false or strpos($thumb, 'https://') !== false) {
					$ret = $thumb;
				}
				else {
					$_sizes = array(75, 100, 240, 500, 640, 1024);
					while (current($_sizes) !== (int)$size) {
						next($_sizes);
					}

					$val = prev($_sizes);
					if (file_exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$view.'/'.$id.'/'.$val.'/'.$thumb)) {
						$ret = '/miwovideos/images/'.$view.'/'.$id.'/'.$val.'/'.$thumb;
						$key = array_search($val, $_sizes);
						unset($_sizes[ $key ]);
					}
					next($_sizes);
					$val = next($_sizes);
					if (!isset($ret) and file_exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$view.'/'.$id.'/'.$val.'/'.$thumb)) {
						$ret = '/miwovideos/images/'.$view.'/'.$id.'/'.$val.'/'.$thumb;
						$key = array_search($val, $_sizes);
						unset($_sizes[ $key ]);
					}

					if (!isset($ret)) {
						reset($_sizes);
						foreach ($_sizes as $_size) {
							if (!isset($ret) and file_exists(MIWOVIDEOS_UPLOAD_DIR.'/images/'.$view.'/'.$id.'/'.$_size.'/'.$thumb)) {
								$ret = '/miwovideos/images/'.$view.'/'.$id.'/'.$_size.'/'.$thumb;
								break;
							}

							if ($view == 'channels') {
								$ret = '/miwovideos/images/channels/default/default.jpg';
							}
							else {
								$ret = '/miwovideos/images/default/default'.$size.'.jpg';
							}
						}
					}
				}
			}
		}

		if (strpos($thumb, 'http://') === false and strpos($thumb, 'https://') === false) {
			switch ($type) {
				case 'url':
					$ret = MURL_MEDIA.$ret;
					break;
				case 'path':
					$ret = MPATH_MEDIA.$ret;
					break;
				case 'default':
					$ret = $ret;
					break;
			}
		}


		return $ret;
	}

	public function getThumbSize($size) {
		switch ($size) {
			case 1: // Very Small Image
				$size = 75;
				break;
			case 2: // Thumbnail Image
				$size = 100;
				break;
			case 3: // Small Image
				$size = 240;
				break;
			case 4: // Medium Image
				$size = 500;
				break;
			case 5: // Large Image
				$size = 640;
				break;
			case 6: // Very Large Image
				$size = 1024;
				break;
		}
		return $size;
	}

	public function getVideoFilePath($id, $size, $source, $type = 'default', $protectUrl = true) {
		$ret = '';
		if (strpos($source, 'http://') !== false or strpos($source, 'https://') !== false) {
			$ret = $source;
		}
		elseif (file_exists(MIWOVIDEOS_UPLOAD_DIR.'/videos/'.$id.'/'.$size.'/'.$source)) {
			if (!MFactory::getApplication()->isAdmin() and $this->config->get('protect_files', 0) and $protectUrl) {
				$ret = $this->protectUrl($id, $size, $source);
			}
			else {
				switch ($type) {
					case 'url':
						$ret = MUri::root().'wp-content/uploads/miwovideos/videos/'.$id.'/'.$size.'/'.$source;
						break;
					case 'path':
						$ret = MPATH_MEDIA.'/miwovideos/videos/'.$id.'/'.$size.'/'.$source;
						break;
					case 'default':
						$ret = '/miwovideos/videos/'.$id.'/'.$size.'/'.$source;
						break;
				}
			}
		}

		return $ret;
	}

	public function protectUrl($id, $size, $source) {
		$time   = time();
		$expire = md5(MFactory::getConfig()->get('secret').time());
		$type   = MFile::getExt($source);

		return MRoute::_('index.php?option=com_miwovideos&view=video&task=getVideoFile&video_id='.$id.'&size='.$size.'&type='.$type.'&time='.$time.'&expire='.$expire.'&format=raw');
	}

	public function getWatchLaterButton($id, $override = null) {
		$watch_later_id = $this->getWatchlater()->id;

		$result = null;

		if (!empty($watch_later_id)) {
			$result = $this->checkVideoInPlaylists($watch_later_id, $id);
		}

		if (empty($result) or empty($watch_later_id)) {
			$html = '<div class="video_watch_later_button miwovideos_video'.$id.'" onclick="return false;">';
			$html .= '<div class="video_watch_later miwovideos_watch_later'.$watch_later_id.'">';
			if ($override == 'vimeo') {
				$html .= '<i class="fa fa-clock-o"></i>';
			}
			$html .= '</div>';
			$html .= '</div>';
		}
		else {
			$html = '<div class="video_added_button miwovideos_video'.$id.'" onclick="return false;">';
			$html .= '<div class="video_added miwovideos_watch_later'.$watch_later_id.'"></div>';
			if ($override == 'vimeo') {
				$html .= '<i class="fa fa-clock-o"></i>';
			}
			$html .= '</div>';
		}

		return $html;
	}

	public function getWatchlater() {
		$db         = MFactory::getDBO();
		$channel_id = MiwoVideos::get('channels')->getDefaultChannel()->id;
		if (!$channel_id) {
			$watch_later     = new stdClass();
			$watch_later->id = null;
			return $watch_later;
		}

		$sql = "SELECT * FROM #__miwovideos_playlists WHERE channel_id = {$channel_id} AND type = 1";
		$db->setQuery($sql);
		$watch_later = $db->loadObject();

		return $watch_later;
	}

	public function checkVideoInPlaylists($playlist_id, $video_id) {
		$result = MiwoDB::loadResult("SELECT * FROM #__miwovideos_playlist_videos WHERE playlist_id = {$playlist_id} AND video_id = {$video_id}");

		return $result;
	}

	public function hitsCounter($item_type) {
		$item_id = MRequest::getInt($item_type.'_id');
		MiwoVideos::get('controller')->increaseField($item_type.'s', 'hits', 1, $item_id);
		return true;
	}

	public function log($message, $priority) {
		if (!$this->config->get('log') or !$message) {
			return false;
		}

		/*MLog::add($message, $priority, 'miwovideos');*/
		$trace = debug_backtrace();
		$file  = MPATH_CACHE.'/miwovideos.log';
		if (is_array($message)) {
			foreach ($message as $msg) {
				file_put_contents($file, date('Y-m-d G:i:s').' - '.$msg.' line '.$trace[1]['line'].' on '.$trace[1]['file']."\n", FILE_APPEND);
			}
		}
		else {
			file_put_contents($file, date('Y-m-d G:i:s').' - '.$message.' line '.$trace[1]['line'].' on '.$trace[1]['file']."\n", FILE_APPEND);
		}
	}

	public function getFfmpegVersion() {
		if(!function_exists('exec')) {
			return false;
		}
		$ffmpeg_version = false;

		if (substr(PHP_OS, 0, 3) == "WIN") {
			$command = "\"".$this->config->get('ffmpeg_path', 'C:\ffmpeg\bin\ffmpeg.exe')."\" 2>&1";
		}
		else {
			$command = "".$this->config->get('ffmpeg_path', '/usr/local/bin/ffmpeg')."  2>&1";
		}

		MiwoVideos::exec($command, $output);
		MiwoVideos::log('FFmpeg : '.$command);
		MiwoVideos::log($output);

		if (preg_match('#FFmpeg version(.*?), Copyright#', implode("\n", $output), $matches)) {
			$ffmpeg_version = trim($matches[1]);
		}
		elseif (preg_match('#ffmpeg version(.*?) Copyright#i', implode("\n", $output), $matches)) {
			$ffmpeg_version = trim($matches[1]);
		}

		return $ffmpeg_version;
	}

	public function findOption() {
		$option = strtolower(MRequest::getCmd('option'));

		$user = MFactory::getUser();
		if (($user->get('guest')) || !$user->authorise('core.login.admin')) {
			$option = 'com_login';
		}

		if (empty($option)) {
			$option = 'com_cpanel';
		}

		if ($args = @$GLOBALS['argv']) { // If script runs on cli
			$option = 'com_miwovideos';
		}

		MRequest::setVar('option', $option);
		return $option;
	}

	public function backupDB($query, $file_name, $fields, $line) {
		$sql_data = '';

		$rows = MiwoDB::loadObjectList($query);

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$values = array();
				foreach ($fields as $field) {
					if (isset($row->$field)) {
						$values[] = "'".self::_cleanBackupFields($row->$field)."'";
					}
					else {
						$values[] = "''";
					}
				}
				$sql_data .= $line." VALUES (".implode(', ', $values).");\n";;
			}
		}
		else {
			return false;
		}

		if (!headers_sent()) {
			// flush the output buffer
			while (ob_get_level() > 0) {
				ob_end_clean();
			}

			ob_start();
			header('Expires: 0');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
			header('Pragma: public');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Accept-Ranges: bytes');
			header('Content-Length: '.strlen($sql_data));
			header('Content-Type: Application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$file_name.'"');
			header('Connection: close');

			echo($sql_data);

			ob_end_flush();
			die();
			return true;
		}
		else {
			return false;
		}
	}

	public function _cleanBackupFields($text) {
		$text = str_replace(array('\r\n', '\r', '\n', '\t', '\n\n', '`', '”', '“', '¿', '\0', '\x0B'), ' ', $text);
		$text = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', ' ', $text);
		$text = preg_replace('/\s/u', ' ', $text);
		$text = stripslashes($text);
		$text = self::replaceSpecialChars($text);
		$text = str_replace('\\', '\\', $text);

		return $text;
	}

	public function replaceSpecialChars($text, $reverse = false) {
		if (is_string($text)) {
			if (!$reverse) {
				$text = str_replace("\'", "'", $text);
				$text = addslashes($text);
			}
			else {
				$text = stripslashes($text);
			}
		}

		return $text;
	}

	public function backgroundTask($id, $filename) {
		if (!$this->config->get('auto_process_videos')) {
			$ret = MiwoVideos::get('videos')->convertToHtml5($id, $filename);
		}
		else {
			$cli = MPATH_MIWI.'/cli/miwovideoscli.php';
			if (substr(PHP_OS, 0, 3) != "WIN") {
				// @TODO Log if throw an error
				$command = "env -i ".$this->config->get('php_path', '/usr/bin/php')." $cli convertToHtml5 ".$id." ".$filename." > /dev/null 2>&1 &";
			}
			else {
				MiwoVideos::exec('where php.exe', $php_path);
				// @TODO Log if throw an error
				$command = $this->config->get('php_path', $php_path)." $cli convertToHtml5 ".$id." ".$filename." NUL";
			}

			MiwoVideos::exec($command, $output, $err);
			MiwoVideos::log('CLI : '.$command);
			MiwoVideos::log($output);
			MiwoVideos::log($err);
			if ($err) {
				$ret = false;
			} else {
				$ret = true;
			}
		}

		return $ret;
	}

	public function agoDateFormat($date) {
		$time = time() - strtotime($date);

		$tokens = array(
			31536000 => 'COM_MIWOVIDEOS_RELATIVE_YEARS',
			2592000  => 'COM_MIWOVIDEOS_RELATIVE_MONTHS',
			604800   => 'COM_MIWOVIDEOS_RELATIVE_WEEKS',
			86400    => 'COM_MIWOVIDEOS_RELATIVE_DAYS',
			3600     => 'COM_MIWOVIDEOS_RELATIVE_HOURS',
			60       => 'COM_MIWOVIDEOS_RELATIVE_MINUTES',
			1        => 'COM_MIWOVIDEOS_RELATIVE_SECONDS'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) {
				continue;
			}
			$diff = round($time / $unit);
			return MText::plural($text, $diff);
		}
	}

	public function exec($command, &$output = null, &$return_var = null) {
		if (!function_exists('exec')) {
			return;
		}
		@exec($command, $output, $return_var);
	}
}