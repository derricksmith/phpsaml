<?php
class PluginPhpsamlUpdate {
	static public $installed_version;
	
	/**
     * Constructor
    **/
	function __construct() {
		self::init();
	}
	
	public static function init() {
		self::get_installed_version();
		self::do_upgrade();
	}
	
	public static function get_installed_version(){
		global $DB;
		$query = "SELECT * FROM `glpi_plugin_phpsaml_configs` WHERE id = '1'";
		$result = $DB->query($query);
		$array = $result->fetch_array();
		if (array_key_exists('version', $array)) {
			$query = "SELECT * FROM `glpi_plugin_phpsaml_configs` WHERE id = '1'";
			$result = $DB->query($query);
			$array = $result->fetch_array();
			self::$installed_version = $array['version'];
		} else {
			self::$installed_version = '1.0.0';
		}
	}
	
	public static function set_installed_version($version){
		global $DB;
		self::$installed_version = $version;
		$query = "UPDATE `glpi_plugin_phpsaml_configs` SET version = '". $version ."' WHERE id = '1'";
		$DB->query($query);
		if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
	}
	
	public static function do_upgrade(){	
		
		self::do_109();
		
		self::do_110();
		
		self::do_111();
		
		self::do_112();
		
		self::do_113();
		
	}
	
	public static function do_109(){
		global $DB;
		
		Toolbox::logInFile("php-errors", "Checking Settings and Upgrading to 1.0.0 if necessary" . "\n", true);
		
		//Check for version column
		$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'version'";
		$result = $DB->query($query);
		if (!$result || $DB->numrows($result) == 0){
			$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD version VARCHAR(15) after id";
			$DB->query($query);
			if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		}
		
		//Check for enforced column
		$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'enforced'";
		$result = $DB->query($query);
		if (!$result || $DB->numrows($result) == 0){
			$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD enforced int(2) after version";
			$DB->query($query);
			if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
			
			$query = "UPDATE `glpi_plugin_phpsaml_configs` SET enforced = '0' WHERE id = '1'";
			$DB->query($query);
			if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		}
		
		if(self::$installed_version <= '1.0.0'){
			self::set_installed_version("1.0.9");
		}
	}
	
	public static function do_110(){
		global $DB;
			
		Toolbox::logInFile("php-errors", "Checking Settings and Upgrading to 1.1.0 if necessary"  . "\n", true);
		
		//Check for requested_authn_context column
		$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'requested_authn_context'";
		$result = $DB->query($query);
		if (!$result || $DB->numrows($result) == 0){
			$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context text after saml_idp_certificate";
			$DB->query($query);
			if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		}
		
		//Check for requested_authn_context_comparison column
		$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'requested_authn_context_comparison'";
		$result = $DB->query($query);
		if (!$result || $DB->numrows($result) == 0){
			$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context_comparison varchar(25) after requested_authn_context";
			$DB->query($query);
			if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		}
		
		if(self::$installed_version < '1.1.0'){
			self::set_installed_version("1.1.0");
		}
	}
	
	public static function do_111(){
		global $DB;
		
		Toolbox::logInFile("php-errors", "Checking Settings and Upgrading to 1.1.1 if necessary"  . "\n", true);
		
		if(self::$installed_version < '1.1.1'){
			self::set_installed_version("1.1.1");
		}
	}
	
	public static function do_112(){
		global $DB;
		
		Toolbox::logInFile("php-errors", "Checking Settings and Upgrading to 1.1.2 if necessary"  . "\n", true);
		
		//Check for saml_sp_nameid_format column
		$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'saml_sp_nameid_format'";
		$result = $DB->query($query);
		if (!$result || $DB->numrows($result) == 0){
			$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD saml_sp_nameid_format varchar(255) after saml_sp_certificate_key";
			$DB->query($query);
			if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		}
		
		if(self::$installed_version < '1.1.2'){
			self::set_installed_version("1.1.2");
		}
	}
	
	public static function do_113(){
		global $DB;
		
		Toolbox::logInFile("php-errors", "Checking Settings and Upgrading to 1.1.3 if necessary"  . "\n", true);
		
		//Check for jit column
		$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'jit'";
		$result = $DB->query($query);
		if (!$result || $DB->numrows($result) == 0){
			$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD jit int(2) after debug";
			$DB->query($query);
			if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		}
		
		if(self::$installed_version < '1.1.3'){
			self::set_installed_version("1.1.3");
		}
	}
}
?>