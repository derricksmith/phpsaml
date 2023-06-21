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

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}


class PluginPhpsamlRuleRightCollection extends RuleCollection {

  /**
     * @see Rule::getCriterias()
     **/
	public $stop_on_first_match = false;

  /**
     * @see Rule::getCriterias()
     **/
	static $rightname = "rule_ldap";
	

  /**
     * @see Rule::getCriterias()
     **/
	public $menu_option = "";

 /**
     * @see Rule::getCriterias()
     **/
   // public $menu_type = "";


  /**
     * @see Rule::getCriterias()
     **/
	function getTitle() 
	{
		return __('Import rules', 'phpsaml');
	}

}
