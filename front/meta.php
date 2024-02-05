<?php
/**
 *  ------------------------------------------------------------------------
 *  Derrick Smith - PHP SAML Plugin
 *  Copyright (C) 2014 by Derrick Smith
 *  ------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of PHP SAML Plugin project.
 *
 * PHP SAML Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHP SAML Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with PHP SAML Plugin. If not, see <http://www.gnu.org/licenses/>.
 *
 * ------------------------------------------------------------------------
 *
 *  @package        PhpSaml - IDP Metadata endpoint (Dont use)
 *  @version        1.3.0
 *  @author         Derrick Smith
 *  @author         Chris Gralike
 *  @copyright      Copyright (c) 2018 by Derrick Smith
 *  @license        GPLv2+
 *  @since          1.3.0
 * ------------------------------------------------------------------------
 **/

include_once '../../../inc/includes.php';                                   //NOSONAR - Cant be included with USE.

use OneLogin\Saml2\Metadata;

// Quick fix for: https://github.com/derricksmith/phpsaml/issues/140
// This is still problematic on errors and might not work properly.
header('Content-Type: text/xml');
$config = PluginPhpsamlPhpsaml::getSettings();
$samlMetadata = Metadata::builder($config['sp'],
                                  $config['security']['authnRequestsSigned'],
                                  false);

                                
$samlMetadata = Metadata::addX509KeyDescriptors($samlMetadata, $config['sp']['x509cert'], $wantsEncrypted = false);

echo $samlMetadata;
