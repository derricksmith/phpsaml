<?php
/**
*  ------------------------------------------------------------------------
*  Copyright (C) 2023 by Chris Gralike, Derrick Smith
*  ------------------------------------------------------------------------
*
* LICENSE
*
* This file is part of phpSaml2.
*
* Ticket Filter plugin is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Ticket Filter is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with ticket filter. If not, see <http://www.gnu.org/licenses/>.
*
* ------------------------------------------------------------------------
*
*  @package  	phpSaml2
*  @version	    1.0.0
*  @author    	Chris Gralike
*  @copyright 	Copyright (c) 2023 by Derrick Smith
*  @license   	MIT
*  @see       	https://github.com/DonutsNL/phpSaml2/readme.md
*  @link		https://github.com/DonutsNL/phpSaml2
*  @since     	0.1
* ------------------------------------------------------------------------
**/

include ('../../../inc/includes.php');

use GlpiPlugin\Phpsaml2;
use OneLogin\Saml2\Metadata;
use OneLogin\Saml2\Settings;





header('Content-Type: text/xml');
$samlSettings = new Settings();
$sp = $samlSettings->getSPData();
$samlMetadata = Metadata::builder($sp);
echo $samlMetadata;
