<?php
/**
 * SAMPLE Code to demonstrate how to handle a SAML assertion response.
 *
 * Your IdP will usually want your metadata, you can use this code to generate it once,
 * or expose it on a URL so your IdP can check it periodically.
 */
include ('../../../inc/includes.php');
require_once GLPI_ROOT.'/plugins/phpsaml/lib/xmlseclibs/xmlseclibs.php';
$libDir = GLPI_ROOT.'/plugins/phpsaml/lib/php-saml/src/Saml2/';
		
$folderInfo = scandir($libDir);

foreach ($folderInfo as $element) {
	if (is_file($libDir.$element) && (substr($element, -4) === '.php')) {
		require_once $libDir.$element;
	}
}

use OneLogin\Saml2\Metadata;
use OneLogin\Saml2\Settings;
header('Content-Type: text/xml');
$samlSettings = new Settings();
$sp = $samlSettings->getSPData();
$samlMetadata = Metadata::builder($sp);
echo $samlMetadata;
