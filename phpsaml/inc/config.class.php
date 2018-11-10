<?php

/*
   ------------------------------------------------------------------------
   Barcode
   Copyright (C) 2009-2016 by the Barcode plugin Development Team.

   https://forge.indepnet.net/projects/barscode
   ------------------------------------------------------------------------

   LICENSE

   This file is part of barcode plugin project.

   Plugin Barcode is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Plugin Barcode is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with Plugin Barcode. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   Plugin Barcode
   @author    David Durieux
   @co-author
   @copyright Copyright (c) 2009-2016 Barcode plugin Development team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://forge.indepnet.net/projects/barscode
   @since     2009

   ------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginPhpsamlConfig extends CommonDBTM {

   static $rightname = 'plugin_phpsaml_config';


   function __construct() {
      $this->table = "glpi_plugin_phpsaml_configs";
   }

   function showForm(){
      global $CFG_GLPI;

      $config = PluginPhpsamlConfig::getConfig();
      echo "<form name='form' method='post'
                  action='".$CFG_GLPI['root_doc']."/plugins/phpsaml/front/config.form.php'>";

      echo "<div align='center'>";
	  echo "<input type='hidden' name='id' value='1'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='4'>".__('PHP SAML Plugin Configuration', 'phpsaml')."</th></tr>";
	  
	  echo "<tr>";
	  echo "<th colspan='4'>".__('General', 'phpsaml')."</th>";
      echo "<td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Strict', 'phpsaml')."</td>";
      echo "<td>";
	  echo "<select name='strict'><option value='1' ".((isset($config['strict']) && $config['strict'] == 1) ? 'selected' : '').">Yes</option><option value='0' ".((isset($config['strict']) && $config['strict'] == 0) || !isset($config['strict'])  ? 'selected' : '').">No</option></select>";
      echo "</td>";
	  echo "<td>".__('Debug', 'phpsaml')."</td>";
      echo "<td>";
	  echo "<select name='debug'><option value='1' ".((isset($config['debug']) && $config['debug'] == 1) ? 'selected' : '').">Yes</option><option value='0' ".((isset($config['debug']) && $config['debug'] == 0) || !isset($config['debug']) ? 'selected' : '').">No</option></select>";
      echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr>";
	  echo "<th colspan='4'>".__('SP Configuration', 'phpsaml')."</th>";
      echo "<td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2'>".__('SP Certificate', 'phpsaml')."</td>";
      echo "<td colspan='2'>";
      echo "<textarea name='saml_sp_certificate'>".(isset($config['saml_sp_certificate']) ? $config['saml_sp_certificate'] : '')."</textarea>";
      echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2'>".__('SP Certificate Key', 'phpsaml')."</td>";
      echo "<td colspan='2'>";
      echo "<textarea name='saml_sp_certificate_key'>".(isset($config['saml_sp_certificate_key']) ? $config['saml_sp_certificate_key'] : '')."</textarea>";
      echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr>";
	  echo "<th colspan='4'>".__('IdP Configuration', 'phpsaml')."</th>";
      echo "<td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2'>".__('IdP Entity ID', 'phpsaml')."</td>";
      echo "<td colspan='2'>";
      echo "<input type='text' size='20' name='saml_idp_entity_id' value='".(isset($config['saml_idp_entity_id']) ? $config['saml_idp_entity_id'] : '')."'>";
      echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2'>".__('IdP SSO Service URL', 'phpsaml')."</td>";
      echo "<td colspan='2'>";
      echo "<input type='text' size='20' name='saml_idp_single_sign_on_service' value='".(isset($config['saml_idp_single_sign_on_service']) ? $config['saml_idp_single_sign_on_service'] : '')."'>";
      echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2'>".__('IdP SLO Service URL', 'phpsaml')."</td>";
      echo "<td colspan='2'>";
      echo "<input type='text' size='20' name='saml_idp_single_logout_service' value='".(isset($config['saml_idp_single_logout_service']) ? $config['saml_idp_single_logout_service'] : '')."'>";
      echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2'>".__('IdP Public X509 Certificate', 'phpsaml')."</td>";
      echo "<td colspan='2'>";
      echo "<textarea name='saml_idp_certificate'>".(isset($config['saml_idp_certificate']) ? $config['saml_idp_certificate'] : '')."</textarea>";
      echo "</td>";
	  echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
      echo "<td colspan='4'><input type='submit' name='update' value=\"" . __("Update") . "\" class='submit' ></td>";
      echo "</tr>";
      
	  echo "</table><br>";

      echo "</div>";
      Html::closeForm();
   }

   function getConfig() {
      $phpsamlconf = new PluginPhpsamlConfig();
      if ($phpsamlconf->getFromDB(1)) {
         $config = array(
			'strict'							=> $phpsamlconf->fields['strict'],
			'debug'							=> $phpsamlconf->fields['debug'],
			'saml_sp_certificate' 				=> $phpsamlconf->fields['saml_sp_certificate'],
			'saml_sp_certificate_key' 			=> $phpsamlconf->fields['saml_sp_certificate_key'],
			'saml_idp_entity_id' 				=> $phpsamlconf->fields['saml_idp_entity_id'],
			'saml_idp_single_sign_on_service' 	=> $phpsamlconf->fields['saml_idp_single_sign_on_service'],
			'saml_idp_single_logout_service' 	=> $phpsamlconf->fields['saml_idp_single_logout_service'],
			'saml_idp_certificate' 				=> $phpsamlconf->fields['saml_idp_certificate']
		);
      } else {
         $config = array();
      }
      return $config;
   }
}
