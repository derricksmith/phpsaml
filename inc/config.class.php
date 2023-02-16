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
	public static $rightname = "plugin_phpsaml_config";


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
    private $expectedItems = 20;


    /**
     * Where to get the current version
     * Change value for unit testing
     * @var string
     **/
    private $PhpsamlGitAtomUrl = 'https://github.com/derricksmith/phpsaml/releases.atom';

    /**
     * Stores a copy of the phpSaml Database Configuration
     * @var array
     **/
    private $config = [];


    /**
     * Registers if a fatal error occured during execution;
     * @var array
     **/
    private $fatalError = false;


    /**
     * Registers errors that occured in a friendly format;
     * @var array
     **/
    private $errorMsgs = null;


    /**
     * 
     * Generate the configuration htmlForm and return it.
     * 
     * @param void
     * @return boolean
     */
    public function showForm($id, array $options = [])
    {
        // Populate current configuration
        if ($this->config = $this->getConfig($id)) {
            // Call the form field handlers
            // using the database array.
            if (is_array($this->config)) {
                foreach ($this->config as $method => $current) {
                    if (method_exists($this, $method)) {
                        // Handle property
                        $this->$method($current);
                    } else {
                        if ($method != 'valid') {
                            $this->registerError("Warning: No handler found for configuration item: $method in ".__class__." db corrupted?");
                        }
                    }
                }
            } else {
                $this->registerError("Error: db config did not return required config array!", true);
            }

            // Generate and show form
            return $this->generateForm(true);
        }
    }




    /**
     *
     * Gets the current configuration from the database. It will first query the columns of the configuration
     * table. It will then use these columns to fetch all the related database configuration values and place them
     * in a structured array. Finally this structured array is returned. The caller should evaluate the 'valid' array
     * key to validate the configuration array is usable.
     *
     * @param string $id        // Change to int, it is still called using strings.
     * @return array $config
     */
    public function getConfig(string $id = '1')
	{
        global $DB;
        $config['valid'] = true;

		$sql = 'SHOW COLUMNS FROM '.$this->getTable();
		if ($result = $DB->query($sql)) {
            if ($this->getFromDB($id)) {
                while ($data = $result->fetch_assoc()) {
                    $config[$data['Field']] =  $this->fields[$data['Field']];
                }
            } else {
                $this->registerError('Phpsaml could not retrieve configuration values from database.', true);
                $config['valid'] = false;
            }
        } else {
            $this->registerError('Phpsaml was not able to retrieve configuration columns from database', true);
            $config['valid'] = false;
        }
        // Test if config exists;
        if (count($config) <> $this->expectedItems) {
            $this->registerError('Phpsaml expected '.$this->expectedItems.' configuration items but got '.count($config).' items instead');
            $config['valid'] = false;
        }

        return $config;
	}




    /**
     *
     * The generateForm method is called by the showForm method. It should only be called after all
     * configuration handlers are executed. It will populate all generic form properties, load the
     * configuration template file and replace all template placeholders with the populated fields.
     * It will disable all form fields if a fatal error was reported using the fatalError class property.
     * Finally it will echo the generated htmlForm.
     *
     * @param bool $return   // return the generated htmlform as string
     * @return string|bool
     */
    private function generateForm(bool $return=false)
    {
        global $CFG_GLPI;

        // Read the template file containing the HTML template;
        if (file_exists($this->tpl)) {
            $this->htmlForm = file_get_contents($this->tpl);
        }

        // Declare general form values
        $formValues = [
            '[[SCRIPT_A]]'               => __('Available', 'phpsaml'),
            '[[SCRIPT_B]]'               => __('Selected', 'phpsaml'),
            '[[GLPI_ROOTDOC]]'           => $CFG_GLPI["root_doc"],
            '[[TITLE]]'                  => __("PHP SAML Configuration", "phpsaml"),
            '[[HEADER_GENERAL]]'         => __("General", "phpsaml"),
            '[[HEADER_PROVIDER]]'        => __("Service Provider Configuration", "phpsaml"),
            '[[HEADER_PROVIDER_CONFIG]]' => __("Identity Provider Configuration", "phpsaml"),
            '[[HEADER_SECURITY]]'        => __("Security", "phpsaml"),
            '[[SUBMIT]]'                 => __("Update", "phpsaml"),
            '[[CLOSE_FORM]]'             => Html::closeForm()
        ];
        // Merge the values in the central array.
        $this->formValues = array_merge($this->formValues, $formValues);

        // Process the errors if any
        if (is_array($this->errorMsgs)) {
            // If a fatal occured the form is not dependable anymore so disable all elements;
            $this->htmlForm = ($this->fatalError) ? str_replace('[[DISABLED]]','DISABLED',$this->htmlForm) : str_replace('[[DISABLED]]','',$this->htmlForm);

            // Process the error messages;
            $nice = '';
            foreach ($this->errorMsgs as $k => $errmsg) {
                $nice .= $errmsg.'<br>';
            }

            $this->formValues['[[ERRORS]]'] = '<p class="full-width errors">'.$nice.'</p>';
        } else {
            $this->formValues['[[ERRORS]]'] = '';
        }

        // Insert values into the form.
        if ($html = str_replace(array_keys($this->formValues), array_values($this->formValues), $this->htmlForm)) {
            return $html;
        } else {
            return false;
        }
    }


    public function processChanges($post)
    {
        echo "<pre> Dit is al gelukt, maar nu...";
        print_r($post);
    }


    /**
     *
     * Adds an error message to the errorMsgs class property. The errorMsgs class property is iterated by the generateForm
     * method to inject all errors into the top header of the htmlForm. If fatal is set to true the generateForm method will
     * disable all form elements and make sure an insert/update can no longer be performed.
     *
     * @param string $errorMsg
     * @param bool $fatal
     * @return void
     */
    private function registerError(string $errorMsg, bool $fatal=false)
    {
        if($fatal){
            $this->fatalError = true;
        }

        $this->errorMsgs[] = $errorMsg;
    }




