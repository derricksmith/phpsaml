<?php

$confFormArray = [
    'form' => [
        'name'      =>  'form',
        'method'    =>  'post',
        'action'    =>  $CFG_GLPI["root_doc"].'/plugins/phpsaml/front/config.form.php',
        'id'        =>  'phpsaml_config',
        'classes'   =>  '',
        'fields'    =>  [

            'id'        =>  ['type'             => 'hidden',
                             'options'          => null,
                             'friendlyName'     => '',
                             'headerText'       => null,
                             'footerText'       => null,
                             'label'            => null,
                             'labelInfo'        => null,
                             'errorKey'         => null,
                             'values'           => null],
                            
            'enforced'  =>  ['type'             => 'select',
                             'options'          => ['Yes' => 1, 'No' => 0],
                             'friendlyName'     => 'Enforced',
                             'headerText'       => 'PHP SAML Configuration',
                             'footerText'       => null,
                             'label'            => 'Plugin Enforced',
                             'labelInfo'        => 'Toggle \'yes\' to enforce Single Sign On for all sessions.  Toggle \'no\' to allow visitors the option 
                                                    to login with Single Sign On from the login page.',
                             'errorKey'         => null,
                             'values'           => null],

            'strict'    =>  ['type'             => 'select',
                             'options'          => ['Yes' => 1, 'No' => 0],
                             'friendlyName'     => 'Strict',
                             'headerText'       => null,
                             'footerText'       => null,
                             'label'            => 'Plugin Enforced',
                             'labelInfo'        => 'If \'strict\' is True, then the PHP Toolkit will reject unsigned or unencrypted messages if it expects them 
                                                    to be signed or encrypted. Also it will reject the messages if the SAML standard is not strictly followed: 
                                                    Destination, NameId, Conditions ... are validated too.',
                             'errorKey'         => null,
                             'values'           => null],
			'debug'     =>  ['type'             => 'select',
                             'options'          => ['Yes' => 1, 'No' => 0],
                             'friendlyName'     => 'Debug',
                             'headerText'       => null,
                             'footerText'       => null,
                             'label'            => 'Plugin Enforced',
                             'labelInfo'        => 'Toggle yes to print errors.',
                             'errorKey'         => null,
                             'values'           => null],
			'jit'     =>  ['type'             => 'select',
                             'options'          => ['Yes' => 1, 'No' => 0],
                             'friendlyName'     => 'Debug',
                             'headerText'       => null,
                             'footerText'       => null,
                             'label'            => 'Plugin Enforced',
                             'labelInfo'        => 'Toggle yes to print errors.',
                             'errorKey'         => null,
                             'values'           => null],
        ],
    ], 
];


