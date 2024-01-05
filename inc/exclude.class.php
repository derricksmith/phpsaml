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
 *  @author    	Chris Gralike
 *  @author	   	Derrick Smith
 *  @copyright 	Copyright (c) 2018 by Derrick Smith
 *  @license   	MIT
 *  @see       	https://github.com/derricksmith/phpsaml/blob/master/LICENSE.txt
 *  @link		https://github.com/derricksmith/phpsaml/
 *  @since     	1.2.2
 * ------------------------------------------------------------------------
 **/

class PluginPhpsamlExclude extends CommonDropdown
{

    /**
     * Table fields
     */
    const NAME              = 'name';
    const ACTIVE            = 'is_active';
    const DATE_CREATION     = 'date_creation';
    const DATE_MOD          = 'date_mod';
    const CLIENTAGENT       = 'ClientAgent';
    const EXCLUDEPATH       = 'ExcludePath';

     /**
     * getTypeName(int nb) : string -
     * Method called by pre_item_add hook validates the object and passes
     * it to the RegEx Matching then decides what to do.
     *
     * @param  int      $nb     number of items.
     * @return void
     */
    public static function getTypeName($nb = 0) : string
    {
        if ($nb > 0) {
           return __('Exclude', 'phpsaml');
        }
        return __('Exclude', 'phpsaml');
    }


    /**
     * getMenuContent() : array | bool -
     * Method called by pre_item_add hook validates the object and passes
     * it to the RegEx Matching then decides what to do.
     *
     * @return mixed             boolean|array
     */
    public static function getMenuContent()
    {
        $menu = [];
        if (Config::canUpdate()) {
            $menu['title'] = self::getMenuName();
            $menu['page']  = '/' . Plugin::getWebDir('phpsaml', false) . 'front/exclude.php';
            $menu['icon']  = self::getIcon();
        }
        if (count($menu)) {
            return $menu;
        }
        return false;
    }

    /**
     * getIcon() : string -
     * Sets icon for object.
     *
     * @return string   $icon
     */
    public static function getIcon() : string
    {
        return 'fas fa-filter';
    }

    /**
     * getAdditionalFields() : array -
     * Fetch fields for Dropdown 'add' form. Array order is equal with
     * field order in the form
     *
     * @return string   $icon
     */
    public function getAdditionalFields()
    {
        return [
            [
                'name'      => 'ClientAgent',
                'label'     => __('Client Agent performing the call', 'phpsaml'),
                'type'      => 'text',
                'list'      => true,
            ],
            [
                'name'      => 'ExcludePath',
                'label'     => __('To be excluded path', 'phpsaml'),
                'type'      => 'text',
                'list'      => true,
                'min'       => 1,
            ],
            [
                'name'      => 'is_active',
                'label'     => __('Active', 'phpsaml'),
                'type'      => 'bool',
            ],
        ];
    }

    /**
     * rawSearchOptions() : array -
     * Add fields to search and potential table columns
     *
     * @return array   $rawSearchOptions
     */
    public function rawSearchOptions() : array
    {
        $tab = parent::rawSearchOptions();

        $tab[] = [
            'id'                 => '5',
            'table'              => $this->getTable(),
            'field'              => 'ClientAgent',
            'name'               => __('Client Agent performing the call', 'phpsaml'),
            'searchtype'         => ['equals', 'notequals'],
            'datatype'           => 'text',
        ];

        $tab[] = [
            'id'                 => '6',
            'table'              => $this->getTable(),
            'field'              => 'ExcludePath',
            'name'               => __('To be excluded path', 'phpsaml'),
            'datatype'           => 'text',
        ];
        return $tab;
    }


    /**
     * getExcludes() : array -
     * Get configured excludes from dropdowns
     *
     * @return patterns              Array with all configured patterns
     * @since                        1.1.0
     */
    public static function getExcludes() : array
    {
        global $DB;
        $excludes = [];
        $dropdown = new FilterPattern();
        $table = $dropdown::getTable();
        foreach($DB->request($table) as $id => $row){
            $excludes[] = [self::NAME                => $row[self::NAME],
                           self::ACTIVE              => $row[self::ACTIVE],
                           self::DATE_CREATION       => $row[self::DATE_CREATION],
                           self::DATE_MOD            => $row[self::DATE_MOD],
                           self::CLIENTAGENT         => $row[self::CLIENTAGENT],
                           self::EXCLUDEPATH         => $row[self::EXCLUDEPATH]];
        }
        return $excludes;
    }

    /**
     * install(Migration migration) : void -
     * Install table needed for Ticket Filter configuration dropdowns
     *
     * @return void
     * @see             hook.php:plugin_ticketfilter_install()
     */
    public static function install(Migration $migration) : void
    {
        global $DB;
        $default_charset = DBConnection::getDefaultCharset();
        $default_collation = DBConnection::getDefaultCollation();
        $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

        $table = self::getTable();

        // Create the base table if it does not yet exist;
        // Dont update this table for later versions, use the migration class;
        if (!$DB->tableExists($table)) {
            $migration->displayMessage("Installing $table");
            $query = <<<SQL
            CREATE TABLE IF NOT EXISTS `$table` (
            `id`                        int {$default_key_sign} NOT NULL AUTO_INCREMENT,
            `name`                      varchar(255) DEFAULT NULL,
            `comment`                   text,
            `is_active`                 tinyint NOT NULL DEFAULT '0',
            `date_creation`             timestamp NULL DEFAULT NULL,
            `date_mod`                  timestamp NULL DEFAULT NULL,
            `ClientAgent`               text NOT NULL,
            `ExcludePath`               text NOT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `is_active` (`is_active`),
            KEY `date_creation` (`date_creation`),
            KEY `date_mod` (`date_mod`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;
            SQL;
            $DB->query($query) or die($DB->error());

            // insert default excludes;
            $query = <<<SQL
            INSERT INTO `$table`(name, comment, is_active, ClientAgent, ExcludePath)
            VALUES('Fusion Inventory 1', 'backport configuration', '1', 'FusionInventory-Agent', '/plugins/fusioninventory/');
            SQL;
            $DB->query($query) or die($DB->error());

            // insert default excludes;
            $query = <<<SQL
            INSERT INTO `$table`(name, comment, is_active, ClientAgent, ExcludePath)
            VALUES('Fusion Inventory 2', '', '1', 'FusionInventory-Agent', '/marketplace/fusioninventory/');
            SQL;
            $DB->query($query) or die($DB->error());

            // insert default excludes;
            $query = <<<SQL
            INSERT INTO `$table`(name, comment, is_active, ClientAgent, ExcludePath)
            VALUES('Fusion Inventory 3', 'backport configuration', '1', 'FusionInventory-Agent', '/plugins/glpiinventory/');
            SQL;
            $DB->query($query) or die($DB->error());

            // insert default excludes;
            $query = <<<SQL
            INSERT INTO `$table`(name, comment, is_active, ClientAgent, ExcludePath)
            VALUES('Fusion Inventory 3', '', '1', 'FusionInventory-Agent', '/marketplace/glpiinventory/');
            SQL;
            $DB->query($query) or die($DB->error());
        }
    }

    /**
     * uninstall(Migration migration) : void -
     * Uninstall tables uncomment the line to make plugin clean table.
     *
     * @return void
     * @see             hook.php:plugin_ticketfilter_uninstall()
     */
    public static function uninstall(Migration $migration) : void
    {
        $table = self::getTable();
        $migration->displayMessage("Uninstalling $table");
        $migration->dropTable($table);
    }
}
