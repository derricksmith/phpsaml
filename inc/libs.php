<?php
if (!defined('GLPI_ROOT')) { define('GLPI_ROOT', '../../..'); }

// Plugins or marketplace location?
$phpSamlPath = (strpos(dirname(__FILE__), 'plugins') !== false) ? '/plugins/phpsaml' : '/marketplace/phpsaml';

require_once GLPI_ROOT . $phpSamlPath . '/lib/xmlseclibs/xmlseclibs.php';

$libDir = GLPI_ROOT . $phpSamlPath . '/lib/php-saml/src/Saml2/';

// Load the libs
$folderInfo = scandir($libDir);
foreach ($folderInfo as $element) {
	if (is_file($libDir.$element) && (substr($element, -4) === '.php')) {
		require_once $libDir.$element;
	}
}

