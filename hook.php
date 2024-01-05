<?php


/**
 *  ------------------------------------------------------------------------
 *  Derrick Smith - PHP SAML Plugin
 *  Copyright (C) 2014 by Derrick Smith
 *  ------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of phpsaml project.
 *
 * PHP SAML Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phpsaml is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with phpsaml. If not, see <http://www.gnu.org/licenses/>.
 *
 * ------------------------------------------------------------------------
 *
 *  @package  	phpsamlconfig
 *  @version	1.3.0
 *  @author    	Derrick Smith
 *  @author	   	Chris Gralike
 *  @copyright 	Copyright (c) 2018 by Derrick Smith
 *  @license   	MIT
 *  @see       	https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 *  @link		https://github.com/derricksmith/phpsaml/
 *  @since     	0.1
 * ------------------------------------------------------------------------
 **/

/**
 * It is in these functions that you need to put your SQL queries used for creating your specific tables.
 *
 * Here, you can now see your plugin in the list of plugins.
 *
 * @return boolean Needs to return true if success
 */

function plugin_phpsaml_install() {
	// Install SamlConfig
	if (method_exists(PluginPhpsamlConfig::class, 'install')) {
		$version   = plugin_version_phpsaml();
		$migration = new Migration($version['version']);
		PluginPhpsamlConfig::install($migration);
	}

	// Install Excludes
	if (method_exists(PluginPhpsamlExclude::class, 'install')) {
		$version   = plugin_version_phpsaml();
		$migration = new Migration($version['version']);
		PluginPhpsamlExclude::install($migration);
	}

	return true;
}

/**
 * Because we've created a table, do not forget to destroy if the plugin is uninstalled.
 *
 * @return boolean Needs to return true if success
 */
function plugin_phpsaml_uninstall() {
	// Install SamlConfig
	 if (method_exists(PluginPhpsamlConfig::class, 'uninstall')) {
		$version   = plugin_version_phpsaml();
		$migration = new Migration($version['version']);
		PluginPhpsamlConfig::uninstall($migration);
	 }
	 // Install excludes
	 if (method_exists(PluginPhpsamlExclude::class, 'uninstall')) {
		$version   = plugin_version_phpsaml();
		$migration = new Migration($version['version']);
		PluginPhpsamlExclude::uninstall($migration);
	 }
	return true;
}

// Called by the rule_engine hook if a phpsaml rule has been succesfully matched
function updateUser($params){
	// https://github.com/derricksmith/phpsaml/issues/149
	//var_dump($params);
}
