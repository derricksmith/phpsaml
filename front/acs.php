<?php
/*
   ------------------------------------------------------------------------
   Derrick Smith - PHP SAML Plugin
   Copyright (C) 2014 by Derrick Smith
   ------------------------------------------------------------------------

   LICENSE

   This file is part of phpsaml project.

   PHP SAML Plugin is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   phpsaml is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with phpsaml. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   phpsamlconfig
   @author    Chris Gralike
   @co-author
   @copyright Copyright (c) 2018 by Derrick Smith
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @since     2018

   @changelog rewrite and restructure removing context switches and improving readability and maintainability
   @changelog breaking config up into methods for maintainability and unit testing purposes.

   ------------------------------------------------------------------------
 */

// Capture the post before GLPI does.
$post = $_POST;

// Use a countable datatype to empty the global
// https://github.com/derricksmith/phpsaml/issues/153
$_POST = [];

// Load GLPI includes
include_once '../../../inc/includes.php';

// Peform assertion
$acs = new PluginPhpsamlAcs();
if(array_key_exists('SAMLResponse', $post)){
    $acs->assertSaml($post);
} else {
    $acs->printError('no SAMLResponse found in POST header');
}
