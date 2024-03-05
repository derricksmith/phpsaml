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
 *  @package        phpsamlconfig
 *  @version        1.3.0
 *  @author         Derrick Smith
 *  @author         Chris Gralike
 *  @copyright      Copyright (c) 2018 by Derrick Smith
 *  @license        MIT
 *  @see            https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 *  @link           https://github.com/derricksmith/phpsaml/
 *  @since          0.1
 * ------------------------------------------------------------------------
 **/

// HEADERGUARD GLPI MUST BE LOADED
if (!defined("GLPI_ROOT")) { die("Sorry. You can't access directly to this file"); }

// CONSTANTS
define("PLUGIN_PHPSAML_VERSION", "1.3.0");
define("PLUGIN_PHPSAML_MIN_GLPI", "9.4");
define("PLUGIN_PHPSAML_MAX_GLPI", "10.0.99");
define('PLUGIN_PHPSAML_DIR', __DIR__);                  //used in config.class.php

/**
 * Definition of the plugin version and its compatibility with the version of core
 *
 * @return array
 */
function plugin_version_phpsaml() : array                           //NOSONAR - Default GLPI function.
{
    return ['name'             => "PHP SAML",
            'version'         => PLUGIN_PHPSAML_VERSION,
            'author'         => 'Derrick Smith',
            'license'         => 'GPLv2+',
            'homepage'         => 'http://derrick-smith.com',
            'requirements'    => [
                'glpi'         => [
                    'min'      => PLUGIN_PHPSAML_MIN_GLPI,
                    'max'      => PLUGIN_PHPSAML_MAX_GLPI,
                    'dev'      => true]] //Required to allow 9.2-dev
    ];
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
function plugin_phpsaml_check_prerequisites() : bool                //NOSONAR - Default GLPI function.
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
 * Check the phpsaml configuration, currently not used.
 *
 * @param bool $verbose
 * @return bool
 */
function plugin_phpsaml_check_config($verbose = false) : bool       //NOSONAR - Default GLPI function.
{
    return true;
}


/**
 * Initialize the phpsaml plugin
 *
 * @global array     $PLUGIN_HOOKS
 * @return void
 * @see                https://glpi-developer-documentation.readthedocs.io/en/master/plugins/hooks.html
 */
function plugin_init_phpsaml() : void                               //NOSONAR - Default GLPI function.
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['phpsaml'] = true;              //NOSONAR - Default GLPI variable

    // We dont use composer (yet)
    Plugin::registerClass('PluginPhpsaml');
    Plugin::registerClass('PluginPhpsamlAcs');
    Plugin::registerClass('PluginPhpsamlExclude');
    Plugin::registerClass('PluginPhpsamlRuleRight');
    Plugin::registerClass('PluginPhpsamlRuleRightCollection', ['rulecollections_types' => true]);

    // Register config page if user has correct rights.
    $p = new Plugin();
    if (Session::getLoginUserID() && $p->isActivated("phpsaml") && Session::haveRight('config', UPDATE)) {
        // Add saml config to config menu
        $PLUGIN_HOOKS['menu_toadd']['phpsaml'] = ['config' => 'PluginPhpsamlConfig',];
        $PLUGIN_HOOKS['config_page']['phpsaml'] = 'front/config.php';
    }

    // Register hook matched rules (not yet implemented fully)
    // https://github.com/derricksmith/phpsaml/issues/149
    $PLUGIN_HOOKS['rule_matched']['phpsaml']    = 'updateUser';

    // Register hook for Single Sign On
    $PLUGIN_HOOKS['post_init']['phpsaml']       = 'pluginPhpsamlPostInit';

    // Register hook for setting $_SESSION vars
    $PLUGIN_HOOKS['init_session']['phpsaml']    = 'pluginPhpsamlInitSession';

    // Register hook to display phpsaml login button
    $PLUGIN_HOOKS['display_login']['phpsaml']   = 'pluginPhpsamlDisplayLogin';
    
    // Register hook to include required js and css files.
    // Added key exists validation for Cron https://github.com/derricksmith/phpsaml/issues/130
    //Maybe move these to /tpl directory?
    if (array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], '/front/config.php')) {
        $PLUGIN_HOOKS['add_javascript']['phpsaml'][] = 'js/jquery.multi-select.js';
        $PLUGIN_HOOKS['add_css']['phpsaml'] = 'css/multi-select.css';
    }
}


/**
 * Called after init of GLPI.
 *
 * @global array $PLUGIN_HOOKS
 */
