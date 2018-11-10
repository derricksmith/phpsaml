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
die();

$phpsaml = new PluginPhpsamlPhpsaml();

$auth = new OneLogin\Saml2\Auth();
if (!isset($_SESSION['samlUserdata'])) {
	$auth->login();
} else {
    $indexUrl = str_replace('/sso.php', '/index.php', Utils::getSelfURLNoQuery());
    Utils::redirect($indexUrl);
}

