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
 
use OneLogin\Saml2\Utils;

if (!defined("GLPI_ROOT")) {
    die("Sorry. You can't access directly to this file");
}

class PluginPhpsamlConfig extends CommonDBTM {

   static $rightname = "plugin_phpsaml_config";


   function showForm(){
		global $DB, $CFG_GLPI;
		$config = PluginPhpsamlConfig::getConfig();
		if (isset($_SESSION["phpsaml_messages"])){
			$messages = $_SESSION["phpsaml_messages"];
		} else {
			$messages = self::validate($config);
		}
		
		$query = "SELECT * FROM `glpi_plugin_phpsaml_configs`";
		$result = $DB->query($query) or die("error  ". $DB->error());
		$data = $result->fetch_array();
		?>
		<script>
			$(function() {

				$('#keep-order').multiSelect({ 
					keepOrder: true,
					selectableHeader: '<b><?php echo __('Available', 'phpsaml'); ?></b>',
					selectionHeader: '<b><?php echo __('Selected', 'phpsaml'); ?></b>',
					cssClass: 'multiselect',
					afterSelect: function(value, text){
						var get_val = $("#requested-authn-context").val();
						var array = [];
						if (get_val) var array = get_val.split(',');
						if(!array.includes(value)){ 
							array.push(value);
						}
						var string = array.toString();
						$("#requested-authn-context").val(string);
						
					},
					afterDeselect: function(value, text){
						var get_val = $("#requested-authn-context").val();
						var array = [];
						if (get_val){ 
							var array = get_val.split(',');
						}
						
						var index = array.indexOf(value.toString());
						if (index !== -1) {
						  array.splice(index, 1);
						}
						var string = array.toString();
						$("#requested-authn-context").val(string);
						
						
					}
				});  
				
			});
		</script>
		<style>
			form#phpsaml_config label, form#phpsaml_config input, form#phpsaml_config textbox, form#phpsaml_config textarea, form#phpsaml_config select {
				font-size: 14px;
			}
			
