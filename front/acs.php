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
 *  @version	1.2.2
 *  @author    	Derrick Smith
 *  @copyright 	Copyright (c) 2018 by Derrick Smith
 *  @license   	MIT
 *  @see       	https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 *  @link		https://github.com/derricksmith/phpsaml/
 *  @since     	0.1
 *  @todo		This file needs refactoring. It currently is not providing any usefull feedback on errors
 * 				Additional logging might be a nice to have as well.
 * ------------------------------------------------------------------------
 **/

if (defined('GLPI_ROOT')) {
    $glpi_root = GLPI_ROOT;
} else {
    $glpi_root = '../../..';
}


// Capture the post preventing
// GLPI from cleaning it.
$post = $_POST;
$_POST = '';

// This code is reused on various locations.
require_once $glpi_root.'/plugins/phpsaml/lib/xmlseclibs/xmlseclibs.php';
$libDir = $glpi_root.'/plugins/phpsaml/lib/php-saml/src/Saml2/';

$folderInfo = scandir($libDir);

foreach ($folderInfo as $element) {
	if (is_file($libDir.$element) && (substr($element, -4) === '.php')) {
		require_once $libDir.$element;
	}
}

include ($glpi_root.'/inc/includes.php');

use OneLogin\Saml2\Settings;
use OneLogin\Saml2\Response;

if(!empty($post) && is_array($post))
{
	// Maybe in the future add ?debug to the URI to trigger this;
	dumpPost($post);
}

$error = null;
$phpsaml = new PluginPhpsamlPhpsaml();

try {
    if (isset($post['SAMLResponse'])) {
		$settings = $phpsaml::$phpsamlsettings;
        $samlSettings = new OneLogin\Saml2\Settings($settings);
        $samlResponse = new OneLogin\Saml2\Response($samlSettings, $post['SAMLResponse']);
        if ($samlResponse->isValid()) {
			$phpsaml::auth();
			$phpsaml::$nameid = $samlResponse->getNameId();
			$phpsaml::$userdata = $samlResponse->getAttributes();
			$phpsaml::$nameidformat = $phpsaml::$auth->getNameIdFormat();
			$phpsaml::$sessionindex = $phpsaml::$auth->getSessionIndex();
			try {
				$phpsaml::glpiLogin((isset($post['RelayState']) && $post['RelayState'] != '' ? $post['RelayState'] : ''));
			} catch(Exception $e) {
				$error = $e->getMessage();
				Toolbox::logInFile("php-errors", $error . "\n", true);
			}
        } else {
			$error = "Invalid SAML Response, strict mode enabled without a valid SP certificate?";
			Toolbox::logInFile("php-errors", $error . "\n", true);
        }
    } else {
		$error = "No SAML Response found in POST.";
		Toolbox::logInFile("php-errors", $error . "\n", true);
    }
} catch (Exception $e) {
	$error = $e->getMessage();
	Toolbox::logInFile("php-errors", $error . "\n", true);
}

if($error){
	Html::nullHeader("Login", $CFG_GLPI["root_doc"] . '/index.php');
	echo '<div class="center b">'.$error.'<br><br>';
	// Logout whit noAUto to manage auto_login with errors
	echo '<a href="' . $CFG_GLPI["root_doc"] .'/index.php">' .__('Log in again') . '</a></div>';
	Html::nullFooter();
	exit();
}

function dumpPost($post) 
{
	$dumpfile = pathinfo(__file__)['dirname'].DIRECTORY_SEPARATOR.'/debug_dump-'.date('Y-m-d-H:i:s').'.php';
	// Make sure the dump cannot be viewed via a webserver
	$data = "<?php /*\n". print_r($post, true);
	if($post['SAMLResponse']) {
		$phpsaml = new PluginPhpsamlPhpsaml();
		$settings = $phpsaml::$phpsamlsettings;
		$samlSettings = new OneLogin\Saml2\Settings($settings);
        $samlResponse = new OneLogin\Saml2\Response($samlSettings, $post['SAMLResponse']);
		if(is_object($samlResponse)){
			$contents = get_object_vars($samlResponse);
		}else{
			$contents = 'noObject';
		}
		$data .= "\n\n Unpacked:\n".print_r($contents, true);
	}
	
	$data .= "\n\n POST:\n". print_r($_POST, true);
	$data .= "\n\n GET:\n". print_r($_GET, true);
	$data .= "\n\n SERVER\n". print_r($_SERVER, true); 
	file_put_contents($dumpfile, $data);
}