// #######################
// Configuration handlers
// #######################

    /**
     *
     * Evaluates the enforced property
     * and populates the form template
     *
     * @param int $dbConf
     * @return boolean
     */
    private function enforced(int $dbConf)
    {
        // Do lable translations
        $formValues = [
            '[[ENFORCED_LABEL]]' =>  __("Plugin Enforced", "phpsaml"),
            '[[ENFORCED_TITLE]]' =>  __("Toggle 'yes' to enforce Single Sign On for all login sessions", "phpsaml"),
            '[[ENFORCED_SELECT]]'=> '',
            '[[ENFORCED_ERROR]]' => ''
        ];

        // Generate select options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[ENFORCED_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);

    }




     /**
     *
     * Evaluates the strict property
     * and populates the form template
     *
     * @param int $dbConf
     * @return void
     */
    private function strict(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[STRICT_LABEL]]' =>  __("Strict", "phpsaml"),
            '[[STRICT_TITLE]]' =>  __("If 'strict' is True, then PhpSaml will reject unencrypted messages", "phpsaml"),
            '[[STRICT_SELECT]]'=> '',
            '[[STRICT_ERROR]]' => false
        ];

        // Generate select options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[STRICT_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the debug property
     * and populates the form template
     *
     * @param int $dbConf
     * @return void
     */
    private function debug(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[DEBUG_LABEL]]' =>  __("Debug", "phpsaml"),
            '[[DEBUG_TITLE]]' =>  __("Toggle yes to print errors", "phpsaml"),
            '[[DEBUG_SELECT]]'=> '',
            '[[DEBUG_ERROR]]' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[DEBUG_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the sp certificate property
     * and populates the form template
     *
     * @param int $dbConf
     * @return boolean
     */
    private function jit(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[JIT_LABEL]]' =>  __("Just In Time (JIT) Provisioning", "phpsaml"),
            '[[JIT_TITLE]]' =>  __("Toggle 'yes' to create new users if they do not already exist.  Toggle 'no' will cause an error if the user does not already exist in GLPI.", "phpsaml"),
            '[[JIT_SELECT]]'=> '',
            '[[JIT_ERROR]]' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[JIT_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the sp certificate property
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */

    private function saml_sp_certificate(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[SP_CERT_LABEL]]' =>  __("Service Provider Certificate", "phpsaml"),
            '[[SP_CERT_TITLE]]' =>  __("Certificate we should use when communicating with the Identity Provider.", "phpsaml"),
            '[[SP_CERT_VALUE]]' => $dbConf,
            '[[SP_CERT_ERROR]]' => ''
        ];

        if (!strstr($dbConf, '-BEGIN CERTIFICATE-') || !strstr($dbConf, '-END CERTIFICATE-')) {
            $formValues['[[SP_CERT_ERROR]]'] = __("This does not look like a valid certificate, include the certificate BEGIN and END tags", "phpsaml");
        }
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the sp certificate key property
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */
    private function saml_sp_certificate_key(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[SP_KEY_LABEL]]' =>  __("Service Provider Certificate Key", "phpsaml"),
            '[[SP_KEY_TITLE]]' =>  __("Certificate private key we should use when communicating with the Identity Provider", "phpsaml"),
            '[[SP_KEY_VALUE]]' => $dbConf,
            '[[SP_KEY_ERROR]]' => false
        ];

        // Do some basic validations
        if (!strstr($dbConf, '-BEGIN PRIVATE KEY-') || !strstr($dbConf, '-END PRIVATE KEY-')) {
            $formValues['[[SP_KEY_ERROR]]'] =  __("This does not look like a valid private key, please make sure to include the private key BEGIN and END tags", "phpsaml");
        }
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the saml sp nameid format property
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */
    private function saml_sp_nameid_format(string $dbConf)
    {
         // Declare template labels
         $formValues = [
            '[[SP_ID_LABEL]]' =>  __("Name ID Format", "phpsaml"),
            '[[SP_ID_TITLE]]' =>  __("The name id format that is sent to the iDP.", "phpsaml"),
            '[[SP_ID_SELECT]]' => '',
            '[[SP_ID_ERROR]]' => false
        ];

        // Generate the options array
        $options = ['unspecified'  => __('Unspecified', 'phpsaml'),
                    'emailAddress' => __('Email Address', 'phpsaml'),
                    'transient'    => __('Transient', 'phpsaml'),
                    'persistent'   => __('Persistent', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[SP_ID_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the idp entity id property
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */
    private function saml_idp_entity_id(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[IP_ID_LABEL]]' =>  __("Identity Provider Entity ID", "phpsaml"),
            '[[IP_ID_TITLE]]' =>  __("Identifier of the IdP entity  (must be a URI).", "phpsaml"),
            '[[IP_ID_VALUE]]' => $dbConf,
            '[[IP_ID_ERROR]]' => false
        ];

        //Validate URL?
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the idp single sign on service property
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */
    private function saml_idp_single_sign_on_service(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[IP_SSO_URL_LABEL]]' =>  __("Identity Provider Single Sign On Service URL", "phpsaml"),
            '[[IP_SSO_URL_TITLE]]' =>  __("URL Target of the Identity Provider where we will send the Authentication Request Message.", "phpsaml"),
            '[[IP_SSO_URL_VALUE]]' => $dbConf,
            '[[IP_SSO_URL_ERROR]]' => false
        ];

        //Validate URL?
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the idp single logout service property for changes
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */
    private function saml_idp_single_logout_service(string $dbConf)
    {
         // Declare template labels
         $formValues = [
            '[[IP_SLS_URL_LABEL]]' =>  __("Identity Provider Single Logout Service URL", "phpsaml"),
            '[[IP_SLS_URL_TITLE]]' =>  __("URL Location of the Identity Provider where GLPI will send the Single Logout Request.", "phpsaml"),
            '[[IP_SLS_URL_VALUE]]' => $dbConf,
            '[[IP_SLS_URL_ERROR]]' => false
        ];

        //Validate URL?
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the idp certificate property for changes
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */
    private function saml_idp_certificate(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[IP_CERT_LABEL]]' =>  __("Identity Provider Public X509 Certificate", "phpsaml"),
            '[[IP_CERT_TITLE]]' =>  __("Public x509 certificate of the Identity Provider.", "phpsaml"),
            '[[IP_CERT_VALUE]]' => $dbConf,
            '[[IP_CERT_ERROR]]' => ''
        ];

        if (!strstr($dbConf, '-BEGIN CERTIFICATE-') || !strstr($dbConf, '-END CERTIFICATE-')) {
            $formValues['[[IP_CERT_ERROR]]'] = "This does not look like a valid certificate, please make sure to include the certificate BEGIN and END tags";
        }
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the authn context property for changes
     * and populates the form template
     *
     * @param string $dbConf
     * @return boolean
     */
    private function requested_authn_context(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[AUTHN_LABEL]]' =>  __("Requested Authn Context", "phpsaml"),
            '[[AUTHN_TITLE]]' =>  __("Set to None and no AuthContext will be sent in the AuthnRequest, oth", "phpsaml"),
            '[[AUTHN_SELECT]]' => '',
            '[[AUTHN_ERROR]]' => false
        ];

        // Generate the options array
        $options = ['PasswordProtectedTransport'  => __('PasswordProtectedTransport', 'phpsaml'),
                    'Password'                    => __('Password', 'phpsaml'),
                    'X509'                        => __('X509', 'phpsaml'),
                    'None'                        => __('None', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[AUTHN_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




   /**
     *
     * Evaluates the requested authn context comparison property for changes
     * and populates the form template
     *
     * @param string $dbConf
     * @return void
     */
    private function requested_authn_context_comparison(string $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[AUTHN_COMPARE_LABEL]]' =>  __("Requested Authn Comparison", "phpsaml"),
            '[[AUTHN_COMPARE_TITLE]]' =>  __("How should the library compare the requested Authn Context?  The value defaults to 'Exact'.", "phpsaml"),
            '[[AUTHN_COMPARE_SELECT]]'=> '',
            '[[AUTHN_COMPARE_ERROR]]' => false
        ];

        // Generate the options array
        $options = ['exact'  => __('Exact', 'phpsaml'),
                    'minimum'=> __('Minimum', 'phpsaml'),
                    'maximum'=> __('Maximum', 'phpsaml'),
                    'better' => __('Better', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[AUTHN_COMPARE_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the nameid encrypted property for changes
     * and populates the form template
     *
     * @param int $dbConf
     * @return void
     */
    private function saml_security_nameidencrypted(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[ENCR_NAMEID_LABEL]]' =>  __("Encrypt NameID", "phpsaml"),
            '[[ENCR_NAMEID_TITLE]]' =>  __("Toggle yes to encrypt NameID.  Requires service provider certificate and key", "phpsaml"),
            '[[ENCR_NAMEID_SELECT]]'=> '',
            '[[ENCR_NAMEID_ERROR]]' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[ENCR_NAMEID_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the authn requests signed property for changes
     * and populates the form template
     *
     * @param int $dbConf
     * @return void
     */
    private function saml_security_authnrequestssigned(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[SIGN_AUTHN_REQ_LABEL]]' =>  __("Sign Authn Requests", "phpsaml"),
            '[[SIGN_AUTHN_REQ_TITLE]]' =>  __("Toggle yes to sign Authn Requests.  Requires service provider certificate and key", "phpsaml"),
            '[[SIGN_AUTHN_REQ_SELECT]]'=> '',
            '[[SIGN_AUTHN_REQ_ERROR]]' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[SIGN_AUTHN_REQ_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the Logout Request Signed property 
     * and populates the form template
     *
     * @param int $dbConf
     * @return boolean
     */
    private function saml_security_logoutrequestsigned(int $dbConf)
    {
        // Declare template labels
        $formValues = [
            '[[SIGN_LOGOUT_REQ_LABEL]]' =>  __("Sign Logout Requests", "phpsaml"),
            '[[SIGN_LOGOUT_REQ_TITLE]]' =>  __("Toggle yes to sign Logout Requests.  Requires service provider certificate and key", "phpsaml"),
            '[[SIGN_LOGOUT_REQ_SELECT]]'=> '',
            '[[SIGN_LOGOUT_REQ_ERROR]]' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[SIGN_LOGOUT_REQ_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the Logout Response Signed property 
     * and populates the form template
     *
     * @param int $dbConf
     * @return void
     */
    private function saml_security_logoutresponsesigned(int $dbConf)
    {
         // Declare template labels
         $formValues = [
            '[[SIGN_LOGOUT_RES_LABEL]]' => __("Sign Logout Requests", "phpsaml"),
            '[[SIGN_LOGOUT_RES_TITLE]]' => __("Toggle yes to sign Logout Requests.  Requires service provider certificate and key", "phpsaml"),
            '[[SIGN_LOGOUT_RES_SELECT]]'=> '',
            '[[SIGN_LOGOUT_RES_ERROR]]' => false
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $dbConf) ? 'selected' : '';
            $formValues['[[SIGN_LOGOUT_RES_SELECT]]'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }




    /**
     *
     * Evaluates the id property for changes
     * and populates the form template
     *
     * @param int $dbConf
     * @return void
     */
    private function id(int $dbConf)
    {
        $this->formValues['[[ID]]'] = $dbConf;
    }




    /**
     * Validates the provided Phpsaml version against the git repository
     * if $return is true method will return collected information in an array.
     *
     * version($dbConf, $return);
     *
     * @param string $compare       //version to compare
     * @param bool $return          //return the outcomes
     * @return void|array $outcomes //optional return
     */
    public function version(string $compare, bool $return = false)
    {
        if ($feed = implode(file($this->PhpsamlGitAtomUrl))) {
            if ($xmlArray = simplexml_load_string($feed)) {
                $href = (string) $xmlArray->entry->link['href'];
                preg_match('/.* (.+)/', (string) $xmlArray->entry->title, $version);
                if (is_array($version)) {
                    $v = $version['1'];
                    if ($v <> $compare) {
                        if ($return) {
                            return ['gitVersion' => $v,
                                    'compare'    => $compare,
                                    'gitUrl'     => $href,
                                    'latest'     => true];
                        }
                        $this->formValues['[[VERSION]]'] = "<font style='color:green;'><a href='$href' target='_blank'>A new version of Phpsaml is available</a>. Version $v was found in the repository, you are running $compare</font>";
                    } else {
                        if ($return) {
                            return ['gitVersion' => $v,
                                    'compare'    => $compare,
                                    'gitUrl'     => $href,
                                    'latest'     => false];
                        }
                        $this->formValues['[[VERSION]]'] = "<font style='color:green;'>You are using version $v which is also the <a href='$href' target='_blank'>latest version</a></font>";
                    }
                } else {
                    $this->registerError("Could not correctly parse xml information from:".$this->PhpsamlGitAtomUrl." is simpleXml available?");
                }
            } else {
                $this->registerError("Could not correctly parse xml information from:".$this->PhpsamlGitAtomUrl." is simpleXml available?");
            }
        } else {
            $this->registerError("Could not retrieve version information from:".$this->PhpsamlGitAtomUrl." is internet access blocked?");
        }
    }
}
