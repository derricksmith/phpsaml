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
	die("Sorry. You can't access this file directly");
}


class PluginPhpsamlRuleRight extends Rule{

    /**
     * Define Rights
     * defines the rights a user must posses to be able to access this menu option in the rules section
     **/
    static $rightname = 'rule_ldap'; #using same as src/rulerightCollection.class.php
    
    /**
     * Define order
     * defines how to order the list
     **/
    public $orderby   = "name";

    /**
     * getTitle
     * @return Title to use in Rules list
     **/
	public function getTitle()
    {
        return __('SAML rules', 'phpsaml');
    }

     /**
     * getIcon
     * @return icon to use in rules list
     **/
	public static function getIcon()
    {
        return Profile::getIcon();
    }

    /**
     * @see Rule::getCriterias()
     * @return returns available criteria
     **/
    public function getCriterias()
    {
        static $criterias = [];

        if (!count($criterias)) {
            $criterias['common']             = __('Global criteria');
            
            $criterias['_useremails']['table']      = '';
            $criterias['_useremails']['field']      = '';
            $criterias['_useremails']['name']       = _n('Email', 'Emails', 1);
            $criterias['_useremails']['linkfield']  = '';
            $criterias['_useremails']['virtual']    = true;
            $criterias['_useremails']['id']         = '_useremails';
            
        }
        return $criterias;
    }

    /**
     * @see Rule::getActions()
     **/
    public function getActions()
    {

        $actions                                                = parent::getActions();

        $actions['entities_id']['name']                         = Entity::getTypeName(1);
        $actions['entities_id']['type']                         = 'dropdown';
        $actions['entities_id']['table']                        = 'glpi_entities';

        $actions['profiles_id']['name']                         = _n('Profile', 'Profiles', Session::getPluralNumber());
        $actions['profiles_id']['type']                         = 'dropdown';
        $actions['profiles_id']['table']                        = 'glpi_profiles';

        $actions['is_recursive']['name']                        = __('Recursive');
        $actions['is_recursive']['type']                        = 'yesno';
        $actions['is_recursive']['table']                       = '';

        $actions['is_active']['name']                           = __('Active');
        $actions['is_active']['type']                           = 'yesno';
        $actions['is_active']['table']                          = '';

        $actions['_entities_id_default']['table']                = 'glpi_entities';
        $actions['_entities_id_default']['field']               = 'name';
        $actions['_entities_id_default']['name']                = __('Default entity');
        $actions['_entities_id_default']['linkfield']           = 'entities_id';
        $actions['_entities_id_default']['type']                = 'dropdown';

        $actions['specific_groups_id']['name']                  = Group::getTypeName(Session::getPluralNumber());
        $actions['specific_groups_id']['type']                  = 'dropdown';
        $actions['specific_groups_id']['table']                 = 'glpi_groups';

        $actions['groups_id']['table']                        = 'glpi_groups';
        $actions['groups_id']['field']                        = 'name';
        $actions['groups_id']['name']                         = __('Default group');
        $actions['groups_id']['linkfield']                    = 'groups_id';
        $actions['groups_id']['type']                         = 'dropdown';
        $actions['groups_id']['condition']                    = ['is_usergroup' => 1];

        $actions['_profiles_id_default']['table']             = 'glpi_profiles';
        $actions['_profiles_id_default']['field']             = 'name';
        $actions['_profiles_id_default']['name']              = __('Default profile');
        $actions['_profiles_id_default']['linkfield']         = 'profiles_id';
        $actions['_profiles_id_default']['type']              = 'dropdown';

        $actions['timezone']['name']                          = __('Timezone');
        $actions['timezone']['type']                          = 'timezone';

        return $actions;
    }
}