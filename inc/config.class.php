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

   @package   phpsamlconfig
   @author    Chris Gralike
   @co-author
   @copyright Copyright (c) 2018 by Derrick Smith
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @since     2018

   @changelog rewrite and restructure removing context switches and improving readability and maintainability
   @changelog breaking config up into methods for maintainability and unit testing purposes.

   ------------------------------------------------------------------------
 */

// Header guard
if (!defined("GLPI_ROOT")) {
    die("Sorry. You can't access directly to this file");
}

class PluginPhpsamlConfig extends CommonDBTM
{
    /**
     * defines the rights a user must posses to be able to access this menu option in the rules section
     * @var string
     **/
	public static   $rightname = "plugin_phpsaml_config";


    /**
     * Defines where the setup HTML template is located
     * @var string
     **/
    private $tpl = '../tpl/configForm.html';


    /**
     * Stores a copy of the HTML template in memory for processing
     * @var string
     **/
    private $htmlForm = null;


    /**
     * Stores a copy of the form values to be injected into the final HTML form
     * @var array
     **/
    private $formValues = [];


    /**
     * The amount of fields we expect from the database
     * Change value for unit testing
     * @var int
     **/
    private $expectedItems = 18;


    /**
     * Expected version
     * Change value for unit testing
     * @var string
     **/
    private $expectedVersion = '1.2.1';

    /**
     * Stores a copy of the phpSaml Database Configuration
     * @var array
     **/
    private $config = [];


    /**
     * Show the form and handle potential inputs.
     * @param void
     * @return boolean
     */
    public function showForm($ID, array $options = [])
    {
        // Populate current configuration
        $this->config = $this->getConfig($ID);

        // process the configuration items
        // using the database array.
        if (is_array($this->config)) {
            foreach ($this->config as $method => $current) {
                if (method_exists($this, $method)) {
                    // Handle property
                    $this->$method($current);
                } else {
                    // TODO: Make this a nice error in HTML template.
                    if ($method != 'valid') {
                        echo "Warning: No property handled found for $method in ".__class__;
                    }
                }
            }
        } else {
            // TODO: Make this a nice error in HTML template.
            echo "Error: could not populate PhpSaml database configuration<br>";
        }
        echo "<pre>";
        var_dump($this->formValues);

        // Generate and show form
        $this->generateForm();

        echo "done!";
    }


    /**
     * Get the current configuration from the database or present a default value.
     * @param int $id
     * @return array $config
     */
    public function getConfig(int $id = 1)
	{
        global $DB;

		$sql = 'SHOW COLUMNS FROM '.$this->getTable();
		if ($result = $DB->query($sql)) {
            if ($this->getFromDB($id)) {
                while ($data = $result->fetch_assoc()) {
                    $config[$data['Field']] = $this->fields[$data['Field']];
                }
            } else {
                echo "Error: could not retrieve configuration data from database";
            }
        } else {
            echo "Error: could not retrieve column data from database";
        }

        if (count($config) <= $this->expectedItems) {
            echo "Warning: Phpsaml did not receive the expected ammount of configuration items from the database!";
            echo debug_backtrace()[1]['function'];
            $config['valid'] = false;
            return $config;
        } else {
            $config['valid'] = true;
            return $config;
        }
	}


    /**
     * Handle the enforced default value and changes.
     *
     * @param int $dbConf
     * @return boolean
     */
    private function enforced(int $dbConf)
    {
        // Do lable translations
        $formValues = [
            'ENFORCED_LABEL' =>  __("Plugin Enforced", "phpsaml"),
            'ENFORCED_TITLE' =>  __("Toggle 'yes' to enforce Single Sign On for all login sessions", "phpsaml"),
            'ENFORCED_SELECT'=> '',
            'ENFORCED_ERROR' => false
        ];

        // Generate select options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['ENFORCED_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);

    }


    /**
     * Handle the strict default value and changes
     *
     * @param int $dbConf
     * @return boolean
     */
    private function strict(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            'STRICT_LABEL' =>  __("Strict", "phpsaml"),
            'STRICT_TITLE' =>  __("If 'strict' is True, then PhpSaml will reject unencrypted messages", "phpsaml"),
            'STRICT_SELECT'=> '',
            'STRICT_ERROR' => false
        ];

