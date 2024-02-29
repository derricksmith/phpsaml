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
 * @package    phpsaml - Main Assertion Consumer Service class
 * @version    1.3.0
 * @author     Chris Gralike
 * @author     Derrick Smith
 * @copyright  Copyright (c) 2018 by Derrick Smith
 * @license    MIT
 * @see        https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 * @link       https://github.com/derricksmith/phpsaml/
 * @since      1.2.2
 * ------------------------------------------------------------------------
 **/

// GLPI MUST BE LOADED
if (!defined("GLPI_ROOT")) {
    die("Sorry. You can't access directly to this file");
}

class PluginPhpsamlAcs
{
    private const DS = DIRECTORY_SEPARATOR;

    private $pathInfo = [];
    private $samlResponse = null;
    private $phpsaml = null;
    private $phpsamlsettings = null;
    private $debug = false;

    public function __construct()
    {
        $this->populatePaths();
        $this->loadRequiredClassFiles();
        $this->getDebugConfig();            // Check if debugging is enabled.
    }

    public function assertSaml($samlResponse): void    //NOSONAR - Complexity by design.
    {
        if (is_array($samlResponse) && array_key_exists('SAMLResponse', $samlResponse)) {
            // Get phpSamlSettings
            $this->phpsaml = new PluginPhpsamlPhpsaml();
            $this->phpsamlsettings = $this->phpsaml::$phpsamlsettings;
            if (is_array($this->phpsamlsettings)) {
                // Saml2\Settings
                // @throws Error If any settings parameter is invalid
                // @throws Exception If Settings is incorrectly supplied 
                try {
                    $samlSettings = new OneLogin\Saml2\Settings($this->phpsamlsettings);
                } catch (Exception|Error $e) {
                    // Exit with error!
                    $this->printError($e->getMessage());
                }

                // Get config and the ProxyVars to true if the proxied config is set to true
                $cfgObj = new PluginPhpsamlConfig();
                $config = $cfgObj->getConfig();
                if ($config[PluginPhpsamlConfig::PROXIED]) {
                    $samltoolkit = new OneLogin\Saml2\Utils();
                    $samltoolkit::setProxyVars(true);
                }

                // Saml2\Response
                // @throws Exception
                try {
                    $this->samlResponse = new OneLogin\Saml2\Response($samlSettings, $samlResponse['SAMLResponse']);

                    // Dump the response on debug?
                    if ($this->debug) {
                        $this->dumpSamlResponse();
                    }

                } catch (Exception $e) {
                    // Exit with error
                    $this->printError($e->getMessage());
                }

                // Validate SamlResponse
                if (is_object($this->samlResponse) && $this->samlResponse->isValid()) {
                    $this->phpsaml::auth();
                    $this->evalAndAssignProperties();

                    // If no error was printed at this point
                    // try to logon the user
                    try {
                        $this->phpsaml::glpiLogin((isset($samlResponse['RelayState']) && $samlResponse['RelayState'] != '' ? $samlResponse['RelayState'] : ''));
                    } catch (Exception $e) {
                        $this->printError($e->getMessage());
                    }
                } else {
                    // Exit with error
                    $this->printError('samlResponse is not valid!');
                }
            } else {
                $this->printError('No valid phpSaml configuration received.');
            }
        } else {
            $this->printError('No valid SAMLResponse received in POST.');
        }
    }