function pluginPhpsamlPostInit()
{
    global $GLPI_CACHE;

    // Collect the properties we need;
    $phpsaml        = new PluginPhpsamlPhpsaml();
    $cfgObj         = new PluginPhpsamlConfig();
    $config         = $cfgObj->getConfig();
    
    // Validate we have a valid configuration
    if(!empty($config[PluginPhpSamlConfig::SSOURL]) &&
       !empty($config[PluginPhpSamlConfig::IPCERT]) &&
       !empty($config[PluginPhpSamlConfig::SLOURL]) ){

            $samlnosso = $GLPI_CACHE->get('phpsaml_'.session_id());
            /**
             * Allow users to bypass enforce switch if needed.
             * Use GLPI cache because $_SESSION is reset by GLPI and not persist.
             * @see     https://github.com/DonutsNL/phpsaml2/issues/1
             */
            if(!isset($_GET['SSO'])   &&
              (isset($_GET['nosso'])  ||
               $samlnosso             )){
                    $GLPI_CACHE->set('phpsaml_'.session_id(), true);
                    $nosso = true;
            }else{
                    $GLPI_CACHE->set('phpsaml_'.session_id(), false);
                    $nosso = false;
            }
            

            /**
             * @since 1.1.0      perform SSO if..
             */
            if ((isset($_GET['SSO']) && ($_GET['SSO'] == 1)    ||
                 $config[PluginPhpsamlConfig::FORCED]          ||
                 !empty($_SESSION['plugin_phpsaml_nameid']))   &&
                 !$nosso                                       ){
                    return $phpsaml->processUserLogin();
            }
    } // else do nothing, we cant use phpsaml for auth.
}

/**
 * Sets $_SESSION vars, function is HOOKED at the GLPI 'session_init' event.
 */
function pluginPhpsamlInitSession()
{
   $phpsaml = new PluginPhpsamlPhpsaml();

   if (!empty($phpsaml::$nameid)) { $_SESSION['plugin_phpsaml_nameid'] = $phpsaml::$nameid; }
   if (!empty($phpsaml::$nameidformat)) { $_SESSION['plugin_phpsaml_nameidformat'] = $phpsaml::$nameidformat; }
   if (!empty($phpsaml::$sessionindex)) { $_SESSION['plugin_phpsaml_sessionindex'] = $phpsaml::$sessionindex; }
}

/**
 * Shows login button, function is HOOKED at the GLPI 'display_login' event.
 */
function pluginPhpsamlDisplayLogin()
{
    // Get button FriendlyName from config.
    // https://github.com/derricksmith/phpsaml/issues/126
    // https://github.com/derricksmith/phpsaml/issues/135
    $cfgObj     = new PluginPhpsamlConfig();
    $btn         = $cfgObj->getConfig();

    // https://github.com/derricksmith/phpsaml/issues/152#issuecomment-1884852309
    if(!empty($btn[PluginPhpSamlConfig::SSOURL]) &&
       !empty($btn[PluginPhpSamlConfig::ENTITY]) &&
       !empty($btn[PluginPhpSamlConfig::IPCERT]) &&
       !empty($btn[PluginPhpSamlConfig::SLOURL]) ){

        $btn          = (array_key_exists(PluginPhpsamlConfig::CFNAME, $btn)) ? $btn[PluginPhpsamlConfig::CFNAME] : 'PHP Saml';
        $redirect     = (isset($_GET['redirect'])) ? '&redirect='.urlencode($_GET['redirect']) : null;
        $btn          = (!empty($btn) && is_string($btn)) ? htmlentities($btn) : 'phpsaml';
        print '
        <div>
            <div class="card-header">
                <h2>Connect with an external provider</h2>
            </div>
            <div class="card-body">
                <div class="list-group list-group-horizontal justify-content-center" style="cursor:pointer; padding:43px 0px 0px 0px;">
                    <a class="list-group-item d-flex flex-column" onclick="window.location.href=\'?SSO=1'.$redirect.'\'" title="phpSaml">
                        <i class="fab fa-windows fa-5x"></i><span>'.$btn.'</span></a>
                </div>
            </div>
        </div>
        ';
    }else{
        // Show an error that no valid configuration was found.
        // Showing the button doesnt make any sense now.
        print '
        <div>
            <div class="card-header">
                <h2>Connect with an external provider</h2>
            </div>
            <div class="card-body">
                <div class="list-group list-group-horizontal justify-content-center" style="cursor:pointer; padding:43px 0px 0px 0px;">
                    <p>PHP SAML was enabled.<br>but no valid configuration could be found.</p>
                </div>
            </div>
        </div>
        ';
    }
}
