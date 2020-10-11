<?php

if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', '../../..');
}


$post = $_POST;
unset($_POST);

include (GLPI_ROOT.'/inc/includes.php');

try {
    if (isset($post['SAMLResponse'])) {
        $samlSettings = new OneLogin\Saml2\Settings(PluginPhpsamlPhpsaml::$phpsamlsettings);
        $samlResponse = new OneLogin\Saml2\Response($samlSettings, $post['SAMLResponse']);
        if ($samlResponse->isValid()) {
			PluginPhpsamlPhpsaml::$nameid = $samlResponse->getNameId();
			PluginPhpsamlPhpsaml::$userdata = $samlResponse->getAttributes();
			PluginPhpsamlPhpsaml::$nameidformat = PluginPhpsamlPhpsaml::$auth->getNameIdFormat();
			PluginPhpsamlPhpsaml::$sessionindex = PluginPhpsamlPhpsaml::$auth->getSessionIndex();
			try {
				PluginPhpsamlPhpsaml::glpiLogin((isset($post['RelayState']) && $post['RelayState'] != '' ? $post['RelayState'] : ''));
			} catch(Exception $e) {
				Toolbox::logInFile("php-errors", $e->getMessage() . "\n", true);
			}
        } else {
			Toolbox::logInFile("php-errors", 'Invalid SAML Response' . "\n", true);
        }
    } else {
		Toolbox::logInFile("php-errors", 'No SAML Response found in POST.' . "\n", true);
    }
} catch (Exception $e) {
	Toolbox::logInFile("php-errors", 'Invalid SAML Response: ' . $e->getMessage() . "\n", true);
}
