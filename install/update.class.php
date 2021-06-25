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
		if(self::$installed_version <= '1.0.0'){
			Toolbox::logInFile("php-errors", "Upgrading to 1.0.0" . "\n", true);
			self::do_109();
		}
		if(self::$installed_version < '1.1.0'){
			Toolbox::logInFile("php-errors", "Upgrading to 1.1.0"  . "\n", true);
			self::do_110();
		}
		if(self::$installed_version < '1.1.1'){
			Toolbox::logInFile("php-errors", "Upgrading to 1.1.1"  . "\n", true);
			self::do_111();
		}
		
		if(self::$installed_version < '1.1.2'){
			Toolbox::logInFile("php-errors", "Upgrading to 1.1.2"  . "\n", true);
			self::do_112();
		}
	}
	
	public static function do_109(){
		global $DB;
		
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD version VARCHAR(15) after id";
		$DB->query($query);
		if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
			
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD enforced int(2) after version";
		$DB->query($query);
		if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		
		$query = "UPDATE `glpi_plugin_phpsaml_configs` SET enforced = '0' WHERE id = '1'";
		$DB->query($query);
		if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		
		self::set_installed_version("1.0.9");
	}
	
	public static function do_110(){
		global $DB;
			
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context text after saml_idp_certificate";
		$DB->query($query);
		if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context_comparison varchar(25) after requested_authn_context";
		$DB->query($query);
		if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		
		self::set_installed_version("1.1.0");
	}
	
	public static function do_111(){
		global $DB;
		self::set_installed_version("1.1.1");
	}
	
	public static function do_112(){
		global $DB;
			
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD saml_sp_nameid_format varchar(255) after saml_sp_certificate_key";
		$DB->query($query);
		if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
		
		self::set_installed_version("1.1.2");
	}
}
?>