			form#phpsaml_config input, form#phpsaml_config textbox, form#phpsaml_config textarea, form#phpsaml_config select {
				margin-bottom:5px;
			}
			
			.multiselect {
				width: 100%;
			}
			
			.phpsaml_config_wrapper {
			}

			.phpsaml_config_wrapper h1 {
				font-size: 18px;
				font-weight: bold;
				border-radius: 4px;
				padding: 5px;
				border-radius: 0;
				margin: 0;
				padding: 10px 5px;
				background-color: #F1F1F1;
			}
			
			.phpsaml_config_wrapper h2 {
				font-size: 16px;
				font-weight: bold;
				padding: 5px;
				margin-bottom: 10px;
				border-bottom:1px solid #000;
			}
			
			.phpsaml_config_wrapper form {
				display: grid;
				grid-template-columns: 1fr 1fr;
				grid-gap: 5px;
			}
			
			.phpsaml_config_wrapper form label {
			  font-weight: bold;
			  display: block;
			  margin-bottom:5px;
			}
			.phpsaml_config_wrapper form p {
			  margin: 0;
			  padding: 5px;
			}
			
			.phpsaml_config_wrapper form input, .phpsaml_config_wrapper form textarea {
				display: block;
				width: 100%;
				-webkit-box-sizing: border-box;
				   -moz-box-sizing: border-box;
						box-sizing: border-box;
			}
			
			.phpsaml_config_wrapper form input {
				height:30px;
			}
			
			.phpsaml_config_wrapper form textarea {
				min-height:250px;
			}
			
			.phpsaml_config_wrapper form input:focus, .phpsaml_config_wrapper form textarea:focus, .phpsaml_config_wrapper form select:focus {
				border-color: rgba(82, 168, 236, 0.8);
				  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
				  -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
				  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
				  outline: 0;
				  outline: thin dotted \9;
			}

			.full-width {
			  grid-column: span 2;
			}
			
			
		</style>
		
		<div class="phpsaml_config_wrapper tab_cadre_fixe">
			<form name="form" method="post" action="<?php echo $CFG_GLPI["root_doc"]; ?>"/plugins/phpsaml/front/config.form.php" id="phpsaml_config">
				<input type="hidden" name="id" value="1">
				
				<h1 class="full-width"><?php echo __("PHP SAML Configuration", "phpsaml"); ?></h1>
				
				<h2 class="full-width"><?php echo __("General", "phpsaml"); ?></h2>
				<p class="full-width">
					<label for="enforced">
						<?php echo __("Plugin Enforced", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Toggle 'yes' to enforce Single Sign On for all sessions.  Toggle 'no' to allow visitors the option to login with Single Sign On from the login page.", "phpsaml"); ?>"></i>
					</label>
					<select name="enforced">
						<option value="1" <?php echo ((isset($config["enforced"]) && $config["enforced"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["enforced"]) && $config["enforced"] == 0) || !isset($config["enforced"]) ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				<p>
					<label for="strict">
						<?php echo __("Strict", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("If 'strict' is True, then the PHP Toolkit will reject unsigned or unencrypted messages if it expects them to be signed or encrypted. Also it will reject the messages if the SAML standard is not strictly followed: Destination, NameId, Conditions ... are validated too.", "phpsaml"); ?>"></i>
					</label>
					<select name="strict">
						<option value="1" <?php echo ((isset($config["strict"]) && $config["strict"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["strict"]) && $config["strict"] == 0) || !isset($config["strict"])  ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				<p>
					<label for="debug">
						<?php echo __("Debug", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Toggle yes to print errors.", "phpsaml"); ?>"></i>
					</label>
					<select name="debug">
						<option value="1" <?php echo ((isset($config["debug"]) && $config["debug"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["debug"]) && $config["debug"] == 0) || !isset($config["debug"]) ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				
				<h2 class="full-width"><?php echo __("Service Provider Configuration", "phpsaml"); ?></h2>
				
				<p class="full-width">
					<label for="saml_sp_certificate">
						<?php echo __("Service Provider Certificate", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Certificate GLPI should use when communicating with the Identity Provider.", "phpsaml"); ?>"></i>
					</label> 
					<?php
						echo (isset($messages["errors"]["saml_sp_certificate"]) ? "<br /><small style='color:red; width:300px'>".$messages["errors"]["saml_sp_certificate"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_sp_certificate"]) ? "<br /><small style='color:orange; width:300px'>".$messages["warnings"]["saml_sp_certificate"]."</small>" : "");
					?>
					<textarea name="saml_sp_certificate"><?php echo (isset($config["saml_sp_certificate"]) ? $config["saml_sp_certificate"] : ""); ?></textarea>
				</p>
				<p class="full-width">
					<label for="saml_sp_certificate_key">
						<?php echo __("Service Provider Certificate Key", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Certificate private key GLPI should use when communicating with the Identity Provider.", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_sp_certificate_key"]) ? "<br /><small style='color:red; width:300px'>".$messages["errors"]["saml_sp_certificate_key"]."</small>" : ""); 
						echo (isset($messages["warnings"]["saml_sp_certificate_key"]) ? "<br /><small style='color:orange; width:300px'>".$messages["warnings"]["saml_sp_certificate_key"]."</small>" : ""); 	
					?>
					<textarea name="saml_sp_certificate_key" rows=15 cols=75><?php echo (isset($config["saml_sp_certificate_key"]) ? $config["saml_sp_certificate_key"] : ""); ?></textarea>
				</p>
				
				<h2 class="full-width"><?php echo __("Identity Provider Configuration", "phpsaml"); ?></h2>
				
				<p class="full-width">
					<label for="saml_idp_entity_id">
						<?php echo __("Identity Provider Entity ID", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Identifier of the IdP entity  (must be a URI).", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_idp_entity_id"]) ? "<br /><small style='color:red; max-width:400px'>".$messages["errors"]["saml_idp_entity_id"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_entity_id"]) ? "<br /><small style='color:orange; max-width:400px'>".$messages["warnings"]["saml_idp_entity_id"]."</small>" : "");
					?>
					<input type="text" size="90" name="saml_idp_entity_id" value="<?php echo (isset($config["saml_idp_entity_id"]) ? $config["saml_idp_entity_id"] : ""); ?>">
				</p>
				<p class="full-width">
					<label for="saml_idp_single_sign_on_service">
						<?php echo __("Identity Provider Single Sign On Service URL", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("URL Target of the Identity Provider where GLPI will send the Authentication Request Message.", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_idp_single_sign_on_service"]) ? "<br /><small style='color:red; max-width:400px'>".$messages["errors"]["saml_idp_single_sign_on_service"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_single_sign_on_service"]) ? "<br /><small style='color:orange; max-width:400px'>".$messages["warnings"]["saml_idp_single_sign_on_service"]."</small>" : "");
					?>
					<input type="text" size="90" name="saml_idp_single_sign_on_service" value="<?php echo (isset($config["saml_idp_single_sign_on_service"]) ? $config["saml_idp_single_sign_on_service"] : ""); ?>">
				</p>
				<p class="full-width">
					<label for="saml_idp_single_logout_service">
						<?php echo __("Identity Provider Single Logout Service URL", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("URL Location of the Identity Provider where GLPI will send the Single Logout Request.", "phpsaml"); ?>"></i>
					</label>
					<?php 
						echo (isset($messages["errors"]["saml_idp_single_logout_service"]) ? "<br /><small style='color:red; max-width:400px'>".$messages["errors"]["saml_idp_single_logout_service"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_single_logout_service"]) ? "<br /><small style='color:orange; max-width:400px'>".$messages["warnings"]["saml_idp_single_logout_service"]."</small>" : "");
					?>
					<input type="text" size="90" name="saml_idp_single_logout_service" value="<?php echo (isset($config["saml_idp_single_logout_service"]) ? $config["saml_idp_single_logout_service"] : ""); ?>">
				</p>
				<p class="full-width">
					<label for="saml_idp_certificate">
						<?php echo __("Identity Provider Public X509 Certificate", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Public x509 certificate of the Identity Provider.", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_idp_certificate"]) ? "<br /><small style='color:red; max-width:400px'>".$messages["errors"]["saml_idp_certificate"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_certificate"]) ? "<br /><small style='color:orange; max-width:400px'>".$messages["warnings"]["saml_idp_certificate"]."</small>" : "");
					?>
					<textarea name="saml_idp_certificate" rows="15" cols="75"><?php echo (isset($config["saml_idp_certificate"]) ? $config["saml_idp_certificate"] : ""); ?></textarea>
				</p>
				<p class="full-width">
					<label for="saml_idp_nameid_format">
						<?php echo __("Name ID Format", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("The name id format that is sent to the iDP.", "phpsaml"); ?>"></i>
					</label>
					<?php 
						echo (isset($messages["errors"]["saml_idp_nameid_format"]) ? "<br /><small style='color:red; max-width:400px'>".$messages["errors"]["saml_idp_nameid_format"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_nameid_format"]) ? "<br /><small style='color:orange; max-width:400px'>".$messages["warnings"]["saml_idp_nameid_format"]."</small>" : "");
					?>
					<select name="saml_idp_nameid_format">
						<option value="unspecified" <?php echo (!isset($config["saml_idp_nameid_format"]) || $config["saml_idp_nameid_format"] == 'unspecified' ? "selected" : ""); ?>>Unspecified</option>
						<option value="emailAddress" <?php echo (isset($config["saml_idp_nameid_format"]) && $config["saml_idp_nameid_format"] == 'emailAddress' ? "selected" : ""); ?>>Email Address</option>
						<option value="transient" <?php echo (isset($config["saml_idp_nameid_format"]) && $config["saml_idp_nameid_format"] == 'transient' ? "selected" : ""); ?>>Transient</option>
						<option value="persistent" <?php echo (isset($config["saml_idp_nameid_format"]) && $config["saml_idp_nameid_format"] == 'persistent' ? "selected" : ""); ?>>Persistent</option>
					</select>
				</p>
				
				<h2 class="full-width"><?php echo __("Security", "phpsaml"); ?></h2>
				<p class="full-width">
					<label for="requested_authn_context">
						<?php echo __("Requested Authn Context", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Set to None and no AuthContext will be sent in the AuthnRequest, oth", "phpsaml"); ?>"></i>
					</label>
				
					<select id="keep-order" multiple="multiple">
						<option value='PasswordProtectedTransport' <?php echo self::inArraySelected('PasswordProtectedTransport', $config["requested_authn_context"], 'string' ); ?>>PasswordProtectedTransport</option>
						<option value='Password' <?php echo self::inArraySelected('Password', $config["requested_authn_context"], 'string' ); ?>>Password</option>
						<option value='X509' <?php echo self::inArraySelected('X509', $config["requested_authn_context"], 'string' ); ?>>X509</option>
					</select>	
					<input id="requested-authn-context" type="hidden" name="requested_authn_context" value="<?php echo (isset($config["requested_authn_context"])) ? $config["requested_authn_context"] : '' ?>" />
				</p>
				<p>
					<label for="requested_authn_context_comparison">
						<?php echo __("Requested Authn Comparison", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("How should the library compare the requested Authn Context?  The value defaults to 'Exact'.", "phpsaml"); ?>"></i>
					</label>
					<select name="requested_authn_context_comparison">
						<option value="exact" <?php echo (!isset($config["requested_authn_context_comparison"]) || (isset($config["requested_authn_context_comparison"]) && $config["requested_authn_context_comparison"] == 'exact') ? "selected" : ""); ?>>Exact</option>
						<option value="minimum" <?php echo ((isset($config["requested_authn_context_comparison"]) && $config["requested_authn_context_comparison"] == 'minimum') || !isset($config["requested_authn_context_comparison"]) ? "selected" : ""); ?>>Minimum</option>
						<option value="maximum" <?php echo ((isset($config["requested_authn_context_comparison"]) && $config["requested_authn_context_comparison"] == 'maximum') || !isset($config["requested_authn_context_comparison"]) ? "selected" : ""); ?>>Maximum</option>
						<option value="better" <?php echo ((isset($config["requested_authn_context_comparison"]) && $config["requested_authn_context_comparison"] == 'better') || !isset($config["requested_authn_context_comparison"]) ? "selected" : ""); ?>>Better</option>
					</select>	
				</p>
				
				<p class="full-width">
					<input type="submit" name="update" value="<?php echo __("Update", "phpsaml"); ?>" class="submit" >
				</p>
			<?php
			Html::closeForm();
			?>
		</div>
				
		<?php
		unset($_SESSION["phpsaml_messages"]);
	}
   
	function inArraySelected($string, $array=array(), $type='string' ){
		if ($type == 'string'){
			$array = explode(',', $array);
		}
		if(isset($array) && !empty($array)){
			if(in_array($string, $array)){
				return "Selected";
			}
		}
	}
   
	function validate($post){
		$messages = array();
		if (empty($post["saml_sp_certificate"])){
			$messages["warnings"]["saml_sp_certificate"] = "You should provide a certificate as best practice.";
		}
		
		if (empty($post["saml_sp_certificate_key"])){
			$messages["warnings"]["saml_sp_certificate_key"] = "You should provide a certificate key as best practice.";
		}
		
		if (empty($post["saml_idp_entity_id"])){
			$messages["errors"]["saml_idp_entity_id"] = "Field cannot be empty";
		}
		
		if (empty($post["saml_idp_single_sign_on_service"])){
			$messages["errors"]["saml_idp_single_sign_on_service"] = "Field cannot be empty";
		}
		
		if (empty($post["saml_idp_single_logout_service"])){
			
		}
		
		if (empty($post["saml_idp_certificate"])){
			$messages["errors"]["saml_idp_certificate"] = "Field cannot be empty";
		}
		
		return $messages;
	}
	
	
	


	function getConfig() {
		$phpsamlconf = new PluginPhpsamlConfig();
		if ($phpsamlconf->getFromDB(1)) {
			$config = array(
				"enforced"									=> $phpsamlconf->fields["enforced"],
				"strict"									=> $phpsamlconf->fields["strict"],
				"debug"										=> $phpsamlconf->fields["debug"],
				"saml_sp_certificate" 						=> $phpsamlconf->fields["saml_sp_certificate"],
				"saml_sp_certificate_key" 					=> $phpsamlconf->fields["saml_sp_certificate_key"],
				"saml_idp_entity_id" 						=> $phpsamlconf->fields["saml_idp_entity_id"],
				"saml_idp_single_sign_on_service" 			=> $phpsamlconf->fields["saml_idp_single_sign_on_service"],
				"saml_idp_single_logout_service" 			=> $phpsamlconf->fields["saml_idp_single_logout_service"],
				"saml_idp_certificate" 						=> $phpsamlconf->fields["saml_idp_certificate"],
				"saml_idp_nameid_format" 					=> $phpsamlconf->fields["saml_idp_nameid_format"],
				"requested_authn_context" 					=> $phpsamlconf->fields["requested_authn_context"],
				"requested_authn_context_comparison" 		=> $phpsamlconf->fields["requested_authn_context_comparison"]
			);
		} else {
			$config = array();
		}
		return $config;
	}
}
