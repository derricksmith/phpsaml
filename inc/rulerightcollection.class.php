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
 *  @package    phpsaml - main User creation rulescollection class
 *  @version    1.3.0
 *  @author     Derrick Smith
 *  @author     Chris Gralike
 *  @copyright  Copyright (c) 2018 by Derrick Smith
 *  @license    MIT
 *  @see        https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 *  @link       https://github.com/derricksmith/phpsaml/
 *  @since      1.3.0
 * ------------------------------------------------------------------------
 **/

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}


class PluginPhpsamlRuleRightCollection extends RuleCollection {

  /**
     * @see Rule::getCriterias()
     **/
    public $stop_on_first_match = false;                    //NOSONAR - Default GLPI property name

  /**
     * @see Rule::getCriterias()
     **/
    static $rightname = "config";


  /**
     * @see Rule::getCriterias()
     **/
    public $menu_option = "";                               //NOSONAR - Default GLPI property name

  /**
     * @see Rule::getCriterias()
     **/
    public function getTitle()
    {
        return __('Import rules', 'phpsaml');
    }

}
