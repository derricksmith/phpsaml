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
   @co-author
   @copyright Copyright (c) 2018 by Derrick Smith
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @since     2018

   ------------------------------------------------------------------------
 */
 
define ("PLUGIN_PHPSAML_VERSION", "1.2.1");
define("PLUGIN_PHPSAML_MIN_GLPI", "9.4");
define("PLUGIN_PHPSAML_MAX_GLPI", "10.0.99");
define('PLUGIN_PHPSAML_DIR', __DIR__);
define('PLUGIN_PHPSAML_BASEURL', GLPI_ROOT .'/plugins/phpsaml/');

/**
 * Definition of the plugin version and its compatibility with the version of core
 *
 * @return array
 */
function plugin_version_phpsaml()
{

    return array('name' => "PHP SAML",
        'version' => PLUGIN_PHPSAML_VERSION,
        'author' => 'Derrick Smith',
        'license' => 'GPLv2+',
        'homepage' => 'http://derrick-smith.com',
		'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_PHPSAML_MIN_GLPI,
                'max' => PLUGIN_PHPSAML_MAX_GLPI,
                'dev' => true, //Required to allow 9.2-dev
            ]
        ],
	);
}

/**
 * Blocking a specific version of GLPI.
 * GLPI constantly evolving in terms of functions of the heart, it is advisable
 * to create a plugin blocking the current version, quite to modify the function
 * to a later version of GLPI. In this example, the plugin will be operational
 * with the 0.84 and 0.85 versions of GLPI.
 *
 * @return boolean
 */
function plugin_phpsaml_check_prerequisites()
{

    if (version_compare(GLPI_VERSION, PLUGIN_PHPSAML_MIN_GLPI, 'lt') || version_compare(GLPI_VERSION, PLUGIN_PHPSAML_MAX_GLPI, 'gt')) {
        if (method_exists('Plugin', 'messageIncompatible')) {
			//since GLPI 9.2
			Plugin::messageIncompatible('core', PLUGIN_PHPSAML_MIN_GLPI, PLUGIN_PHPSAML_MAX_GLPI);
		} else {
			echo "This plugin requires GLPI >= ".PLUGIN_PHPSAML_MIN_GLPI." and GLPI <= ".PLUGIN_PHPSAML_MAX_GLPI;
        }
		return false;
    }

    return true;
}

/**
 * Control of the configuration
 *
 * @param type $verbose
 * @return boolean
 */
function plugin_phpsaml_check_config($verbose = false)
{
    if (true) { // Your configuration check
       return true;
    }

    if ($verbose) {
        echo 'Installed / not configured';
    }

    return false;
}

/**
 * Initialization of the plugin
 *
 * @global array $PLUGIN_HOOKS
 */
function plugin_init_phpsaml()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['phpsaml'] = true;
	
	Plugin::registerClass('PluginPhpsaml');
	
	if (Session::getLoginUserID()) {
		$plugin = new Plugin();
		if ($plugin->isActivated("phpsaml")) {
			if (Session::haveRight('config', UPDATE)) {
				// Config page
				$PLUGIN_HOOKS['config_page']['phpsaml'] = 'front/config.php';
				//Redirect code
				$PLUGIN_HOOKS['redirect_page']['phpsaml'] = 'phpsaml.form.php';
			}
		}
	}
	// Hook for Single Sign On
	$PLUGIN_HOOKS['post_init']['phpsaml'] = 'plugin_post_init_phpsaml';
	
	// Hook for setting into session saml values
	$PLUGIN_HOOKS['init_session']['phpsaml'] = 'plugin_init_session_phpsaml';
	
	// Hook login form - Display Single Sign On Button
	$PLUGIN_HOOKS['display_login']['phpsaml'] = 'plugin_display_login';
	
	if (strpos($_SERVER['REQUEST_URI'], 'plugins/phpsaml/front/config.php') || strpos($_SERVER['REQUEST_URI'], 'plugins\phpsaml\front\config.php')){
		// Load JS on config page
		$PLUGIN_HOOKS['add_javascript']['phpsaml'][] = 'js/jquery.multi-select.js';
		//$PLUGIN_HOOKS['add_javascript']['phpsaml'][] = 'js/phpsaml.js';
		
		// Load CSS on config page
		$PLUGIN_HOOKS['add_css']['phpsaml'] = 'css/multi-select.css';
	}
}

