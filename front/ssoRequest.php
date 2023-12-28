<?php
/**
 * SAMPLE Code to demonstrate how to initiate a SAML Authorization request
 *
 * When the user visits this URL, the browser will be redirected to the SSO
 * IdP with an authorization request. If successful, it will then be
 * redirected to the consume URL (specified in settings) with the auth
 * details.
 */
session_start();
include ('../../../inc/includes.php');
require_once GLPI_ROOT.'/plugins/phpsaml/lib/xmlseclibs/xmlseclibs.php';
$libDir = GLPI_ROOT.'/plugins/phpsaml/lib/php-saml/src/Saml2/';
		
$folderInfo = scandir($libDir);

foreach ($folderInfo as $element) {
	if (is_file($libDir.$element) && (substr($element, -4) === '.php')) {
		require_once $libDir.$element;
	}
}
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;
$auth = new OneLogin\Saml2\Auth();

if (!isset($_SESSION['samlUserdata'])) {
    $auth->login();
} else {
    $indexUrl = str_replace('/sso.php', '/index.php', Utils::getSelfURLNoQuery());
    Utils::redirect($indexUrl);
}