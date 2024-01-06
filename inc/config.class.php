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

// HEADERGUARD GLPI MUST BE LOADED
if (!defined("GLPI_ROOT")) { die("Sorry. You can't access directly to this file"); }

class PluginPhpsamlConfig extends CommonDBTM
{
    // CONSTANTS
     /**
     * @todo: make sure all references to configuration properties use these constants
     **/
	public const STRICT	 = 'strict'; // todo: make sql add default value 0 so we can clear allot of additional isset checking.
	public const DEBUG	 = 'debug';  // todo: make sql add default value 0 so we can clear allot of additional isset checking.
	public const SPCERT	 = 'saml_sp_certificate';
	public const SPKEY	 = 'saml_sp_certificate_key';
	public const NAMEFM	 = 'saml_sp_nameid_format';
	public const ENTITY	 = 'saml_idp_entity_id';
	public const SSOURL	 = 'saml_idp_single_sign_on_service';
	public const SLOURL	 = 'saml_idp_single_logout_service';
	public const IPCERT	 = 'saml_idp_certificate';
	public const CMPREQ	 = true; // Compress requests
	public const CMPRES	 = true; // Compress response
	public const ENAME	 = 'saml_security_nameidencrypted';
	public const SAUTHN	 = 'saml_security_authnrequestssigned';
	public const SSLORQ	 = 'saml_security_logoutrequestsigned';
	public const SSLORE	 = 'saml_security_logoutresonsesigned';
	public const AUTHNC	 = 'requested_authn_context';
	public const AUTHND	 = 'requested_authn_context_comparison'; //diff
	public const XMLVAL	 = true;  // Perform xml validation
	public const DSTVAL	 = false; // relax destination validation
	public const LOWURL	 = true;  // lowercaseUrlEncoding
    public const ACSPATH = '/plugins/phpsaml/front/acs.php';
    public const SLOPATH = '/plugins/phpsaml/front/slo.php';
    public const FORCED  = 'enforced';
    public const PROXIED = 'proxied';
    public const CFNAME  = 'saml_configuration_name';