function plugin_post_init_phpsaml(){
	global $DB, $CFG_GLPI;
	
	$phpsamlConfig = new PluginPhpsamlConfig();
	$config = $phpsamlConfig->getConfig();
	
	if (strpos($_SERVER['REQUEST_URI'], 'front/logout.php') || strpos($_SERVER['REQUEST_URI'], 'front\logout.php')){
		$_SESSION['noAUTO'] = 1;
	}
	
	//Added 1.1.0 - SSO enforcement and signin with SSO button on login page
	if ((isset($_GET['SSO']) && $_GET['SSO'] == 1) || (isset($config['enforced']) && $config['enforced'] == 1) || (!empty($_SESSION['plugin_phpsaml_nameid']))){
		$phpsaml = new PluginPhpsamlPhpsaml();
		
		//Added 1.2.0 - Return if cli, cannot use SSO on cli
		if (PHP_SAPI === 'cli'){
			return;
		}
		
		if (strpos($_SERVER['REQUEST_URI'], 'front/cron.php') !== false || strpos($_SERVER['REQUEST_URI'], 'front\cron.php') !== false){
			return;
		}
		
		if (strpos($_SERVER['REQUEST_URI'], 'ldap_mass_sync.php') !== false){
			return;
		}
		
		if (strpos($_SERVER['REQUEST_URI'], 'apirest.php') !== false){
			return;
		}
		
		if (strpos($_SERVER['REQUEST_URI'], 'front/acs.php') !== false || strpos($_SERVER['REQUEST_URI'], 'front\acs.php') !== false){
			return;
		}

		if (class_exists('PluginFusioninventoryCommunication') && strpos($_SERVER['HTTP_USER_AGENT'], 'FusionInventory-Agent_') !== false){ 
			if(strpos($_SERVER['REQUEST_URI'], '/plugins/fusioninventory/') !== false || strpos($_SERVER['REQUEST_URI'], '\plugins\fusioninventory/') !== false){
				return;
			}
		}
		
		if (strpos($_SERVER['REQUEST_URI'], 'front/logout.php') !== false || strpos($_SERVER['REQUEST_URI'], 'front\logout.php') !== false){
			if (!empty($config['saml_idp_single_logout_service'])){
				$phpsaml::sloRequest();
			} 
		}
		
		if (!$phpsaml::isUserAuthenticated()) {
			if ((isset($_GET['noAUTO']) && $_GET['noAUTO'] == 1) || (isset($_SESSION['noAUTO']) && $_SESSION['noAUTO'] == 1)){
				
				//lets make sure the session is cleared.
				$phpsaml::glpiLogout();
				
				$error = "You have logged out of GLPI but are still logged into your Identity Provider.  Select Log in Again to automatically log back into GLPI or close this window.  Configure the SAML setting in the PHPSAML plugin configuration to enable Single Logout.";
				
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
	return;
}

function plugin_init_session_phpsaml() {
   $phpsaml = new PluginPhpsamlPhpsaml();

   if(!empty($phpsaml::$nameid)) $_SESSION['plugin_phpsaml_nameid'] = $phpsaml::$nameid;
   if(!empty($phpsaml::$nameidformat)) $_SESSION['plugin_phpsaml_nameidformat'] = $phpsaml::$nameidformat;
   if(!empty($phpsaml::$sessionindex)) $_SESSION['plugin_phpsaml_sessionindex'] = $phpsaml::$sessionindex;
}

function plugin_display_login(){
	// lets check for the redirect parameter, if it doesn't exist we will redirect back to front page
	$redirect = (isset($_GET['redirect']) ? '&redirect='.urlencode($_GET['redirect']) : null);
	?>
	<input class="submit btn btn-primary w-100" value="Sign In with SSO" onclick="window.location.href='?SSO=1<?php echo $redirect; ?>'" />
	<?php
}
