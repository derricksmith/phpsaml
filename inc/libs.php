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
 *  @package    libs
 *  @version    1.3.0
 *  @author     Derrick Smith
 *  @copyright  Copyright (c) 2018 by Derrick Smith
 *  @license    MIT
 *  @see        https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 *  @link       https://github.com/derricksmith/phpsaml/
 *  @since      0.1
 * ------------------------------------------------------------------------
 **/

if (!defined('GLPI_ROOT')) { define('GLPI_ROOT', '../../..'); }

// Plugins or marketplace location?
$phpSamlPath = (strpos(dirname(__FILE__), 'plugins') !== false) ? '/plugins/phpsaml' : '/marketplace/phpsaml';

require_once GLPI_ROOT . $phpSamlPath . '/lib/xmlseclibs/xmlseclibs.php';            //NOSONAR - Cant be included with USE keyword.

$libDir = GLPI_ROOT . $phpSamlPath . '/lib/php-saml/src/Saml2/';

// Load the libs
$folderInfo = scandir($libDir);
foreach ($folderInfo as $element) {
    if (is_file($libDir.$element) && (substr($element, -4) === '.php')) {
        require_once $libDir.$element;                                              //NOSONAR - Cant be included with USE keyword.
    }
}

