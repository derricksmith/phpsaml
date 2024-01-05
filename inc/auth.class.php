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
 *  @package  	phpsamlconfig
 *  @version	1.3.0
 *  @author    	Derrick Smith
 *  @author	   	Chris Gralike
 *  @copyright 	Copyright (c) 2018 by Derrick Smith
 *  @license   	MIT
 *  @see       	https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 *  @link		https://github.com/derricksmith/phpsaml/
 *  @since     	0.1
 * ------------------------------------------------------------------------
 **/

class  PluginPhpsamlAuth extends Auth
{

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * @param string $userName
     * @return $this
     */
    public function loadUserData($userName)
    {
        if ($this->user->getFromDBbyName(addslashes($userName)) != '') {
			return $this;
		} elseif ($this->user->getFromDBbyEmail(addslashes($userName)) != '') {
			return $this;
		}
    }

    /**
     * @return bool
     */
    public function checkUserData()
    {
        $this->auth_succeded = (bool)$this->user->fields;
        return $this->auth_succeded;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
