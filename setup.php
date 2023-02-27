<?php

/*
   ------------------------------------------------------------------------
   Derrick Smith - PHP SAML Plugin
   Copyright (C) 2014 by Derrick Smith
   ------------------------------------------------------------------------

   LICENSE

   This file is part of phpsaml project.

   PHP SAML Plugin is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   phpsaml is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with phpsaml. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   phpsaml
   @author    Derrick Smith
   @co-author Chris Gralike
   @copyright Copyright (c) 2018 by Derrick Smith
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @since     2018

   ------------------------------------------------------------------------
 */
define("PLUGIN_PHPSAML_VERSION", "1.2.1");
define("PLUGIN_PHPSAML_MIN_GLPI", "9.4");
define("PLUGIN_PHPSAML_MAX_GLPI", "10.0.99");
define('PLUGIN_PHPSAML_DIR', __DIR__);

$phpSamlPath = (strpos(dirname(__FILE__), 'plugins') !== false) ? '/plugins/phpsaml' : '/marketplace/phpsaml';
define('PLUGIN_PHPSAML_BASEURL', GLPI_ROOT . $phpSamlPath . '/');


/**
 *
 * Definition of the plugin version and its compatibility with the version of core
 *
 * @return array
 */
function plugin_version_phpsaml() : array
{
    return ['name' 			=> "PHP SAML",
            'version' 		=> PLUGIN_PHPSAML_VERSION,
            'author' 		=> 'Derrick Smith',
            'license' 		=> 'GPLv2+',
            'homepage' 		=> 'http://derrick-smith.com',
		    'requirements'	=> [
				'glpi' 		=> [
					'min'  	=> PLUGIN_PHPSAML_MIN_GLPI,
					'max'  	=> PLUGIN_PHPSAML_MAX_GLPI,
					'dev'  	=> true]] //Required to allow 9.2-dev
	];
}


/**
 *
 * Blocking a specific version of GLPI.
 * GLPI constantly evolving in terms of functions of the heart, it is advisable
 * to create a plugin blocking the current version, quite to modify the function
 * to a later version of GLPI. In this example, the plugin will be operational
 * with the 0.84 and 0.85 versions of GLPI.
 *
 * @return boolean
 */
function plugin_phpsaml_check_prerequisites() : bool
{
    if (version_compare(GLPI_VERSION, PLUGIN_PHPSAML_MIN_GLPI, 'lt') ||
	    version_compare(GLPI_VERSION, PLUGIN_PHPSAML_MAX_GLPI, 'gt')) {

        if (method_exists('Plugin', 'messageIncompatible')) {
			//since GLPI 9.2
			Plugin::messageIncompatible('core', PLUGIN_PHPSAML_MIN_GLPI, PLUGIN_PHPSAML_MAX_GLPI);
		} else {
			echo "This plugin requires GLPI >= ".PLUGIN_PHPSAML_MIN_GLPI." and GLPI <= ".PLUGIN_PHPSAML_MAX_GLPI;
        }
		return false;
    } else {
		return true;
	}
}

/**
 *
 * Check the configuration
 *
 * @param bool $verbose
 * @return bool
 */
function plugin_phpsaml_check_config(bool $verbose = false) : bool
{
	// This will always return true, it will never reach $verbose or false.
	// Do we want to use this?
    if ($verbose) {
        echo 'Installed / not configured';
    }

    return true;
}

/**
 *
 * Called at init
 *
 * @global array $PLUGIN_HOOKS
 * @return void
 */
function plugin_init_phpsaml() : void
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['phpsaml'] = true;
	
	Plugin::registerClass('PluginPhpsaml');
	
	if (Session::getLoginUserID()) {
		$plugin = new Plugin();
		if ($plugin->isActivated("phpsaml") && Session::haveRight('config', UPDATE)) {
			// Config page
			$PLUGIN_HOOKS['config_page']['phpsaml'] = 'front/config.php';

			// TODO: @derrick this file is not existing in phpsaml what does/should it do?
			//$PLUGIN_HOOKS['redirect_page']['phpsaml'] = 'phpsaml.form.php';

			Plugin::registerClass('PluginPhpsamlRuleRight');
			Plugin::registerClass('PluginPhpsamlRuleRightCollection', ['rulecollections_types' => true]);
		}
	}

	// Hooks: https://glpi-developer-documentation.readthedocs.io/en/master/plugins/hooks.html
	// Hook for rule matched
	$PLUGIN_HOOKS['rule_matched']['phpsaml'] = 'updateUser';

	// Hook for Single Sign On
	$PLUGIN_HOOKS['post_init']['phpsaml'] = 'plugin_post_init_phpsaml';
	
	// Hook for setting into session saml values
	$PLUGIN_HOOKS['init_session']['phpsaml'] = 'plugin_init_session_phpsaml';
	
	// Hook login form - Display Single Sign On Button
	$PLUGIN_HOOKS['display_login']['phpsaml'] = 'plugin_display_login';
	
	if (strpos($_SERVER['REQUEST_URI'], 'plugins/phpsaml/front/config.php') || strpos($_SERVER['REQUEST_URI'], 'plugins\phpsaml\front\config.php')){
		// Load JS on config page
		$PLUGIN_HOOKS['add_javascript']['phpsaml'][] = 'js/jquery.multi-select.js';
		
		// Load CSS on config page
		$PLUGIN_HOOKS['add_css']['phpsaml'] = 'css/multi-select.css';
	}
}