/*

				<p class="full-width">
					<label for="jit">
						<?php echo __("Just In Time (JIT) Provisioning", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Toggle 'yes' to create new users if they do not already exist.  Toggle 'no' will cause an error if the user does not already exist in GLPI.", "phpsaml"); ?>"></i>
					</label>
					<select name="jit">
						<option value="1" <?php echo ((isset($config["jit"]) && $config["jit"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["jit"]) && $config["jit"] == 0) || !isset($config["jit"]) ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				
				<h2 class="full-width"><?php echo __("Service Provider Configuration", "phpsaml"); ?></h2>
				
				<p class="full-width">
					<label for="saml_sp_certificate">
						<?php echo __("Service Provider Certificate", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Certificate GLPI should use when communicating with the Identity Provider.", "phpsaml"); ?>"></i>
					</label> 
					<?php
						echo (isset($messages["errors"]["saml_sp_certificate"]) ? "<br /><small style='color:red; width:300px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_sp_certificate"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_sp_certificate"]) ? "<br /><small style='color:orange; width:300px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_sp_certificate"]."</small>" : "");
					?>
					<textarea name="saml_sp_certificate"><?php echo (isset($config["saml_sp_certificate"]) ? $config["saml_sp_certificate"] : ""); ?></textarea>
				</p>
				<p class="full-width">
					<label for="saml_sp_certificate_key">
						<?php echo __("Service Provider Certificate Key", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Certificate private key GLPI should use when communicating with the Identity Provider.", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_sp_certificate_key"]) ? "<br /><small style='color:red; width:300px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_sp_certificate_key"]."</small>" : ""); 
						echo (isset($messages["warnings"]["saml_sp_certificate_key"]) ? "<br /><small style='color:orange; width:300px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_sp_certificate_key"]."</small>" : ""); 	
					?>
					<textarea name="saml_sp_certificate_key" rows=15 cols=75><?php echo (isset($config["saml_sp_certificate_key"]) ? $config["saml_sp_certificate_key"] : ""); ?></textarea>
				</p>
				<p class="full-width">
					<label for="saml_idp_nameid_format">
						<?php echo __("Name ID Format", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("The name id format that is sent to the iDP.", "phpsaml"); ?>"></i>
					</label>
					<?php 
						echo (isset($messages["errors"]["saml_sp_nameid_format"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_sp_nameid_format"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_sp_nameid_format"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_sp_nameid_format"]."</small>" : "");
					?>
					<select name="saml_sp_nameid_format">
						<option value="unspecified" <?php echo (!isset($config["saml_sp_nameid_format"]) || $config["saml_sp_nameid_format"] == 'unspecified' ? "selected" : ""); ?>>Unspecified</option>
						<option value="emailAddress" <?php echo (isset($config["saml_sp_nameid_format"]) && $config["saml_sp_nameid_format"] == 'emailAddress' ? "selected" : ""); ?>>Email Address</option>
						<option value="transient" <?php echo (isset($config["saml_sp_nameid_format"]) && $config["saml_sp_nameid_format"] == 'transient' ? "selected" : ""); ?>>Transient</option>
						<option value="persistent" <?php echo (isset($config["saml_sp_nameid_format"]) && $config["saml_sp_nameid_format"] == 'persistent' ? "selected" : ""); ?>>Persistent</option>
					</select>
				</p>
				
				<h2 class="full-width"><?php echo __("Identity Provider Configuration", "phpsaml"); ?></h2>
				
				<p class="full-width">
					<label for="saml_idp_entity_id">
						<?php echo __("Identity Provider Entity ID", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Identifier of the IdP entity  (must be a URI).", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_idp_entity_id"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_idp_entity_id"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_entity_id"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_idp_entity_id"]."</small>" : "");
					?>
					<input type="text" size="90" name="saml_idp_entity_id" value="<?php echo (isset($config["saml_idp_entity_id"]) ? $config["saml_idp_entity_id"] : ""); ?>">
				</p>
				<p class="full-width">
					<label for="saml_idp_single_sign_on_service">
						<?php echo __("Identity Provider Single Sign On Service URL", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("URL Target of the Identity Provider where GLPI will send the Authentication Request Message.", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_idp_single_sign_on_service"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_idp_single_sign_on_service"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_single_sign_on_service"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_idp_single_sign_on_service"]."</small>" : "");
					?>
					<input type="text" size="90" name="saml_idp_single_sign_on_service" value="<?php echo (isset($config["saml_idp_single_sign_on_service"]) ? $config["saml_idp_single_sign_on_service"] : ""); ?>">
				</p>
				<p class="full-width">
					<label for="saml_idp_single_logout_service">
						<?php echo __("Identity Provider Single Logout Service URL", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("URL Location of the Identity Provider where GLPI will send the Single Logout Request.", "phpsaml"); ?>"></i>
					</label>
					<?php 
						echo (isset($messages["errors"]["saml_idp_single_logout_service"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_idp_single_logout_service"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_single_logout_service"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_idp_single_logout_service"]."</small>" : "");
					?>
					<input type="text" size="90" name="saml_idp_single_logout_service" value="<?php echo (isset($config["saml_idp_single_logout_service"]) ? $config["saml_idp_single_logout_service"] : ""); ?>">
				</p>
				<p class="full-width">
					<label for="saml_idp_certificate">
						<?php echo __("Identity Provider Public X509 Certificate", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Public x509 certificate of the Identity Provider.", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_idp_certificate"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_idp_certificate"]."</small>" : "");
						echo (isset($messages["warnings"]["saml_idp_certificate"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_idp_certificate"]."</small>" : "");
					?>
					<textarea name="saml_idp_certificate" rows="15" cols="75"><?php echo (isset($config["saml_idp_certificate"]) ? $config["saml_idp_certificate"] : ""); ?></textarea>
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
				<p class="full-width">
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
				<p>
					<label for="saml_security_nameidencrypted">
						<?php echo __("Encrypt NameID", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Toggle yes to encrypt NameID.  Requires service provider certificate and key", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_security_nameidencrypted"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_security_nameidencrypted"]."<br /></small>" : "");
						echo (isset($messages["warnings"]["saml_security_nameidencrypted"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_security_nameidencrypted"]."<br /></small>" : "");
					?>
					<select name="saml_security_nameidencrypted">
						<option value="1" <?php echo ((isset($config["saml_security_nameidencrypted"]) && $config["saml_security_nameidencrypted"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["saml_security_nameidencrypted"]) && $config["saml_security_nameidencrypted"] == 0) || !isset($config["saml_security_nameidencrypted"]) ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				<p>
					<label for="saml_security_authnrequestssigned">
						<?php echo __("Sign Authn Requests", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Toggle yes to sign Authn Requests.  Requires service provider certificate and key", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_security_authnrequestssigned"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_security_authnrequestssigned"]."<br /></small>" : "");
						echo (isset($messages["warnings"]["saml_security_authnrequestssigned"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_security_authnrequestssigned"]."<br /></small>" : "");
					?>
					<select name="saml_security_authnrequestssigned">
						<option value="1" <?php echo ((isset($config["saml_security_authnrequestssigned"]) && $config["saml_security_authnrequestssigned"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["saml_security_authnrequestssigned"]) && $config["saml_security_authnrequestssigned"] == 0) || !isset($config["saml_security_authnrequestssigned"]) ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				<p>
					<label for="saml_security_logoutrequestsigned">
						<?php echo __("Sign Logout Requests", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Toggle yes to sign Logout Requests.  Requires service provider certificate and key", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_security_logoutrequestsigned"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_security_logoutrequestsigned"]."<br /></small>" : "");
						echo (isset($messages["warnings"]["saml_security_logoutrequestsigned"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_security_logoutrequestsigned"]."<br /></small>" : "");
					?>
					<select name="saml_security_logoutrequestsigned">
						<option value="1" <?php echo ((isset($config["saml_security_logoutrequestsigned"]) && $config["saml_security_logoutrequestsigned"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["saml_security_logoutrequestsigned"]) && $config["saml_security_logoutrequestsigned"] == 0) || !isset($config["saml_security_logoutrequestsigned"]) ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				<p>
					<label for="saml_security_logoutresponsesigned">
						<?php echo __("Sign Logout Response", "phpsaml"); ?>
						<i class="pointer fa fa-info" title="<?php echo __("Toggle yes to sign Logout Response.  Requires service provider certificate and key", "phpsaml"); ?>"></i>
					</label>
					<?php
						echo (isset($messages["errors"]["saml_security_logoutresponsesigned"]) ? "<br /><small style='color:red; max-width:400px'>".__('Error: ', 'phpsaml').$messages["errors"]["saml_security_logoutresponsesigned"]."<br /></small>" : "");
						echo (isset($messages["warnings"]["saml_security_logoutresponsesigned"]) ? "<br /><small style='color:orange; max-width:400px'>".__('Warning: ', 'phpsaml').$messages["warnings"]["saml_security_logoutresponsesigned"]."<br /></small>" : "");
					?>
					<select name="saml_security_logoutresponsesigned">
						<option value="1" <?php echo ((isset($config["saml_security_logoutresponsesigned"]) && $config["saml_security_logoutresponsesigned"] == 1) ? "selected" : ""); ?>>Yes</option>
						<option value="0" <?php echo ((isset($config["saml_security_logoutresponsesigned"]) && $config["saml_security_logoutresponsesigned"] == 0) || !isset($config["saml_security_logoutresponsesigned"]) ? "selected" : ""); ?>>No</option>
					</select>
				</p>
				
				<p class="full-width">
					<input type="submit" name="update" value="<?php echo __("Update", "phpsaml"); ?>" class="submit" >
				</p>
			<?php
			Html::closeForm();
			?>
		</div>
*/