        // Generate select options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['STRICT_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     * Handle the debug default value and changes
     *
     * @param int $dbConf
     * @return boolean
     */
    private function debug(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            'DEBUG_LABEL' =>  __("Debug", "phpsaml"),
            'DEBUG_TITLE' =>  __("Toggle yes to print errors", "phpsaml"),
            'DEBUG_SELECT'=> '',
            'DEBUG_ERROR' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['DEBUG_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     * Handle the jit default value and changes
     *
     * @param int $dbConf
     * @return boolean
     */
    private function jit(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            'JIT_LABEL' =>  __("Strict", "phpsaml"),
            'JIT_TITLE' =>  __("If 'strict' is True, then PhpSaml will reject unencrypted messages", "phpsaml"),
            'JIT_SELECT'=> '',
            'JIT_ERROR' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['JIT_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     * Handle the saml sp certificate default value and changes
     *
     * @param string $dbConf
     * @return boolean
     */
    private function saml_sp_certificate(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            'SP_CERT_LABEL' =>  __("Service Provider Certificate", "phpsaml"),
            'SP_CERT_TITLE' =>  __("Certificate we should use when communicating with the Identity Provider.", "phpsaml"),
            'SP_CERT_VALUE' => $dbConf,
            'SP_CERT_ERROR' => false
        ];

        if (!strstr($dbConf, '-BEGIN CERTIFICATAE-') && !strstr($dbConf, '-END CERTIFICATAE-')) {
            echo "Warning: Value does not look like a valid certificate, include the certificate BEGIN and END tags";
        }
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }

    /**
     * Handle the saml sp certificate key default value and changes
     *
     * @param string $dbConf
     * @return boolean
     */
    private function saml_sp_certificate_key(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            'SP_KEY_LABEL' =>  __("Service Provider Certificate Key", "phpsaml"),
            'SP_KEY_TITLE' =>  __("Certificate private key we should use when communicating with the Identity Provider", "phpsaml"),
            'SP_KEY_VALUE' => $dbConf,
            'SP_KEY_ERROR' => false
        ];

        if (!strstr($dbConf, '-BEGIN CERTIFICATAE-') &&
            !strstr($dbConf, '-END CERTIFICATAE-')) {
            echo "Warning: Value does not look like a valid certificate, include the certificate BEGIN and END tags";
        }
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     * Handle the Saml SP NameId formating default value and changes
     *
     * @param string $dbConf
     * @return boolean
     */
    private function saml_sp_nameid_format(string $dbConf)
    {
         // Declare template labels
         $formValues = [
            'SP_ID_LABEL' =>  __("Name ID Format", "phpsaml"),
            'SP_ID_TITLE' =>  __("The name id format that is sent to the iDP.", "phpsaml"),
            'SP_ID_SELECT' => '',
            'SP_ID_ERROR' => false
        ];

        // Generate the options array
        $options = ['unspecified'  => __('Unspecified', 'phpsaml'),
                    'emailAddress' => __('Email Address', 'phpsaml'),
                    'transient'    => __('Transient', 'phpsaml'),
                    'persistent'   => __('Persistent', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['SP_ID_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     * Control of the configuration
     *
     * @param string $dbConf
     * @return boolean
     */
    private function saml_idp_entity_id(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            'IP_ID_LABEL' =>  __("Identity Provider Entity ID", "phpsaml"),
            'IP_ID_TITLE' =>  __("Identifier of the IdP entity  (must be a URI).", "phpsaml"),
            'IP_ID_VALUE' => $dbConf,
            'IP_ID_ERROR' => false
        ];

        //Validate URL?
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     * Description
     *
     * @param string $dbConf
     * @return boolean
     */
    private function saml_idp_single_sign_on_service(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            'IP_SSO_URL_LABEL' =>  __("Identity Provider Single Sign On Service URL", "phpsaml"),
            'IP_SSO_URL_TITLE' =>  __("URL Target of the Identity Provider where we will send the Authentication Request Message.", "phpsaml"),
            'IP_SSO_URL_VALUE' => $dbConf,
            'IP_SSO_URL_ERROR' => false
        ];

        //Validate URL?
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }

    /**
     * Description
     *
     * @param string $dbConf
     * @return boolean
     */
    private function saml_idp_single_logout_service(string $dbConf)
    {
         // Declare template labels
         $formValues = [
            'IP_SLS_URL_LABEL' =>  __("Identity Provider Single Logout Service URL", "phpsaml"),
            'IP_SLS_URL_TITLE' =>  __("URL Location of the Identity Provider where GLPI will send the Single Logout Request.", "phpsaml"),
            'IP_SLS_URL_VALUE' => $dbConf,
            'IP_SLS_URL_ERROR' => false
        ];

        //Validate URL?
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }

    /**
     * Description
     *
     * @param string $dbConf
     * @return boolean
     */
    private function saml_idp_certificate(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            'IP_CERT_LABEL' =>  __("Identity Provider Public X509 Certificate", "phpsaml"),
            'IP_CERT_TITLE' =>  __("Public x509 certificate of the Identity Provider.", "phpsaml"),
            'IP_CERT_VALUE' => $dbConf,
            'IP_CERT_ERROR' => false
        ];

        if (!strstr($dbConf, '-BEGIN CERTIFICATAE-') &&
            !strstr($dbConf, '-END CERTIFICATAE-')) {
            echo "Warning: Value does not look like a valid certificate, include the certificate BEGIN and END tags";
        }
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }

    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function requested_authn_context()
    {

    }

    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function requested_authn_context_comparison()
    {

    }

    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function saml_security_nameidencrypted()
    {

    }

    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function saml_security_authnrequestssigned()
    {

    }

    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function saml_security_logoutrequestsigned()
    {

    }

    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function saml_security_logoutresponsesigned()
    {

    }


    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function id()
    {
        // For future use
    }


    /**
     * Control of the configuration
     *
     * @param void
     * @return boolean
     */
    private function version(string $current)
    {
        if($current != $this->expectedVersion){
            echo "Warning: Version mismatch detected,
                  database reported $current, we expected {$this->expectedVersion} ";
        }
        // For future use
        // Validate actual PHP Saml version else generate notification;
    }




    private function generateForm()
    {
        // Read the template file containing the HTML template;
       
        if (file_exists($this->tpl)) {
            $this->htmlForm = file_get_contents($this->tpl);
        }
    }
}
