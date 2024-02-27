<?php
if (defined('GLPI_ROOT')) {
    $glpi_root = GLPI_ROOT;
} else {
    $glpi_root = '../../..';
}


$post = $_POST;
unset($_POST);
$_POST = array();

include ($glpi_root.'/inc/includes.php');

require_once $glpi_root.'/plugins/phpsaml/lib/xmlseclibs/xmlseclibs.php';
$libDir = $glpi_root.'/plugins/phpsaml/lib/php-saml/src/Saml2/';

$folderInfo = scandir($libDir);

foreach ($folderInfo as $element) {
	if (is_file($libDir.$element) && (substr($element, -4) === '.php')) {
		require_once $libDir.$element;
	}
}

use OneLogin\Saml2\Settings;
use OneLogin\Saml2\Response;

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
			$error = "Invalid SAML Response";
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
