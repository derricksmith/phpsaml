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
		// Fixed for PHP8
		if (is_array($array) && array_key_exists('version', $array)) {
			$query = "SELECT * FROM `glpi_plugin_phpsaml_configs` WHERE id = '1'";
			$result = $DB->query($query);
			$array = $result->fetch_array();
			self::$installed_version = $array['version'];
		} else {
			self::$installed_version = '';
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
		
		self::do_120();
		
		self::do_121();
		
	}
	
	public static function do_109(){
		global $DB;
		
		Toolbox::logInFile("phpsaml", "INFO -- Checking Settings and Upgrading to 1.0.0 if necessary" . "\n", true);
		
		if(self::$installed_version <= '1.0.9' || self::$installed_version == ''){
			Toolbox::logInFile("phpsaml", "INFO -- Upgrading PHPSAML plugin to 1.0.9" . "\n", true);
			
			//Check for version column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'version'";
			$result = $DB->query($query);
			//Alter table if 'version' column is missing
			if (!$result || $DB->numrows($result) == 0){
				Toolbox::logInFile("phpsaml", "INFO -- Column 'version' missing, updating table" . "\n", true);
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD version VARCHAR(15) after id";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'version' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
			
			//Check for enforced column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'enforced'";
			$result = $DB->query($query);
			//Alter table if 'enforced' column is missing
			if (!$result || $DB->numrows($result) == 0){
				Toolbox::logInFile("phpsaml", "INFO -- Column 'enforced' missing, updating table" . "\n", true);
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD enforced int(2) after version";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				
				//Update 'enforced' column if there were not issues creating it
				if (!$DB->error()) {
					Toolbox::logInFile("phpsaml", "INFO -- Column 'version' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
					$query = "UPDATE `glpi_plugin_phpsaml_configs` SET enforced = '0' WHERE id = '1'";
					$DB->query($query);
					if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
					if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'enforced' set to 0" . "\n", true);
				}
			}
			
			self::set_installed_version("1.0.9");
			Toolbox::logInFile("phpsaml", "INFO -- PHPSAML upgraded to 1.0.9" . "\n", true);
		}
	}
	
	public static function do_110(){
		global $DB;
		
		Toolbox::logInFile("phpsaml", "INFO -- Checking Settings and Upgrading to 1.1.0 if necessary"  . "\n", true);
		
		if(self::$installed_version <= '1.1.0'){
		
			//Check for requested_authn_context column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'requested_authn_context'";
			$result = $DB->query($query);
			//Alter table if 'requested_authn_context column' column is missing
			if (!$result || $DB->numrows($result) == 0){
				Toolbox::logInFile("phpsaml", "INFO -- Column 'requested_authn_context column' missing, updating table" . "\n", true);
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context text after saml_idp_certificate";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'requested_authn_context' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
			
			//Check for requested_authn_context_comparison column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'requested_authn_context_comparison'";
			$result = $DB->query($query);
			//Alter table if 'requested_authn_context_comparison' column is missing
			if (!$result || $DB->numrows($result) == 0){
				Toolbox::logInFile("phpsaml", "INFO -- Column 'requested_authn_context_comparison' missing, updating table" . "\n", true);
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD requested_authn_context_comparison varchar(25) after requested_authn_context";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'requested_authn_context_comparison' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
		
			self::set_installed_version("1.1.0");
			Toolbox::logInFile("phpsaml", "INFO -- PHPSAML upgraded to 1.1.0" . "\n", true);
		}
	}
	
	public static function do_111(){
		global $DB;
		
		Toolbox::logInFile("phpsaml", "INFO -- Checking Settings and Upgrading to 1.1.1 if necessary"  . "\n", true);
		//No database operations required for version 1.1.1
		if(self::$installed_version < '1.1.1'){
			self::set_installed_version("1.1.1");
			Toolbox::logInFile("phpsaml", "INFO -- PHPSAML upgraded to 1.1.1" . "\n", true);
		}
	}
	
	public static function do_112(){
		global $DB;
		
		Toolbox::logInFile("phpsaml", "INFO -- Checking Settings and Upgrading to 1.1.2 if necessary"  . "\n", true);
		
			if(self::$installed_version < '1.1.2'){
			
			//Check for saml_sp_nameid_format column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'saml_sp_nameid_format'";
			$result = $DB->query($query);
			//Alter table if 'saml_sp_nameid_format' column is missing
			if (!$result || $DB->numrows($result) == 0){
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD saml_sp_nameid_format varchar(255) after saml_sp_certificate_key";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'saml_sp_nameid_format' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
		
			self::set_installed_version("1.1.2");
			Toolbox::logInFile("phpsaml", "INFO -- PHPSAML upgraded to 1.1.2" . "\n", true);
		}
	}
	
	public static function do_113(){
		global $DB;
		
		Toolbox::logInFile("phpsaml", "INFO -- Checking Settings and Upgrading to 1.1.3 if necessary"  . "\n", true);
		
		if(self::$installed_version < '1.1.3'){
		
			//Check for jit column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'jit'";
			$result = $DB->query($query);
			//Alter table if 'jit' column is missing
			if (!$result || $DB->numrows($result) == 0){
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD jit int(2) after debug";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'jit' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
		
			self::set_installed_version("1.1.3");
			Toolbox::logInFile("phpsaml", "INFO -- PHPSAML upgraded to 1.1.3" . "\n", true);
		}
	}
	
	public static function do_120(){
		global $DB;
		
		Toolbox::logInFile("phpsaml", "INFO -- Checking Settings and Upgrading to 1.2.0 if necessary"  . "\n", true);
		//No database operations required for version 1.2.0
		if(self::$installed_version < '1.2.0'){
			self::set_installed_version("1.2.0");
			Toolbox::logInFile("phpsaml", "INFO -- PHPSAML upgraded to 1.2.0" . "\n", true);
		}
	}
	
	public static function do_121(){
		global $DB;
		
		Toolbox::logInFile("phpsaml", "INFO -- Checking Settings and Upgrading to 1.2.1 if necessary"  . "\n", true);

		if(self::$installed_version < '1.2.1'){
			//Check for saml_security_nameidencrypted column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'saml_security_nameidencrypted'";
			$result = $DB->query($query);
			//Alter table if 'saml_security_nameidencrypted' column is missing
			if (!$result || $DB->numrows($result) == 0){
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD saml_security_nameidencrypted int(2) after requested_authn_context_comparison";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'saml_security_nameidencrypted' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
			
			//Check for saml_security_authnrequestssigned column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'saml_security_authnrequestssigned'";
			$result = $DB->query($query);
			//Alter table if 'saml_security_authnrequestssigned' column is missing
			if (!$result || $DB->numrows($result) == 0){
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD saml_security_authnrequestssigned int(2) after saml_security_nameidencrypted";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'saml_security_authnrequestssigned' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
			
			//Check for saml_security_logoutrequestsigned column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'saml_security_logoutrequestsigned'";
			$result = $DB->query($query);
			//Alter table if 'saml_security_logoutrequestsigned' column is missing
			if (!$result || $DB->numrows($result) == 0){
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD saml_security_logoutrequestsigned int(2) after saml_security_authnrequestssigned";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'saml_security_logoutrequestsigned' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
			
			//Check for saml_security_logoutresponsesigned column
			$query = "SHOW COLUMNS FROM `glpi_plugin_phpsaml_configs` LIKE 'saml_security_logoutresponsesigned'";
			$result = $DB->query($query);
			//Alter table if 'saml_security_logoutresponsesigned' column is missing
			if (!$result || $DB->numrows($result) == 0){
				$query = "ALTER TABLE `glpi_plugin_phpsaml_configs` ADD saml_security_logoutresponsesigned int(2) after saml_security_logoutrequestsigned";
				$DB->query($query);
				if ($DB->error()) Toolbox::logInFile("php-errors", $DB->error()  . "\n", true);
				if (!$DB->error()) Toolbox::logInFile("phpsaml", "INFO -- Column 'saml_security_logoutresponsesigned' added to 'glpi_plugin_phpsaml_configs'" . "\n", true);
			}
			
			
			
			self::set_installed_version("1.2.1");
			Toolbox::logInFile("phpsaml", "INFO -- PHPSAML upgraded to 1.2.1" . "\n", true);
		}
	}
}
?>