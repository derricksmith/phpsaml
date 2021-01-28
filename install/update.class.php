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
	
	public static function set_installed_version(){
		global $DB;
		$query = "UPDATE `glpi_plugin_phpsaml_configs` SET version = '". PLUGIN_PHPSAML_VERSION ."' WHERE id = '1'";
		$DB->query($query) or die("error set version ". $DB->error());
	}
	
	public static function do_upgrade(){	
		if(self::$installed_version == '1.0.0'){
			self::do_109();
		}
		if(self::$installed_version == '1.0.9'){
			self::do_110();
		}
	}
	
	public static function do_109(){
		global $DB;
		
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD version VARCHAR(15) after id";
		$DB->query($query) or die("error add version column 'version' ". $DB->error());
			
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD enforced int(2) after version";
		$DB->query($query) or die("error add version column 'enforced' ". $DB->error());
		
		$query = "UPDATE `glpi_plugin_phpsaml_configs` SET enforced = '0' WHERE id = '1'";
		$DB->query($query) or die("error set enforced = 0' ". $DB->error());
		
		self::set_installed_version();
	}
	
	public static function do_110(){
		global $DB;
			
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context text after saml_idp_certificate";
		$DB->query($query) or die("error add version column 'requested_authn_context' ". $DB->error());
		
		$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context_comparison varchar(25) after requested_authn_context";
		$DB->query($query) or die("error add version column 'requested_authn_context_comparison' ". $DB->error());
		
		self::set_installed_version();
	}
}
?>