    // PROPERTIES
    /**
     * defines the rights a user must posses to be able to access this menu
     * option in the rules section. Also required to be added to config menu.
     * @var string
     **/
    public static $rightname     = 'config';

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
       return _n('Saml config', 'Saml config', $nb, 'phpsaml');
   }

   /**
     * getIcon() : string -
     * Sets icon for object.
     *
     * @return string   $icon
     */
    public static function getIcon() : string
    {
        return 'fas fa-address-book';
    }


	//public static $rightname = 'plugin_phpsaml_config';


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
    private $expectedItems = 21;


    /**
     * Where to get the current version
     * Change value for unit testing
     * @var string
     **/
    private $phpSamlGitAtomUrl = 'https://github.com/derricksmith/phpsaml/releases.atom';

    /**
     * Stores a copy of the phpSaml Database Configuration
     * @var array
     **/
    private $config = [];


    /**
     * Registers if a fatal error occured during execution;
     * @var mixed
     **/
    private $fatalError = false;

    /**
     * Registers if a fatal error occured during execution;
     * @var mixed
     **/
    private $warningError = false;


    /**
     * Registers errors that occured in a friendly format;
     * @var array
     **/
    private $errorMsgs = [];


    // METHODS
    /**
     *
     * Generate the configuration htmlForm and return it.
     * @param mixed $id         The id of the configuration
     * @param array $options    Not used
     * @return string           Returns the generated html form
     * @since                   1.2.1
     */
    public function showForm($id, $options = [])
    {
        // Populate current configuration
        if (is_numeric($id) && $this->config = $this->getConfig($id)) {
            // Call the form field handlers
            if (is_array($this->config)) {
                foreach ($this->config as $method => $current) {
                    if (method_exists($this, $method)) {
                        // Make sure we pass strings on null values.
                        $current = (is_null($current)) ? '' : $current;
                        // Handle property
                        $this->$method($current);
                    } else {
                        if ($method != 'valid') {
                            $this->registerError(__("🟨 No handler found for configuration item: $method in ".__class__." db corrupted?", 'phpsaml'));
                        }
                    }
                }
            } else {
                $this->registerError("🟥 Db config did not return required config array", true);
            }
        } else {
            $this->registerError("🟥 Unknown configuration requested", true);
        }
        return $this->generateForm(true);
    }
    

    /**
     *
     * process $_POST values of the updated form. On error it will regenerate the form with
     * errors and provided values and will not process the form and will loop untill the errors
     * are fixed. Navigating away will reset the form.
     *
     * @return string   HTML Form or header redirect
     * @since           1.2.1
     * @todo            add option to reset the form with configuration items calling discarding the POST and caling 'show form'
     */
    public function processChanges()
    {
        // populate config
        $id = (isset($_POST['id']) && is_numeric($_POST['id']) && (strlen($_POST['id']) < 10)) ? (int) $_POST['id'] : '1';
        $this->config = $this->getConfig($id);

        // Use the POST values to iterate through the
        // handlers and make them validate the input.
        foreach ($_POST as $method => $value) {
            if (array_key_exists($method, $this->config)) {
                // We can safely call this valid method
                $this->$method($value);
            }
        }

        // If we have fatal errors, then show the form and prevent an update
        // TODO: For some reason, fatal errors make all the text areas add \r\n characters 
        // else process the update.
        if ($this->fatalError) {
            return $this->generateForm();
        } else {
            $this->update($_POST);
            Html::back();
        }
    }


    /**
     *
     * Gets the current configuration from the database. It will first query the columns of the configuration
     * table. It will then use these columns to fetch all the related database configuration values and place them
     * in a structured array. Finally this structured array is returned. The caller should evaluate the 'valid' array
     * key to validate the configuration array is usable.
     *
     * @param string|int $id    What configuration to fetch.
     * @param string $property  return 1 specific configuration property if it exists
     * @return array $config    returns array of properties
     * @since                   1.2.1
     * @todo                    Needs attention, everything depends on this being succesfull. If config is bugged everything will break, including login, config, etc.
     * @todo                    Add translations for errors
     */
    public function getConfig($id = '1')
	{
        global $DB;
        $config = [];
		$sql = 'SHOW COLUMNS FROM '.$this->getTable();
		if ($result = $DB->query($sql)) {
            if (is_numeric($id) && $this->getFromDB($id)) {
                while ($data = $result->fetch_assoc()) {
                    $config[$data['Field']] =  $this->fields[$data['Field']];
                }

                if (count($config) <> $this->expectedItems) {
                    $this->registerError('🟨 Phpsaml expected '.$this->expectedItems.' configuration items but got '.count($config).' items instead triggered update');
                    // Update is not triggered in all cases
                    // This is just a quick check/fix
                    include_once( PLUGIN_PHPSAML_DIR . "/install/update.class.php" );
                    $update = new PluginPhpsamlUpdate();
                }
            } else {
                $this->registerError('🟥 Phpsaml could not retrieve configuration values from database.', 'general', true);
            }
        } else {
            $this->registerError('🟥 Phpsaml was not able to retrieve configuration columns from database', 'general', true);
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
     * @param bool $return  return the generated htmlform as string
     * @return string       HTML of the form
     * @since               1.2.1
     * @todo                replace with twig templates
     */
    private function generateForm() : string
    {
        global $CFG_GLPI;

        // Read the template file containing the HTML template;
        if (file_exists($this->tpl)) {
            $this->htmlForm = file_get_contents($this->tpl);
        }

        // Declare general form values
        $formValues = [
            'AVAILABLE'              => __('Available', 'phpsaml'),
            'SELECTED'               => __('Selected', 'phpsaml'),
            'GLPI_ROOTDOC'           =>   $CFG_GLPI["root_doc"],
            'TITLE'                  => __("PHP SAML Configuration", "phpsaml"),
            'HEADER_GENERAL'         => __("General", "phpsaml"),
            'HEADER_PROVIDER'        => __("Service Provider Configuration", "phpsaml"),
            'HEADER_PROVIDER_CONFIG' => __("Identity Provider Configuration", "phpsaml"),
            'HEADER_SECURITY'        => __("Security", "phpsaml"),
            'SUBMIT'                 => __("Update", "phpsaml"),
            'CLOSE_FORM'             =>   Html::closeForm(false)
        ];
        // Merge the values in the central array.
        $this->formValues = array_merge($this->formValues, $formValues);
    
        // Process generic errors if any
        if (count($this->errorMsgs) > 0) {
            

            // Process the error messages;
            $nice = '';
            foreach ($this->errorMsgs as $k => $errmsg) {
                $nice .= $errmsg.'<br>';
            }

            $this->formValues['ERRORS'] = ' <div class="alert mb-o rounded-0 border-top-0 border-bottom-0 border-right-0 full-width" role="alert">'.$nice.'</div>';
        } else {
            $this->formValues['ERRORS'] = '';
        }

        // Add template curlies to the keys and make sure
        // all keys are upper case;
        foreach ($this->formValues as $key => $value){
            $tplValues['{{'.strtoupper($key).'}}'] = $value;
        }

        // Disable the form if a fatal was generated.
        $this->htmlForm = ($this->fatalError) ? str_replace('{{DISABLED}}', 'DISABLED', $this->htmlForm) : str_replace('{{DISABLED}}', '', $this->htmlForm);
        if(is_array($tplValues)){
            if ($html = str_replace(array_keys($tplValues), array_values($tplValues), $this->htmlForm)) {
                // Clean any remaining placeholders like {{ERRORS}}
                $html = preg_replace('/{{.*}}/', '', $html);
                return $html;
            }
        }
        // If we end up here, something is wrong!
        // Disable the form because prob the database is not available or populated.
        // replace all elements with error values;
        // Return the unusable form hopefully with good errors to fix.
        $this->htmlForm = ($this->fatalError) ? str_replace('{{DISABLED}}', 'DISABLED', $this->htmlForm) : str_replace('{{DISABLED}}', '', $this->htmlForm);
        $html = preg_replace('/{{.*}}/', 'error', $html);
        return $html;

    }

    
    /**
     *
     * Adds an error message to the errorMsgs class property. The errorMsgs class property is iterated by the generateForm
     * method to inject all errors into the top header of the htmlForm. If fatal is set to true the generateForm method will
     * disable all form elements and make sure an insert/update can no longer be performed.
     *
     * @param string $errorMsg  the error message
     * @param string $field     generate field specific error.
     * @param bool $fatal       is it fatal?
     * @param bool $warning     is it a warning?
     * @return void
     * @since                   1.2.1
     */
    private function registerError(string $errorMsg, string $field=null, bool $fatal=false, bool $warning=true) : void
    {
        $errorMsg = (!empty($errorMsg)) ? $errorMsg : 'No error information provided';

        // Warning will prevent update from being executed allowing form changes;
        $this->warningError = ($warning) ? true : $this->warningError;

        //Fatal will prevent update from being executed disabling forms.
        $this->fatalError = ($fatal) ? true : $this->fatalError;

        // Create field specific error else generate generic error
        if ($field === null) {
            $this->errorMsgs[] = $errorMsg;
        }else{
            $spaceholder = '{{'.strtoupper($field).'}}';
            $this->formValues[$spaceholder] = __($errorMsg, 'phpsaml');
        }
    }

     /**
     *
     * Adds an error message to the errorMsgs class property. The errorMsgs class property is iterated by the generateForm
     * method to inject all errors into the top header of the htmlForm. If fatal is set to true the generateForm method will
     * disable all form elements and make sure an insert/update can no longer be performed.
     *
     * @param string $certString    the error message
     * @return array $certDetails
     * @since        1.2.1
     */
    public function validateAndParseCertString(string $cert) : array
    {
        // Do some basic validations
        $validationErrors['BEGIN_TAG_PRESENT']  = (!preg_match('/-+BEGIN CERTIFICATE-+/', $cert)) ? false : true;
        $validationErrors['END_TAG_PRESENT']    = (!preg_match('/-+END CERTIFICATE-+/', $cert)) ? false : true;

        // Only chr(10) \n is allowed in an X509 certificate
        // Only apply filtering if GLPI added CRLF
        if(strpos($cert, "\r")){
            $cert = preg_replace('/\r\n|\r|\n/', '', $cert);
            // Match the certificate elements using non greedy payload search   
        }

        // Validate certificate components
        preg_match('/(-+BEGIN CERTIFICATE-+)(.+?)(-+END CERTIFICATE-+)/', $cert, $m);

        // There should be exactly 4 matches!
        if (count($m) == 4) {
            // Reconstruct the certificate including the correct openssl CRLF
            $validationErrors['CERT_SEMANTICS_VALID'] = true;
            $cert = $m['1'].chr(10).$m['2'].chr(10).$m['3'];
        } else {
            $validationErrors['CERT_SEMANTICS_VALID'] = false;
        }
        
        // Try to parse the reconstructed certificate.
        if (extension_loaded('openssl') && ($validationErrors['CERT_SEMANTICS_VALID'] == true)) {
            if ($pCert = openssl_x509_parse($cert)) {
                $validationErrors['CERT_LOGIC_VALID'] = true;
            } else {
                $validationErrors['CERT_LOGIC_VALID'] = false;
            }
        } else {
            $validationErrors['CERT_LOGIC_VALID'] = 'Certificate is not validated, openssl might be disabled, or the certificate is not valid';
        }

        // Calculate additional fields and add them to
        // a structured certificate array.
        if (isset($pCert) && !empty($pCert)) {
            // Work out the certificate timestamps
            $n = new DateTimeImmutable('now');
            $t = (array_key_exists('validTo', $pCert)) ? DateTimeImmutable::createFromFormat("ymdHisT", $pCert['validTo']) : false;
            $f = (array_key_exists('validFrom', $pCert)) ? DateTimeImmutable::createFromFormat("ymdHisT", $pCert['validFrom']) : false;
            $d = $n->diff($t);
            $cn= (array_key_exists('subject', $pCert) && array_key_exists('CN', $pCert['subject'])) ? $pCert['subject']['CN'] : '';
            $io= (array_key_exists('issuer', $pCert) && array_key_exists('O', $pCert['issuer'])) ? $pCert['issuer']['O'] : '';
            $icn=(array_key_exists('issuer', $pCert) && array_key_exists('CN', $pCert['issuer'])) ? $pCert['issuer']['CN'] : '';

            return [
                'msgs'          => $validationErrors,
                'certStr'       => $cert,
                'certDetails'   => ['cn'  => $cn,
                                    'isO' => $io,
                                    'isCN'=> $icn],
                'validTo'       => $t->format('Y-m-d'),
                'validFrom'     => $f->format('Y-m-d'),
                'certAge'       => $d->format('%R%a')
            ];
        } else {
            return [
                'msgs'          => $validationErrors,
                'certStr'       => $cert,
                'certDetails'   => false,
                'validTo'       => false,
                'validFrom'     => false,
                'certAge'       => false
            ];
        }
    }



    /**********************************
     *
     * Evaluates the enforced property and populates the form template
     *
     * @param int $cValue   configuration value to process
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function enforced(int $cValue) : void
    {
        // Validate the input value
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Enforced can only be 1 or 0", 'ENFORCED_ERROR');
        }

        // Do lable translations
        $formValues = [
            'ENFORCED_LABEL' =>  __("Plugin Enforced", "phpsaml"),
            'ENFORCED_TITLE' =>  __("Toggle 'yes' to enforce Single Sign On for all login sessions", "phpsaml"),
            'ENFORCED_SELECT'=> ''
        ];

        // Generate select options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['ENFORCED_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);

    }


     /**
     *
     * Evaluates the strict property and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function strict(int $cValue) : void
    {
        // Validate the input value
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Strict can only be 1 or 0", 'STRICT_ERROR');
        }

        // Declare template labels
        $formValues = [
            'STRICT_LABEL' =>  __("Strict", "phpsaml"),
            'STRICT_TITLE' =>  __("If 'strict' is True, then PhpSaml will reject unencrypted messages", "phpsaml"),
            'STRICT_SELECT'=>  ''
        ];

        // Generate select options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['STRICT_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the debug property and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function debug(int $cValue) : void
    {
        // Validate the input value
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Debug can only be 1 or 0", 'DEBUG_ERROR');
        }

        // Check if debug directory is present and warn user
        if(PluginPhpsamlAcs::checkDebugDir()) {
            $this->registerError('⚠️ Warning a debug directory is detected in the plugin root. If debug is enabled potential vulnerable SAML responses are dumped there! Remove the directory if you are not debugging!');
        }

        // Declare template labels
        $formValues = [
            'DEBUG_LABEL' =>  __("Debug", "phpsaml"),
            'DEBUG_TITLE' =>  __("Toggle yes to print errors", "phpsaml"),
            'DEBUG_SELECT'=> ''
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['DEBUG_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the debug property and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function proxied(int $cValue) : void
    {
        // Validate the input value
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Proxied can only be 1 or 0", 'DEBUG_ERROR');
        }

        // Declare template labels
        $formValues = [
            'PROXIED_LABEL' =>  __("Saml Proxied", "phpsaml"),
            'PROXIED_TITLE' =>  __("Make phpsaml parse X_FORWARDED headers", "phpsaml"),
            'PROXIED_SELECT'=> ''
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['PROXIED_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the sp certificate property and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function jit(int $cValue) : void
    {
        // Validate the input value
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Jit can only be 1 or 0", 'JIT_ERROR');
        }

        // Declare template labels
        $formValues = [
            'JIT_LABEL' =>  __("Just In Time (JIT) Provisioning", "phpsaml"),
            'JIT_TITLE' =>  __("Toggle 'yes' to create new users if they do not already exist.  Toggle 'no' will cause an error if the user does not already exist in GLPI.", "phpsaml"),
            'JIT_SELECT'=> ''
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['JIT_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the sp certificate property and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     * @todo                    Suspected that GLPI is applying input- output filters replacing ch(10) with CRLF that might break certificate on successive updates.
     *                          now being addressed in the validateAndParseCertString() but is not fully tested (fuzzing) yet .
     */
    protected function saml_sp_certificate(string $cValue) : void
    {
        // Validate certificate
        if(!empty($cValue)) {
            $cert = $this->validateAndParseCertString($cValue);

            

            if(!$cert['msgs']['BEGIN_TAG_PRESENT'] || !$cert['msgs']['END_TAG_PRESENT']) {
                $this->registerError('⚠️ The optional SP Certificate does not look valid');
            }

            if (is_array($cert['certDetails'])) {
                $valid = (strpos($cert['certAge'],'-') !== false) ? '<font style="color:red">expired:'.$cert['certAge'].' day(s) ago</font>' : '<font style="color:green">is valid the next:'.$cert['certAge'].' day(s)</font>';
                $cer = "🟩 Configured Service Provider cert was issued by: {$cert['certDetails']['isCN']} for: {$cert['certDetails']['cn']} and $valid";
            } else {
                $cer = '🟨 No Service Provider certificate details provided or provided data is invalid';
            }
        } else {
            // Are we in strict mode?
            $cer = '';
            if($this->config[SELF::STRICT]) {
                $cer .= '🟥 Strict cannot be enabled if no Service Provider certificate has been configured<br>';
            }
            $cer .= '🟦 The optional Service Provider Certificate is not configured, we strongly recommend that you do and enable strict mode';
        }
        
       
        
        // Declare template labels
        $formValues = [
            'SP_CERT_LABEL' =>  __("Service Provider Certificate", "phpsaml"),
            'SP_CERT_TITLE' =>  __("Certificate we should use when communicating with the Identity Provider. Use one long string without returns!", "phpsaml"),
            'SP_CERT_VALUE' => $cValue,
            'SP_CERT_VALID' => "$cer"
        ];

        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the sp certificate key property and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     * @todo                    write key validation function using openssl.
     */
    protected function saml_sp_certificate_key(string $cValue) : void
    {
        // Declare template labels
        $formValues = [
            'SP_KEY_LABEL' =>  __("Service Provider Certificate Key", "phpsaml"),
            'SP_KEY_TITLE' =>  __("Certificate private key we should use when communicating with the Identity Provider", "phpsaml"),
            'SP_KEY_VALUE' => $cValue];

        // Do some basic validations
        // An error here should never be fatal as field is not required.
        if (!strstr($cValue, '-BEGIN PRIVATE KEY-') || !strstr($cValue, '-END PRIVATE KEY-')) {
            $this->registerError('This does not look like a valid private key, please make sure to include the private key BEGIN and END tags','SP_KEY_ERROR');
        }
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the saml sp nameid format property and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function saml_sp_nameid_format(string $cValue) : void
    {
         // Declare template labels
         $formValues = [
            'SP_ID_LABEL' =>  __("Name ID Format", "phpsaml"),
            'SP_ID_TITLE' =>  __("The name id format that is sent to the iDP.", "phpsaml"),
            'SP_ID_SELECT' => ''];

        // Generate the options array
        $options = ['unspecified'  => __('Unspecified', 'phpsaml'),
                    'emailAddress' => __('Email Address', 'phpsaml'),
                    'transient'    => __('Transient', 'phpsaml'),
                    'persistent'   => __('Persistent', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['SP_ID_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the idp entity id property and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function saml_idp_entity_id(string $cValue) : void
    {
        //Validate value
        if(!empty($cValue)) {
            if((!strstr($cValue, 'https')) && (filter_var($cValue, FILTER_VALIDATE_URL) === FALSE)) {
                
                $this->registerError('🟨 Provided IdP entity ID URL does not look like a valid TLS enabled URL');
            }
        } else {
            $this->registerError('🟨 The IdP entity ID URL required field.');
        }

        
        // Declare template labels
        $formValues = [
            'IP_ID_LABEL' =>  __("Identity Provider Entity Id", "phpsaml"),
            'IP_ID_TITLE' =>  __("Identifier of the IdP entity  (must be a URI).", "phpsaml"),
            'IP_ID_VALUE' => $cValue];
        
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the idp single sign on service property and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function saml_idp_single_sign_on_service(string $cValue) : void
    {
        //Validate URL
        if(!empty($cValue)) {
            if((!strstr($cValue, 'https')) && (filter_var($cValue, FILTER_VALIDATE_URL) === FALSE)) {
                $this->registerError('🟨 Provided sign on service URL does not look like a valid TLS enabled URL');
            }
        } else {
            $this->registerError('🟨 The single sign on service URL required field.');
        }
        
        // Declare template labels
        $formValues = [
            'IP_SSO_URL_LABEL' =>  __("Identity Provider Single Sign On Service URL", "phpsaml"),
            'IP_SSO_URL_TITLE' =>  __("URL Target of the Identity Provider where we will send the Authentication Request Message.", "phpsaml"),
            'IP_SSO_URL_VALUE' => $cValue];
 
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the idp single logout service property for changes and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function saml_idp_single_logout_service(string $cValue) : void
    {
        //Validate URL
        if(!empty($cValue)) {
            if((!strstr($cValue, 'https')) && (filter_var($cValue, FILTER_VALIDATE_URL) === FALSE)) {
                $this->registerError('🟨 Provided IdP single logout URL does not look like a valid URL', '');
            }
        } else {
            $this->registerError('🟨 The IdP single logout URL  is a required field.', '');
        }
        
         // Declare template labels
         $formValues = [
            'IP_SLS_URL_LABEL' =>  __("Identity Provider Single Logout Service URL", "phpsaml"),
            'IP_SLS_URL_TITLE' =>  __("URL Location of the Identity Provider where GLPI will send the Single Logout Request.", "phpsaml"),
            'IP_SLS_URL_VALUE' => $cValue];
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the idp certificate property for changes and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function saml_idp_certificate(string $cValue) : void
    {
        $cert = $this->validateAndParseCertString($cValue);

        $validationErrors ='';
        $validationErrors .= (!$cert['msgs']['BEGIN_TAG_PRESENT']) ? 'The certificate BEGIN tag should be present<br>' : '';
        $validationErrors .= (!$cert['msgs']['END_TAG_PRESENT']) ? 'The certificate END tag should be present<br>' : '';

        if (is_array($cert['certDetails'])) {
            $valid = (strpos($cert['certAge'],'-') !== false) ? '<font style="color:red">expired:'.$cert['certAge'].' day(s) ago</font>' : '<font style="color:darkgreen">is valid for another:'.$cert['certAge'].' day(s)</font>';
            $cer = "🟩 Configured IdP cert was issued by: {$cert['certDetails']['isCN']} for: {$cert['certDetails']['cn']} and $valid";
        } else {
            $cer = '🟥 <font color="red">No valid Ipd certificate details provided or available</font>';
        }
        
        if ($validationErrors) {
            $this->registerError($validationErrors, 'IP_CERT_ERROR');
        }

        // Declare template labels
        $formValues = [
            'IP_CERT_LABEL' =>  __("Identity Provider Public X509 Certificate", "phpsaml"),
            'IP_CERT_TITLE' =>  __("Public x509 certificate of the Identity Provider.", "phpsaml"),
            'IP_CERT_VALUE' => $cValue,
            'IP_CERT_VALID' => "$cer"
        ];
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the authn context property for changes and populates the form template
     *
     * @param string $cValue
     * @return boolean
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function requested_authn_context(string $cValue) : void
    {
        /*This value uses a multi select that generates a comma separated value. This value
          is processed by JS code in the HTML template to create the multiselect.*/
         
        $cValue = (empty($cValue)) ? 'none' : $cValue;

        // Declare template labels
        $formValues = [
            'AUTHN_LABEL' =>  __("Requested Authn Context", "phpsaml"),
            'AUTHN_TITLE' =>  __("Set to None and no AuthContext will be sent in the AuthnRequest, oth", "phpsaml"),
            'AUTHN_SELECT' => '',
            'AUTHN_CONTEXT' => $cValue
        ];

        // Generate the options array
        $options = ['PasswordProtectedTransport'  => __('PasswordProtectedTransport', 'phpsaml'),
                    'Password'                    => __('Password', 'phpsaml'),
                    'X509'                        => __('X509', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['AUTHN_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


   /**
     *
     * Evaluates the requested authn context comparison property for changes and populates the form template
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function requested_authn_context_comparison(string $cValue) : void
    {
        // Declare template labels
        $formValues = [
            'AUTHN_COMPARE_LABEL' =>  __("Requested Authn Comparison", "phpsaml"),
            'AUTHN_COMPARE_TITLE' =>  __("How should the library compare the requested Authn Context?  The value defaults to 'Exact'.", "phpsaml"),
            'AUTHN_COMPARE_SELECT'=> ''
        ];

        // Generate the options array
        $options = ['exact'  => __('Exact', 'phpsaml'),
                    'minimum'=> __('Minimum', 'phpsaml'),
                    'maximum'=> __('Maximum', 'phpsaml'),
                    'better' => __('Better', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['AUTHN_COMPARE_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the nameid encrypted property for changes and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function saml_security_nameidencrypted(string $cValue) : void
    {
        // Validate input
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Nameidencrypted can only be 1 or 0", 'ENCR_NAMEID_ERROR');
        }

        // Declare template labels
        $formValues = [
            'ENCR_NAMEID_LABEL' =>  __("Encrypt NameID", "phpsaml"),
            'ENCR_NAMEID_TITLE' =>  __("Toggle yes to encrypt NameID.  Requires service provider certificate and key", "phpsaml"),
            'ENCR_NAMEID_SELECT'=> ''
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['ENCR_NAMEID_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the authn requests signed property for changes and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function saml_security_authnrequestssigned(string $cValue) : void
    {
        // Validate input
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Authnrequestssigned can only be 1 or 0", 'SIGN_AUTHN_REQ_ERROR');
        }

        // Declare template labels
        $formValues = [
            'SIGN_AUTHN_REQ_LABEL' =>  __("Sign Authn Requests", "phpsaml"),
            'SIGN_AUTHN_REQ_TITLE' =>  __("Toggle yes to sign Authn Requests.  Requires service provider certificate and key", "phpsaml"),
            'SIGN_AUTHN_REQ_SELECT'=> ''
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['SIGN_AUTHN_REQ_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the Logout Request Signed property and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function saml_security_logoutrequestsigned(string $cValue) : void
    {
        // Validate input
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Logoutrequestsigned can only be 1 or 0", 'SIGN_LOGOUT_REQ_ERROR');
        }

        // Declare template labels
        $formValues = [
            'SIGN_LOGOUT_REQ_LABEL' =>  __("Sign Logout Requests", "phpsaml"),
            'SIGN_LOGOUT_REQ_TITLE' =>  __("Toggle yes to sign Logout Requests.  Requires service provider certificate and key", "phpsaml"),
            'SIGN_LOGOUT_REQ_SELECT'=> ''
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['SIGN_LOGOUT_REQ_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the Logout Response Signed property and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function saml_security_logoutresponsesigned(string $cValue) : void
    {
        // Validate input
        if (!preg_match('/[0-1]/', $cValue)) {
            $this->registerError("Logoutresponsesigned can only be 1 or 0", 'SIGN_LOGOUT_RES_ERROR');
        }

        // Declare template labels
        $formValues = [
            'SIGN_LOGOUT_RES_LABEL' => __("Sign Logout Requests", "phpsaml"),
            'SIGN_LOGOUT_RES_TITLE' => __("Toggle yes to sign Logout Requests.  Requires service provider certificate and key", "phpsaml"),
            'SIGN_LOGOUT_RES_SELECT'=> ''
        ];

        // Generate options
        $options = [ 1 => __('Yes', 'phpsaml'),
                     0 => __('No', 'phpsaml')];

        foreach ($options as $value => $label) {
            $selected = ($value == $cValue) ? 'selected' : '';
            $formValues['SIGN_LOGOUT_RES_SELECT'] .= "<option value='$value' $selected>$label</option>";
        }

        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


     /**
     *
     * Evaluates the configuration friendly name property for changes and populates the form template
     * https://github.com/derricksmith/phpsaml/issues/126
     *
     * @param string $cValue
     * @return void
     * @since                   1.2.1
     * @todo                    write unit test
     */
    protected function saml_configuration_name(string $cValue) : void
    {
         // Declare template labels
         $formValues = [
            'CONF_NAME_LABEL' =>  __("Provider friendly name", "phpsaml"),
            'CONF_NAME_TITLE' =>  __("Provider friendly name as shown on the logon button", "phpsaml"),
            'CONF_NAME_VALUE' => $cValue];

        //Validate URL?
        
        // Merge outcomes in formValues
        $this->formValues = array_merge($this->formValues, $formValues);
    }


    /**
     *
     * Evaluates the id property for changes and populates the form template
     *
     * @param int $cValue
     * @return void
     * @since               1.2.1
     * @todo                write unit test
     */
    protected function id(int $cValue) : void
    {
        $this->formValues['ID'] = $cValue;
    }


    /**
     * Validates the provided Phpsaml version against the git repository
     * if $return is true method will return collected information in an array.
     *
     * version($dbConf, $return);
     *
     * @param string $compare       Version to compare
     * @param bool $return          Return the outcomes
     * @return array|void $outcomes      Optional return
     * @since                       1.2.1
     * @todo                        write unit test
     */
    public function version($compare, $return = false)
    {
        if ($feed = implode(file($this->phpSamlGitAtomUrl))) {
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
                        $this->formValues['VERSION'] = "<a href='$href' target='_blank'>🟨 A different version of Phpsaml is marked latest</a>. Version $v was found in the repository, you are running $compare";
                    } else {
                        if ($return) {
                            return ['gitVersion' => $v,
                                    'compare'    => $compare,
                                    'gitUrl'     => $href,
                                    'latest'     => false];
                        }
                        $this->formValues['VERSION'] = "🟩 You are using version $v which is also the <a href='$href' target='_blank'>latest version</a>";
                    }
                } else {
                    $this->registerError("Could not correctly parse xml information from:".$this->phpSamlGitAtomUrl." is simpleXml available?");
                    $this->formValues['VERSION'] = "🟥 Phpsaml could not verify the latest version, please verify manually";
                }
            } else {
                $this->registerError("Could not correctly parse xml information from:".$this->phpSamlGitAtomUrl." is simpleXml available?");
                $this->formValues['VERSION'] = "🟥 Phpsaml could not verify the latest version, please verify manually";
            }
        } else {
            $this->registerError("Could not retrieve version information from:".$this->phpSamlGitAtomUrl." is internet access blocked?");
            $this->formValues['VERSION'] = "🟥 Phpsaml could not verify the latest version, please verify manually";
        }
        if ($return) {
            // Return dummy array.
            return ['gitVersion' => 'Unknown',
                    'compare'    => $compare,
                    'gitUrl'     => '',
                    'latest'     => false];
        }
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
        $version = PLUGIN_PHPSAML_VERSION;
        $table = self::getTable();

        // TODO: use mysql transaction and commit only when all queries are successfull.

        // Create the base table if it does not yet exist;
        // Dont update this table for later versions, use the migration class;
        if (!$DB->tableExists($table)) {
            $migration->displayMessage("Installing $table");
            $query = <<<SQL
            CREATE TABLE `$table` (
                `id` int(11) {$default_key_sign} NOT NULL auto_increment,
                `version` varchar(15) NOT NULL,
                `enforced` int(2) unsigned NOT NULL,
                `proxied` int(2) unsigned NOT NULL,
                `strict` int(2) unsigned NOT NULL,
                `debug` int(2) unsigned NOT NULL,
                `jit` int(2) unsigned NOT NULL,
                `saml_sp_certificate` text NOT NULL,
                `saml_sp_certificate_key` text NOT NULL,
                `saml_sp_nameid_format` varchar(128) NOT NULL,
                `saml_idp_entity_id` varchar(128)  NOT NULL,
                `saml_idp_single_sign_on_service` varchar(128) NOT NULL,
                `saml_idp_single_logout_service` varchar(128) NOT NULL,
                `saml_idp_certificate` text NOT NULL,
                `requested_authn_context` text NOT NULL,
                `requested_authn_context_comparison` varchar(25) NOT NULL,
                `saml_security_nameidencrypted` int(2) unsigned NOT NULL,
                `saml_security_authnrequestssigned` int(2) unsigned NOT NULL,
                `saml_security_logoutrequestsigned` int(2) unsigned NOT NULL,
                `saml_security_logoutresponsesigned` int(2) unsigned NOT NULL,
                `saml_configuration_name` varchar(50) NOT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;
            SQL;
            $DB->query($query) or die($DB->error());
        }
        
        if ($DB->tableExists($table)) {
            // insert default config;
            $query = <<<SQL
            INSERT INTO `$table`
                (`id`,
                `version`,
                `enforced`,
                `proxied`,
                `strict`,
                `debug`,
                `jit`,
                `saml_sp_certificate`,
                `saml_sp_certificate_key`,
                `saml_sp_nameid_format`,
                `saml_idp_entity_id`,
                `saml_idp_single_sign_on_service`,
                `saml_idp_single_logout_service`,
                `saml_idp_certificate`,
                `requested_authn_context`,
                `requested_authn_context_comparison`,
                `saml_security_nameidencrypted`,
                `saml_security_authnrequestssigned`,
                `saml_security_logoutrequestsigned`,
                `saml_security_logoutresponsesigned`,
                `saml_configuration_name`)
            VALUES('1',
                '$version',
                '0',
                '0',
                '1',
                '0',
                '0',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '0',
                '0',
                '0',
                '0',
                'default');
            SQL;
            $DB->query($query) or die($DB->error());        // Die will leave the plugin in an unusable and terrible state.
        }
            // Migration
            //$migration->changeField($table, 'OLDFIELD', 'NEWFIELD', 'DATATYPE', ['null' => false, 'value' => '1']);
            //$migration->migrationOneTable($table);
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
