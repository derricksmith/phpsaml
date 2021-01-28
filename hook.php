<?php

/*
   ------------------------------------------------------------------------
   fpsaml - Basic Template Plugin
   Copyright (C) 2014 by Future Processing
   ------------------------------------------------------------------------

   LICENSE

   This file is part of fpsaml project.

   FP Basic Template Plugin is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   fpsaml is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with fpsaml. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   fpsaml
   @author    Future Processing
   @co-author
   @copyright Copyright (c) 2014 by Future Processing
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @since     2014

   ------------------------------------------------------------------------
 */

/**
 * It is in these functions that you need to put your SQL queries used for creating your specific tables.
 *
 * Here, you can now see your plugin in the list of plugins.
 *
 * @return boolean Needs to return true if success
 */

function plugin_phpsaml_install() {
	global $DB;

	if (!$DB->tableExists("glpi_plugin_phpsaml_configs")) {
      	$query = "CREATE TABLE `glpi_plugin_phpsaml_configs` (
			`id` int(11) NOT NULL auto_increment,
			`version` varchar(15) NOT NULL,
			`enforced` int(2) NOT NULL,
			`strict` int(2) NOT NULL,
			`debug` int(2) NOT NULL,
			`saml_sp_certificate` text collate utf8_unicode_ci NOT NULL,
			`saml_sp_certificate_key` text collate utf8_unicode_ci NOT NULL,
			`saml_idp_entity_id` varchar(128) collate utf8_unicode_ci NOT NULL,
			`saml_idp_single_sign_on_service` varchar(128) collate utf8_unicode_ci NOT NULL,
			`saml_idp_single_logout_service` varchar(128) collate utf8_unicode_ci NOT NULL,
			`saml_idp_certificate` text collate utf8_unicode_ci NOT NULL,
			`requested_authn_context` text collate utf8_unicode_ci NOT NULL,
			`requested_authn_context_comparison` varchar(25) collate utf8_unicode_ci NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$DB->query($query) or die("error creating glpi_plugin_phpsaml_configs ". $DB->error());
		
		$query = "INSERT INTO `glpi_plugin_phpsaml_configs`
            (`id`,`version`, `enforced`, `strict`, `debug`, `saml_idp_entity_id`, `saml_sp_certificate`, `saml_sp_certificate_key`, `saml_idp_single_sign_on_service`, `saml_idp_single_logout_service`, `saml_idp_certificate`, `requested_authn_context`, `requested_authn_context_comparison`)
            VALUES
            ('1', '". PLUGIN_PHPSAML_VERSION ."', '0', '1', '', '', '', '', '', '', '', '', '')";
		$DB->query($query) or die("error populate glpi_plugin_phpsaml_configs ". $DB->error());
	}
	
	if ($DB->tableExists('glpi_plugin_phpsaml_configs')) {
		include_once( PLUGIN_PHPSAML_DIR . "/install/update.class.php" );
		$update = new PluginPhpsamlUpdate();
	}
	return true;
}

/**
 * Because we've created a table, do not forget to destroy if the plugin is uninstalled.
 *
 * @return boolean Needs to return true if success
 */
function plugin_phpsaml_uninstall() {
	global $DB;

	if ($DB->tableExists("glpi_plugin_phpsaml_config")) {
		$query = "DROP TABLE `glpi_plugin_phpsaml_config`";
		$DB->query($query) or die("error deleting glpi_plugin_phpsaml_config");
	}
	if ($DB->tableExists("glpi_plugin_phpsaml_configs")) {
		$query = "DROP TABLE `glpi_plugin_phpsaml_configs`";
		$DB->query($query) or die("error deleting glpi_plugin_phpsaml_configs");
	}
	return true;
}
