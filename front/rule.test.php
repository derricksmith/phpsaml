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

   @package   phpsaml
   @author    Chris Gralike
   @co-author
   @copyright Copyright (c) 2018 by Derrick Smith
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @since     2018

   ------------------------------------------------------------------------
 */

include_once('../../../inc/includes.php');


// Generate a random password
$password   = bin2hex(random_bytes(20));
$randomName = bin2hex(random_bytes(5));

$usr = [ 
  'name'  => $randomName,
  'realname' => $randomName,
  'firstname' => $randomName,
  'email'     => $randomName.'@voorbeeld.tld',
];

$input = [
  'name'        => $usr['name'],
  'realname'    => $usr['realname'],
  'firstname'   => $usr['firstname'],
  '_useremails' => [$usr['email']],
  'password'    => $password,
  'password2'   => $password,
  '_ruleright_process' => true];

echo "<pre>";
var_dump($usr);
echo "<br/>";
var_dump($input);
// Load the rulesEngine and process them
$phpSamlRuleCollection = new PluginPhpsamlRuleRightCollection();
$matchInput = ['_useremails' => $input['_useremails']];
$out['_ldap_rules'] = $phpSamlRuleCollection->processAllRules($matchInput, [], [], []);
echo "<br/>";
var_dump($out);

if($out['_ldap_rules']['_rule_process'] > 0) {
  $input  = array_merge($input, $out);
  
}

echo "<br/>";
  var_dump($input);

$newUser = new User();
var_dump($uid = $newUser->add($input));
echo "<br/>";
var_dump($newUser->applyRightRules());