    private function evalAndAssignProperties(): void
    {
        $error = false;

        if (!$response['nameId'] = $this->samlResponse->getNameId()) {
            $error['nameId'] = 'NameId is missing in response';
        } else {
            // If the string #EXT# if found, a guest account is used thats not
            // transformed properly. Write an error and exit!
            // https://github.com/derricksmith/phpsaml/issues/135
            if (strstr($response['nameId'], '#EXT#@')) {
                $this->printError('Detected an inproperly transformed guest claims, make sure nameid,
                                   name are populated using user.mail instead of the uset.principalname.<br>
                                   You can use the debug saml dumps to validate and compare the claims passed.<br>
                                   They should contain the original email addresses.<br>
                                   Also see: https://learn.microsoft.com/en-us/azure/active-directory/develop/saml-claims-customization');
            }
            $this->phpsaml::$nameid = $response['nameId'];
        }

        if (!$response['userData'] = $this->samlResponse->getAttributes()) {
            $error['userData'] = 'Invalid userdata in Saml response';
        } else {
            $this->phpsaml::$userdata = $response['userData'];
        }

        if (!$response['nameIdFormat'] = $this->phpsaml::$auth->getNameIdFormat()) {
            $error['userData'] = 'No or invalid nameIdFormat';
        } else {
            $this->phpsaml::$nameidformat = $response['nameIdFormat'];
        }

        if ($response['sessionIndex'] = $this->phpsaml::$auth->getSessionIndex()) {
            $error['userData'] = 'No or invalid sessionIndex';
        } else {
            $this->phpsaml::$sessionindex = $response['sessionIndex'];
        }

        // If debugging is on dump outcomes to file
        if (is_array($error) && (count($error) > 1)) {
            // Print error and exit
            $this->printError('Required elements where not found in samlResponse');
        }
    }

    private function populatePaths(): void
    {
        // Populate paths for inclusion
        $this->pathInfo['glpi'] = dirname(pathinfo(__file__)['dirname'], '3');
        $this->pathInfo['base'] = dirname(pathinfo(__file__)['dirname'], '1');
        $this->pathInfo['lib'] = $this->pathInfo['base'] . self::DS . 'lib';
        $this->pathInfo['inc'] = $this->pathInfo['base'] . self::DS . 'inc';
        $this->pathInfo['front'] = $this->pathInfo['base'] . self::DS . 'front';
        $this->pathInfo['debug'] = $this->pathInfo['base'] . self::DS . 'debug';
        $this->pathInfo['saml2'] = $this->pathInfo['lib'] . self::DS . 'php-saml' . self::DS . 'src' . self::DS . 'Saml2';
        $this->pathInfo['xml'] = $this->pathInfo['lib'] . self::DS . 'xmlseclibs';
    }

    private function loadRequiredClassFiles(): void
    {
        // Load GLPI include file
        if (file_exists($this->pathInfo['glpi'] . self::DS . 'inc' . self::DS . 'includes.php')) {
            require_once $this->pathInfo['glpi'] . self::DS . 'inc' . self::DS . 'includes.php';        //NOSONAR - Cant be included with USE.
        } else {
            $this->printError('Required GLPI include file could not be loaded!');
        }

        // Load XML libs
        if (file_exists($this->pathInfo['xml'] . self::DS . '/xmlseclibs.php')) {
            require_once $this->pathInfo['xml'] . self::DS . '/xmlseclibs.php';                     //NOSONAR - Cant be included with USE.
        } else {
            $this->printError('Required classfile xmlseclibs.php could not be loaded!');
        }

        // Load Saml2 classfiles
        if (!class_exists(OneLogin\Saml2\Settings::class)) {
            foreach (scandir($this->pathInfo['saml2']) as $classFile) {
                if (is_file($this->pathInfo['saml2'] . $classFile) && (substr($element, -4) === '.php')) {
                    require_once $this->pathInfo['saml2'] . $classFile;                           //NOSONAR - Cant be included with USE.
                } else {
                    $this->printError('Required Saml2 classfiles could not be loaded!');
                }
            }
        }

    }

    private function getDebugConfig(): void
    {
        $samlConfig = new PluginPhpsamlConfig;
        $config = $samlConfig->getConfig();
        if ($config[PluginPhpsamlConfig::DEBUG]) {
            $this->debug = true;
        }
    }

    public function printError(string $msg): void
    {
        global $CFG_GLPI;
        Toolbox::logInFile("php-errors", $msg . "\n", true);
        Html::nullHeader("Login", $CFG_GLPI["root_doc"] . '/index.php');
        echo '<div class="center b">' . $msg . '<br><br>';
        // Logout with noAUto to manage auto_login with errors
        echo '<a href="' . $CFG_GLPI["root_doc"] . '/index.php">' . __('Log in again') . '</a></div>';
        Html::nullFooter();
        exit();
    }

    private function dumpSamlResponse(): void
    {
        // Make sure the dump cannot be viewed via a webserver
        $data = "<?php /*\n";
        // Process the response
        if (gettype($this->samlResponse) == 'object') {
            $objVars = get_object_vars($this->samlResponse);
            $objMethods = get_class_methods($this->samlResponse);
            $data .= "\n\n Unpacked SamlResponse Methods:\n" . print_r($objMethods, true);
            $data .= "\n\n Unpacked SamlResponse vars:\n" . print_r($objVars, true);
        }
        $data .= "\n\n POST:\n" . print_r($_POST, true);
        $data .= "\n\n GET:\n" . print_r($_GET, true);

        if (is_dir($this->pathInfo['debug'])) {
            // Dump the data if a debug folder is created
            $dumpfile = '/debug_dump-' . date('Y-m-d-H:i:s') . '.php';
            file_put_contents($this->pathInfo['debug'] . $dumpfile, $data);
        } else {
            Toolbox::logInFile("php-errors", "INFO: Debugging is enabled but debug dir is not present in plugin folder.\n", true);
        }
    }

    public static function checkDebugDir(): bool
    {
        $debugdir = dirname(pathinfo(__file__)['dirname'], '1') . self::DS . 'debug';
        return (is_dir($debugdir)) ? true : false;
    }
}