/**
 * Called after init of GLPI.
 *
 * @global array $PLUGIN_HOOKS
 */
function plugin_post_init_phpsaml()
{
	global $CFG_GLPI;
	
	// Collect the properties we need;
	$phpsamlConfig 	= new PluginPhpsamlConfig();
	$config 		= $phpsamlConfig->getConfig();
	$enforc 		= $config['enforced'];
	$sloUri 		= $config['saml_idp_single_logout_service'];


	if (strpos($_SERVER['REQUEST_URI'], 'front/logout.php') || strpos($_SERVER['REQUEST_URI'], 'front\logout.php')) {
		$_SESSION['noAUTO'] = 1;
	}
	
	//Added 1.1.0 - SSO enforcement and signin with SSO button on login page
	     // Is SSO URI flag set to 1
	if ((!isset($_GET['noenforce'])) && (isset($_GET['SSO']) && $_GET['SSO'] == 1) ||
	    ($enforc) ||
		(!empty($_SESSION['plugin_phpsaml_nameid']))) {
		$phpsaml = new PluginPhpsamlPhpsaml();
		
		// Handle excludes
		$excludes = ['cron.php',
					 'ldap_mass_sync.php',
					 'apirest.php',
					 'acs.php'];
		foreach ($excludes as $value) {
			if ((PHP_SAPI === 'cli') || strpos($_SERVER['REQUEST_URI'], $value) !== false) {
				return true;
			}
		}
		
		// Not used anymore in glpi 10 with native inventory.
		if ((class_exists('PluginFusioninventoryCommunication') &&
			(strpos($_SERVER['HTTP_USER_AGENT'], 'FusionInventory-Agent_') !== false)) &&
			((strpos($_SERVER['REQUEST_URI'], '/plugins/fusioninventory/') !== false) ||
			 (strpos($_SERVER['REQUEST_URI'], '\plugins\fusioninventory/') !== false))) {
				return false;
		}
		
		if ((strpos($_SERVER['REQUEST_URI'], 'front/logout.php') !== false ||
		     strpos($_SERVER['REQUEST_URI'], 'front\logout.php') !== false) &&
			 (!empty($sloUri))) {
				$phpsaml::sloRequest();
		}
		
		// This needs cleanup.
		if (!$phpsaml::isUserAuthenticated()) {
			if ((isset($_GET['noAUTO']) && $_GET['noAUTO'] == 1) || (isset($_SESSION['noAUTO']) && $_SESSION['noAUTO'] == 1)) {
				
				//lets make sure the session is cleared.
				$phpsaml::glpiLogout();
				
				$error = "You have logged out of GLPI but are still logged into your Identity Provider.
						  Select Log in Again to automatically log back into GLPI or close this window.
						  Configure the SAML setting in the PHPSAML plugin configuration to enable Single Logout.";
				
				// we have done at least a good login? No, we exit.
				Html::nullHeader("Login", $CFG_GLPI['url_base'] . '/index.php');
				echo '<div class="center b">'.$error.'<br><br>';

				// Logout whit noAUto to manage auto_login with errors
				echo '<a href="' . $CFG_GLPI['url_base'] .'/index.php">' .__('Log in again') . '</a></div>';
				Html::nullFooter();

				exit();
			} else {
				// Fix for invalid redirect errors when port number is included in HTTP_HOST.
				// Maybe replace it with GLPI config: URL of the application?
				list($realhost,)=explode(':',$_SERVER['HTTP_HOST']);
				
				// lets check for the redirect parameter, if it doesn't exist lets redirect the visitor back to the original page
				// Fixed in 1.2.0 - Resolved Undefinded index: HTTP_HOST
				$returnTo = (isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $realhost . $_SERVER['REQUEST_URI']);
				$phpsaml::ssoRequest($returnTo);
			}
		}
	}
	return true;
}

function plugin_init_session_phpsaml()
{
   $phpsaml = new PluginPhpsamlPhpsaml();

   if (!empty($phpsaml::$nameid)) { $_SESSION['plugin_phpsaml_nameid'] = $phpsaml::$nameid; }
   if (!empty($phpsaml::$nameidformat)) { $_SESSION['plugin_phpsaml_nameidformat'] = $phpsaml::$nameidformat; }
   if (!empty($phpsaml::$sessionindex)) { $_SESSION['plugin_phpsaml_sessionindex'] = $phpsaml::$sessionindex; }
}

function plugin_display_login()
{
	$cfg 		= new PluginPhpsamlConfig();
	$btn 		= $cfg->getConfig('1', 'saml_configuration_name');
	$redirect 	= (isset($_GET['redirect'])) ? '&redirect='.urlencode($_GET['redirect']) : null;
	$btn 		= (!empty($btn) && is_string($btn)) ? htmlentities($btn) : 'phpsaml';
	echo 		  "<input class=\"submit btn btn-primary\" value=\"Use $btn\" onclick=\"window.location.href='?SSO=1$redirect'\" />";
}
