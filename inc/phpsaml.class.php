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
 *  @author    	Derrick Smith
 *  @author	   	Chris Gralike
 *  @copyright 	Copyright (c) 2018 by Derrick Smith
 *  @license   	AGPL License 3.0 or (at your option) any later version
 *             	http://www.gnu.org/licenses/agpl-3.0-standalone.html
 *  @since     	0.1
 * ------------------------------------------------------------------------
 **/

class PluginPhpsamlPhpsaml
{
	// CLASS CONSTANTS
    public const SESSION_GLPI_NAME_ACCESSOR	= 'glpiname';
    public const SESSION_VALID_ID_ACCESSOR 	= 'valid_id';

	// Excluded local files
	public const EXCLUDED_FILES				= ['cron.php',
									   		   'ldap_mass_sync.php',
									   		   'apirest.php',
									   		   'acs.php'];

	// Excluded useragents
	// https://github.com/derricksmith/phpsaml/issues/134
	// "POST / HTTP/1.1" 200 473 "-" "GLPI-Agent_v1.5-gitf0e44092"
	// "POST / HTTP/1.1" 200 330 "-" "GLPI-Injector_v1.5-gitf0e44092"
	private const EXCLUDED_USERAGENTS		= ['FusionInventory-Agent'	=> '/plugins/fusioninventory/',
											   'FusionInventory-Agent' => '/marketplace/fusioninventory/',
											   'GLPI-Agent'	=>	'/',
											   'GLPI-Injector' => '/'];

	// https://docs.oasis-open.org/security/saml/v2.0/saml-bindings-2.0-os.pdf
	private const SCHEMA_NAME 				= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
	private const SCHEMA_SURNAME 			= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
	private const SCHEMA_FIRSTNAME 			= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/firstname';
	private const SCHEMA_GIVENNAME 			= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';
	private const SCHEMA_EMAILADDRESS 		= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress';
	
	// CLASS PROPERTIES
	// Do we need all these class properties to keep their values (static) after exec?
	/**
     * Holds the OneLogin\Saml\Auth object
	 *
	 * @var 	object 	(OneLogin\Saml2\Auth)
	 * @link 			Also called statically in /front/acs.php
     **/
	public static $auth;

	/**
     * Holds the populated OneLogin\Saml\auth(configuration) array
	 * populated in $this->init() as a return value
	 *
     * @var 	array
	 * @access 	public
	 * @link 			Referenced statically by front\acs.php
	 * @todo 			Write a getter for the OneLogin configuration instead of directly accessing the class property.
	 * @todo 			Make getSettings() populate the class property directly instead of passing it as a return value to $this->init().
     **/
	public static $phpsamlsettings;

	/**
     * Holds the (if allready) authenticated user reference provided by idp after succesfull authentication.
	 *
     * @var 	string
	 * @access 	public
	 * @link 			Referenced and populated by phpsaml\front\acs.php using the samlResponse->getNameId() method;
	 * @link			References by phpsaml\setup.php function plugin_init_session_phpsaml() hooked to the GLPI init_session event_hook;
	 * @see				setup.php some $_SESSION declarations dont seem to be used anywhere, are they required?
	 * @todo			External direct access to the property should not be allowed, we should use getters and setters;
     **/
	public static $nameid;

	/**
     * Holds the idp provided user Attributes populated by phpsaml\front\acs.php using the samlResponse->getAttributes() method.
	 *
     * @var 	array
	 * @access 	public
	 * @link 			Referenced and populated by phpsaml\front\acs.php using the samlResponse->getAttributes() method;
	 * @link			Referenced locally to populate JIT function;
	 * @todo			External direct access to the property should not be allowed, we should use getters and setters;
     **/
	public static $userdata;

	/**
     * Holds the nameidformat populated by phpsaml\front\acs.php with the nameIdFormat configured in the $this->$auth (\OneLogin\Saml\Auth).
	 *
     * @var 	string
	 * @access 	public
	 * @link 			Referenced and populated by phpsaml\front\acs.php using the $phpsaml::$auth->getNameIdFormat() method;
	 * @link			References by phpsaml\setup.php function plugin_init_session_phpsaml hooked to GLPI session_init hook.
	 * @see				setup.php some $_SESSION declarations dont seem to be used anywhere, are they required?
     **/
	public static $nameidformat;

