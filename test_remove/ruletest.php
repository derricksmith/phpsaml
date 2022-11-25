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

class testUser{
    public function __construct(){
        echo "<pre>";
        $rand = rand(0,9999);

        //

        // New User details 
        $createUser = [
            'entities_id'   => 0,
            'name'          => 'pietje.puk'.$rand.'@test.tld',
            'realname'      => 'Puk'.$rand,
            'firstname'     => 'Pietje'.$rand,
            '_useremails'   => ['pietje.puk'.$rand.'@test.tld'],
            'password'      => 'TestPassword123',
            'password2'     => 'TestPassword123'
        ];

        $matchInput = ['_useremails' => $createUser['_useremails']];

        $PhpSamlRuleCollection = new PluginPhpsamlRuleRightCollection();
        
        $user = new User();
        $id = $user->add($createUser);
        Echo "<pre>User $id created!<br></pre>";

        $data = $PhpSamlRuleCollection->processAllRules($matchInput, [], ['class'=>$user]);
        print_r($data);
    }
}

$test = new testUser();

