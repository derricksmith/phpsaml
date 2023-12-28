<?php
if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', '../../..');
}
require_once GLPI_ROOT .'/plugins/phpsaml/lib/xmlseclibs/xmlseclibs.php';
$libDir = GLPI_ROOT .'/plugins/phpsaml/lib/php-saml/src/Saml2/';
$folderInfo = scandir($libDir);

foreach ($folderInfo as $element) {
	if (is_file($libDir.$element) && (substr($element, -4) === '.php')) {
		require_once $libDir.$element;
	}
}