	/**
     * Holds the sessionIndex populated by phpsaml\front\acs.php using the phpsaml::$auth->getAttributes() method.
	 *
     * @var 	string|null
	 * @access 	public
	 * @link 			Referenced and populated by phpsaml\front\acs.php using the $phpsaml::$auth->getSessionIndex() method;
	 * @link			References by phpsaml\setup.php function plugin_init_session_phpsaml hooked to GLPI session_init hook.
	 * @see				setup.php some $_SESSION declarations dont seem to be used anywhere, are they required?
     **/
	public static $sessionindex;

	/**
     * Define the user rights required, GLPI plugin property
     * @var		string
     **/
	public static $rightname = 'plugin_phpsaml_phpsaml';

	/**
     * Indicate the object has allready been initialized
	 * @access 	private
     * @var 	bool
     **/
	private static $init = false;
	

	// CLASS METHODS
	/**
     * Not sure why we need everything in this object to be static.
	 * @see https://www.php.net/manual/en/language.variables.scope.php#language.variables.scope.static
    **/
	public function __construct()
	{
		self::init();
	}
	

	/**
     * @return void
     */
	private static function init()
	{
		if (!self::$init) {
			require_once('libs.php');
			self::$phpsamlsettings 	= self::getSettings();
			self::$nameid 			= (!empty($_SESSION['plugin_phpsaml_nameid'])) 		 ? $_SESSION['plugin_phpsaml_nameid'] 		: null;
			self::$nameidformat 	= (!empty($_SESSION['plugin_phpsaml_nameidformat'])) ? $_SESSION['plugin_phpsaml_nameidformat'] : null;
			self::$sessionindex 	= (!empty($_SESSION['plugin_phpsaml_sessionindex'])) ? $_SESSION['plugin_phpsaml_sessionindex'] : null;
			self::$init 			= true;
		}
	}
	

	/**
	 * Initializes the SAML Auth object.
     * @return void
     */
	public static function auth()
	{
		if (!self::$auth) {
			self::$auth = new OneLogin\Saml2\Auth(self::$phpsamlsettings);
		}

		// Set ProxyVars if configured in PHPSAML Config
		// https://github.com/SAML-Toolkits/php-saml#url-guessing-methods
		// https://github.com/derricksmith/phpsaml/issues/127
		$cfgObj		    = new PluginPhpsamlConfig();
		$config 		= $cfgObj->getConfig();
		if ($config[PluginPhpsamlConfig::PROXIED]) {
			$samltoolkit = new OneLogin\Saml2\Utils();
			$samltoolkit::setProxyVars(true);
		}
	}

