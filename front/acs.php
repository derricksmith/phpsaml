<?php
// Bypass csrf protection since we are not posting to glpi

if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', '../../..');
}


$post = $_POST;
unset($_POST);

include (GLPI_ROOT.'/inc/includes.php');

try {
    if (isset($post['SAMLResponse'])) {
		print_r($phpsamlsettings);
        $samlSettings = new OneLogin\Saml2\Settings(PluginPhpsamlPhpsaml::$phpsamlsettings);
        $samlResponse = new OneLogin\Saml2\Response($samlSettings, $post['SAMLResponse']);
        if ($samlResponse->isValid()) {
			PluginPhpsamlPhpsaml::$nameid = $samlResponse->getNameId();
			PluginPhpsamlPhpsaml::$userdata = $samlResponse->getAttributes();
			PluginPhpsamlPhpsaml::$nameidformat = PluginPhpsamlPhpsaml::$auth->getNameIdFormat();
			PluginPhpsamlPhpsaml::$sessionindex = PluginPhpsamlPhpsaml::$auth->getSessionIndex();
			try {
				PluginPhpsamlPhpsaml::glpiLogin($post['RelayState']);
			} catch(Exception $e) {
				echo $e->getMessage();
			}
        } else {
            echo 'Invalid SAML Response';
        }
    } else {
        echo 'No SAML Response found in POST.';
    }
} catch (Exception $e) {
    echo 'Invalid SAML Response: ' . $e->getMessage();
}