	 /**
	 * ProcessUserLogin handles the login process
	 * called by the glpi post init;
	 *
     * @return bool
	 * @since 1.2.2
     */
	public function processUserLogin()
	{
		global $CFG_GLPI;

		$cfgObj		    = new PluginPhpsamlConfig();
		$config 		= $cfgObj->getConfig();

		// Return false for fusioninventory agents
		// https://github.com/derricksmith/phpsaml/issues/134
		foreach(self::EXCLUDED_USERAGENTS as $agent => $request){
			if(array_key_exists('HTTP_USER_AGENT', $_SERVER) && array_key_exists('REQUEST_URI', $_SERVER)){
				if(strpos($_SERVER['HTTP_USER_AGENT'], $agent) !== false){
					if(strpos($_SERVER['REQUEST_URI'], $request) !== false) {
						return false;
					}
				}
			}
		}

		// Return true for local files in Excludes constant
		foreach (self::EXCLUDED_FILES as $value) {
			if ((PHP_SAPI === 'cli') || strpos($_SERVER['REQUEST_URI'], $value) !== false) {
				return true;
			}
		}

		// Perform logout if requested.
		if ((strpos($_SERVER['REQUEST_URI'], 'front/logout.php') !== false) &&
			(!empty($config[PluginPhpsamlConfig::SLOURL]))) {

			$_SESSION['noAUTO'] = 1;
			self::sloRequest();
		}

		// Check if the user was authenticated
		if (!self::isUserAuthenticated()) {

			if ((isset($_GET['noAUTO']) && $_GET['noAUTO'] == 1) ||
			    (isset($_SESSION['noAUTO']) && $_SESSION['noAUTO'] == 1)) {
				
				//Make sure the session is cleared.
				self::glpiLogout();
				
				$error = "You have logged out of GLPI but are still logged into your Identity Provider.
						  Select Log in Again to automatically log back into GLPI or close this window.
						  Configure the SAML setting 'Single Logout URI' to perform automatic logout.";
				
				// we have done at least a good login? No, we exit.
				Html::nullHeader("Login", $CFG_GLPI['url_base'] . '/index.php');
				echo '<div class="center b">'.$error.'<br><br>';

				// Logout with noAUto to manage auto_login with errors
				echo '<a href="' . $CFG_GLPI['url_base'] .'/index.php">' .__('Log in again') . '</a></div>';
				Html::nullFooter();

				exit();
			} else {

				// Fix for invalid redirect errors when port number is included in HTTP_HOST.
				// Maybe replace it with GLPI config: URL of the application?
				list($realhost,)=explode(':',$_SERVER['HTTP_HOST']);
				
				/////////////////// Problematic code //////////////////
				// TODO: This needs additional testing in conjunction with the setProxyVars(true) at init!
				// TODO: make this code readable.
				// https://github.com/derricksmith/phpsaml/issues/120
				$returnTo = ((((isset($_GET['redirect']) ? $_GET['redirect'] : isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) ?  $_SERVER['HTTP_X_FORWARDED_PROTO'] : isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http") . "://" . $realhost .  $_SERVER['REQUEST_URI']);

				self::ssoRequest($returnTo);
			}
		}
	}

	
    /**
     * @return bool
     */
    public static function isUserAuthenticated()
    {
        if (version_compare(GLPI_VERSION, '0.85', 'lt') && version_compare(GLPI_VERSION, '0.84', 'gt')) {
            return isset($_SESSION[self::SESSION_GLPI_NAME_ACCESSOR]);
        } else {
            return isset($_SESSION[self::SESSION_GLPI_NAME_ACCESSOR])
            && isset($_SESSION[self::SESSION_VALID_ID_ACCESSOR])
            && $_SESSION[self::SESSION_VALID_ID_ACCESSOR] === session_id();
        }
    }

	/**
	 *
	 * Perform login or JIT
	 *
     * @return bool
	 * @since 1.1.3
	 * 
	 * TODO: Add the ability to change user properties when
	 * fields (other than email) has changed in the saml response 
	 * as suggested by https://github.com/derricksmith/phpsaml/issues/108
	 * 
     */
	public static function glpiLogin($relayState = null) : void
    {
        $auth 			= new PluginPhpsamlAuth();
		$cfgObj			= new PluginPhpsamlConfig();
		$config 		= $cfgObj->getConfig();
		
		// Perform login
		if ($auth->loadUserData(self::$nameid) && $auth->checkUserData()) {
			Session::init($auth);
			Session::addMessageAfterRedirect(__("SAML login succesful"), true, INFO); 
			self::redirectToMainPage($relayState);
		} else {
			// JIT Provisioning added version 1.1.3
			if (isset($config['jit']) && $config['jit'] == 1) {
				self::performJit($relayState);
			} else {
				$error = "User or NameID not found.  Enable JIT Provisioning or manually create the user account";
				Toolbox::logInFile("php-errors", $error . "\n", true);
				Session::addMessageAfterRedirect(__("Login failed: $error"), true, ERROR); 
				self::redirectToMainPage($relayState);
			}
		}
    }


	/**
     * @return bool
     */
	private static function performJit($relayState)
	{
		$user = new User();
		$auth = new PluginPhpsamlAuth();

		if (!$user->getFromDBbyEmail(self::$nameid)){

			// TODO: https://github.com/derricksmith/phpsaml/issues/108 Add better validations on field presence makeing only email mandatory
			// using email as name if no name is present. Add additional optional fields if present in the saml response like phonenumbers etc.
			if ((!empty(self::$userdata[self::SCHEMA_NAME][0])) 
			     && (!empty(self::$userdata[self::SCHEMA_EMAILADDRESS][0]))
				 && (!empty(self::$userdata[self::SCHEMA_SURNAME][0]))
				 && (!empty(self::$userdata[self::SCHEMA_FIRSTNAME][0]) || !empty(self::$userdata[self::SCHEMA_GIVENNAME][0]))) {
				
				// Generate a random password
				$password = bin2hex(random_bytes(20));

				// figure out what claim to use;
				// https://github.com/derricksmith/phpsaml/issues/125
				$nameObj = (isset(self::$userdata[self::SCHEMA_FIRSTNAME][0])) ? self::SCHEMA_FIRSTNAME : self::SCHEMA_GIVENNAME;

				$newUser = new User();
				$input = [
					'name'        => self::$userdata[self::SCHEMA_NAME][0],
					'realname'    => self::$userdata[self::SCHEMA_SURNAME][0],
					'firstname'   => self::$userdata[$nameObj][0],
					'_useremails' => [self::$userdata[self::SCHEMA_EMAILADDRESS][0]],
					'password'    => $password,
					'password2'   => $password];

				$newUser->add($input);

				// Load the rulesEngine and process them
				$phpSamlRuleCollection = new PluginPhpsamlRuleRightCollection();
				$matchInput = ['_useremails' => $input['_useremails']];
				$phpSamlRuleCollection->processAllRules($matchInput, [], []);

				// Retry login with newly created user.
				if ($auth->loadUserData(self::$nameid) && $auth->checkUserData()) {
					Session::init($auth);
					Session::addMessageAfterRedirect(__("SAML Login succesfull"), true, INFO); 
					self::redirectToMainPage($relayState);
				}else{
					Session::addMessageAfterRedirect(__("SAML Login failed using create user, weird!"), true, ERROR); 
					self::redirectToMainPage($relayState);
				}
			} else {
				$error = "JIT Error: Unable to create user because missing claims we got the following to work with:".
						  "\n *schemaname:" . self::$userdata[self::SCHEMA_NAME][0] .
						  "\n *name:" . @self::$userdata[self::SCHEMA_NAME][0] .
						  "\n *realname:" . self::$userdata[self::SCHEMA_SURNAME][0] . 
						  "\n *_useremail:". self::$userdata[self::SCHEMA_EMAILADDRESS][0].
						  "\n password: null by default. Make sure all fields are present!";
				Toolbox::logInFile("php-errors", $error . "\n", true);
				Session::addMessageAfterRedirect(__("Login failed: $error"), true, ERROR); 
				self::redirectToMainPage($relayState);
			}
		} else {
			$error = "JIT Error: Unable to create user because the email address already exists";
			Toolbox::logInFile("php-errors", $error . "\n", true);
			Session::addMessageAfterRedirect(__("Login failed: user email allready exists!"), true, ERROR);                   
			self::redirectToMainPage($relayState);
		}
	}

	/**
     * @return void
     */
	public static function glpiLogout()
	{

		$validId   = $_SESSION['valid_id'];
		$cookieKey = array_search($validId, $_COOKIE);
		
		Session::destroy();
		
		//Remove cookie to allow new login
		$cookiePath = ini_get('session.cookie_path');
		
		if (isset($_COOKIE[$cookieKey])) {
		   setcookie($cookieKey, '', time() - 3600, $cookiePath);
		   unset($_COOKIE[$cookieKey]);
		}
	}
	

	/**
     * @return bool
     */
	public static function ssoRequest($redirect)
	{
		global $CFG_GLPI;
		try {
			self::auth();
			self::$auth->login($redirect);
		} catch (Exception $e) {
			$error = $e->getMessage();
			Toolbox::logInFile("php-errors", $error . "\n", true);
			Html::nullHeader("Login", $CFG_GLPI["url_base"] . '/index.php');
			echo '<div class="center b">'.$error.'<br><br>';
			// Logout with noAuto to manage auto_login with errors
			echo '<a href="' . $CFG_GLPI["url_base"] .'/index.php">' .__('Log in again') . '</a></div>';
			Html::nullFooter();
		}
	}
	

	/**
     * @return bool
     */
	public static function sloRequest()
	{
		global $CFG_GLPI;
		
		$returnTo 		= null;
		$parameters 	= [];
		$nameId 	    = (isset(self::$nameid)) 	   ? self::$nameid 		 : null;
		$sessionIndex   = (isset(self::$sessionindex)) ? self::$sessionindex : null;
		$nameIdFormat   = (isset(self::$nameidformat)) ? self::$nameidformat : null;

		self::glpiLogout();

		if (!empty(self::$phpsamlsettings['idp']['singleLogoutService'])){
			try {
				self::auth();
				self::$auth->logout($returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat);
			} catch (Exception $e) {
				$error = $e->getMessage();
				Toolbox::logInFile("php-errors", $error . "\n", true);
				
				Html::nullHeader("Login", $CFG_GLPI["url_base"] . '/index.php');
				echo '<div class="center b">'.$error.'<br><br>';
				// Logout whit noAUto to manage auto_login with errors
				echo '<a href="' . $CFG_GLPI["url_base"] .'/index.php">' .__('Log in again') . '</a></div>';
				Html::nullFooter();	
			}
		}
	}
	

	/**
     * @return bool
     */
    public static function redirectToMainPage($relayState = null)
    {
        global $CFG_GLPI;
        $destinationUrl = $CFG_GLPI['url_base'];
		$redirect 		= ($relayState) ? '?redirect=' . rawurlencode($relayState) : null;

        if (isset($_SESSION["glpiactiveprofile"])) {
            if ($_SESSION["glpiactiveprofile"]["interface"] == "helpdesk") {
                if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']
                    && empty($redirect)
                ) {
                    $destinationUrl .= "/front/helpdesk.public.php?create_ticket=1";
                } else {
                    $destinationUrl .= "/front/helpdesk.public.php$redirect";
                }
            } else {
                if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']
                    && empty($redirect)
                ) {
                    $destinationUrl .= "/front/ticket.form.php";
                } else {
                    $destinationUrl .= "/front/central.php$redirect";
                }
            }
        }
        header("Location: " . $destinationUrl);
    }

	 /**
     *
     * Pupulates and returns the configuration array for the PHP-saml library.
     *
     * @return array $config    returns array with php-saml configuration
     * @since                   1.0.0
     * @example					https://github.com/SAML-Toolkits/php-saml/blob/master/settings_example.php
	 * @todo					CG: use getters in the future instead of doing validations here.
	 * @todo 					CG: This method makes more sense inside a separate config class that can then also be used by config form class.
     */
	public static function getSettings()
	{
		global $CFG_GLPI;

		$cfgObj		 = new PluginPhpsamlConfig();
		$config 	 = $cfgObj->getConfig();
		
		// Populate configuration array using phpsaml configuration in database then return the array
		$libSamlConf = [
			'strict' 						=> (isset($config[PluginPhpsamlConfig::STRICT]) && $config[PluginPhpsamlConfig::STRICT] == 1) ? true : false,
			'debug' 						=> (isset($config[PluginPhpsamlConfig::DEBUG])  && $config[PluginPhpsamlConfig::DEBUG]  == 1) ? true : false,
			'baseurl' 						=> null,
			// Service provider configuration
			'sp' 							=> [
				'entityId' 					=> $CFG_GLPI['url_base'].'/',
				'assertionConsumerService' 	=> [
					'url' 					=> $CFG_GLPI['url_base'].PluginPhpsamlConfig::ACSPATH
				],
				'singleLogoutService' 		=> [
					'url' 					=> $CFG_GLPI['url_base'].PluginPhpsamlConfig::SLOPATH
				],
				'x509cert' 					=> (isset($config[PluginPhpsamlConfig::SPCERT])) ? $config[PluginPhpsamlConfig::SPCERT] : '',
				'privateKey' 				=> (isset($config[PluginPhpsamlConfig::SPKEY]))  ? $config[PluginPhpsamlConfig::SPKEY]  : '',
				'NameIDFormat' 				=> 'urn:oasis:names:tc:SAML:1.1:nameid-format:'.(isset($config[PluginPhpsamlConfig::NAMEFM]) ? $config[PluginPhpsamlConfig::NAMEFM] : 'unspecified')
			],
			// Identity Provider configuration to connect with our SP
			'idp' 							=> [
				'entityId' 					=> (isset($config[PluginPhpsamlConfig::ENTITY])) ? $config[PluginPhpsamlConfig::ENTITY] : '',
				'singleSignOnService' 		=> [
					'url' 					=> (isset($config[PluginPhpsamlConfig::SSOURL])) ? $config[PluginPhpsamlConfig::SSOURL] : '',
				],
				'singleLogoutService' 		=> [
					'url' 					=> (isset($config[PluginPhpsamlConfig::SLOURL])) ? $config[PluginPhpsamlConfig::SLOURL] : '',
				],
				'x509cert' 					=> (isset($config[PluginPhpsamlConfig::IPCERT])) ? $config[PluginPhpsamlConfig::IPCERT] : '',
			],
			// Compress requests and responses
			'compress' 						=> [
				'requests' 					=> PluginPhpsamlConfig::CMPREQ,
				'responses' 				=> PluginPhpsamlConfig::CMPRES,
			],
			// Security configuration
			'security' 						=> [
				'nameIdEncrypted' 			=> (isset($config[PluginPhpsamlConfig::ENAME])  && $config[PluginPhpsamlConfig::ENAME]  == 1) ? true : false,  // normalize in PluginPhpsamlConfig::getConfig instead of validate and assign here?
				'authnRequestsSigned' 		=> (isset($config[PluginPhpsamlConfig::SAUTHN]) && $config[PluginPhpsamlConfig::SAUTHN] == 1) ? true : false,  // normalize in PluginPhpsamlConfig::getConfig instead of validate and assign here?
				'logoutRequestSigned' 		=> (isset($config[PluginPhpsamlConfig::SSLORQ]) && $config[PluginPhpsamlConfig::SSLORQ] == 1) ? true : false,  // normalize in PluginPhpsamlConfig::getConfig instead of validate and assign here?
				'logoutResponseSigned' 		=> (isset($config[PluginPhpsamlConfig::SSLORE]) && $config[PluginPhpsamlConfig::SSLORE] == 1) ? true : false,  // normalize in PluginPhpsamlConfig::getConfig instead of validate and assign here?

				//'signMetadata' 			=> false,
				//'wantMessagesSigned' 		=> false,
				//'wantAssertionsEncrypted' => false,
				//'wantAssertionsSigned' 	=> false,
				//'wantNameId' 				=> true,
				//'wantNameIdEncrypted' 	=> false,
				// Set true or don't present this parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
				// Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
				'requestedAuthnContext' 	=> self::getAuthn($config[PluginPhpsamlConfig::AUTHNC]),
				'requestedAuthnContextComparison' => (isset($config[PluginPhpsamlConfig::AUTHND]) ? $config[PluginPhpsamlConfig::AUTHND] : 'exact'),
				'wantXMLValidation' 		=> PluginPhpsamlConfig::XMLVAL,
				'relaxDestinationValidation'=> PluginPhpsamlConfig::DSTVAL,

				// Algorithm that the toolkit will use on signing process. Options:
				//    'http://www.w3.org/2000/09/xmldsig#rsa-sha1'
				//    'http://www.w3.org/2000/09/xmldsig#dsa-sha1'
				//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
				//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384'
				//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512'
				// Notice that sha1 is a deprecated algorithm and should not be used
				'signatureAlgorithm' 		=> 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
				//'signatureAlgorithm' => XMLSecurityKey::RSA_SHA256,

				// Algorithm that the toolkit will use on digest process. Options:
				//    'http://www.w3.org/2000/09/xmldsig#sha1'
				//    'http://www.w3.org/2001/04/xmlenc#sha256'
				//    'http://www.w3.org/2001/04/xmldsig-more#sha384'
				//    'http://www.w3.org/2001/04/xmlenc#sha512'
				// Notice that sha1 is a deprecated algorithm and should not be used
				'digestAlgorithm' 			=> 'http://www.w3.org/2001/04/xmlenc#sha256',
				'lowercaseUrlencoding' 		=> PluginPhpsamlConfig::LOWURL
			]
		];

		// Maybe do some validations in the future on free format fields like URIs
		return $libSamlConf;
	}
	
	public static function getAuthn($value)
	{
		if (preg_match('/^none,.+/i', $value)) {
			$array  = explode(',', $value);
			$output = array();
			foreach ($array as $item) {
				switch ($item) {
					case 'PasswordProtectedTransport':
						$output[] = 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport';
						break;
					case 'Password':
						$output[] = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Password';
						break;
					case 'X509':
						$output[] = 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509';
						break;
					default:
						$output[] = '';
						break;
				}
			}
			return $output;
		} else {
			return false;
		}
	}
}
