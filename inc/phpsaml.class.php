<?php

class PluginPhpsamlPhpsaml
{
	/**
     * Constants
    **/
    const SESSION_GLPI_NAME_ACCESSOR= 'glpiname';
    const SESSION_VALID_ID_ACCESSOR = 'valid_id';
	const SCHEMA_NAME 				= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name';
	const SCHEMA_SURNAME 			= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';
	const SCHEMA_FIRSTNAME 			= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/firstname';
	const SCHEMA_GIVENNAME 			= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';
	const SCHEMA_EMAILADDRESS 		= 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress';
	

	// Reversed keyword order to comply with PSR-2.
	// TODO: We should not make all of them publicly available.
	public static  $auth;
	public static  $phpsamlsettings;
	public static  $nameid;
	public static  $userdata;
	public static  $nameidformat;
	public static  $sessionindex;
	public static  $rightname = 'plugin_phpsaml_phpsaml';
	private static  $init = false;
	

	/**
     * Constructor
    **/
	public function __construct()
	{
		self::init();
	}
	

	/**
     * @return bool
     */
	public static function init()
	{

		if (!self::$init) {
			require_once('libs.php');

			self::$phpsamlsettings = self::getSettings();
			self::$nameid 		= (!empty($_SESSION['plugin_phpsaml_nameid'])) 		 ? $_SESSION['plugin_phpsaml_nameid'] 		: null;
			self::$nameidformat = (!empty($_SESSION['plugin_phpsaml_nameidformat'])) ? $_SESSION['plugin_phpsaml_nameidformat'] : null;
			self::$sessionindex = (!empty($_SESSION['plugin_phpsaml_sessionindex'])) ? $_SESSION['plugin_phpsaml_sessionindex'] : null;
			self::$init = true;
		}
	}
	

	/**
     * @return bool
     */
	public static function auth()
	{
		if (!self::$auth) {
			self::$auth = new OneLogin\Saml2\Auth(self::$phpsamlsettings);
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
     */
	public static function glpiLogin($relayState = null) : void
    {
		$phpsamlconf 	= new PluginPhpsamlConfig();
        $auth 			= new PluginPhpsamlAuth();
		$config 		= $phpsamlconf->getConfig();
		
		// Perform login
		if ($auth->loadUserData(self::$nameid) && $auth->checkUserData()) {
			Session::init($auth);
			self::redirectToMainPage($relayState);
		} else {
			// JIT Provisioning added version 1.1.3
			if (isset($config['jit']) && $config['jit'] == 1) {
				self::performJit($relayState);
			} else {
				$error = "User or NameID not found.  Enable JIT Provisioning or manually create the user account";
				Toolbox::logInFile("php-errors", $error . "\n", true);
			}
			
			throw new Exception($error);
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
			if ((!empty(self::$userdata[self::SCHEMA_NAME][0])) && (!empty(self::$userdata[self::SCHEMA_EMAILADDRESS][0]))){
				
				// Generate a random password
				$password = bin2hex(random_bytes(20));

				// figure out what schema to use;
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
					self::redirectToMainPage($relayState);
				}
			} else {
				$error = "JIT Error: Unable to create user because missing claims (emailaddress)";
				Toolbox::logInFile("php-errors", $error . "\n", true);
			}
		} else {
			$error = "JIT Error: Unable to create user because the email address already exists";
			Toolbox::logInFile("php-errors", $error . "\n", true);
		}
	}

	/**
     * @return bool
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
		$parameters 	= array();
		$nameId 		= null;
		$sessionIndex 	= null;
		$nameIdFormat 	= null;
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
	
	public static function getSettings()
	{
		global $CFG_GLPI;
		$glpiUrl = $CFG_GLPI['url_base'];

		$phpsamlconf = new PluginPhpsamlConfig();
		$config = $phpsamlconf->getConfig();
		
		// This array is messy and very hard to read.
		return array (
			// If 'strict' is True, then the PHP Toolkit will reject unsigned
			// or unencrypted messages if it expects them signed or encrypted
			// Also will reject the messages if not strictly follow the SAML
			// standard: Destination, NameId, Conditions ... are validated too.
			'strict' => (isset($config['strict']) && $config['strict'] == 1 ? true : false),

			// Enable debug mode (to print errors)
			'debug' => (isset($config['debug']) && $config['debug'] == 1 ? true : false),

			// Set a BaseURL to be used instead of try to guess
			// the BaseURL of the view that process the SAML Message.
			// Ex. http://sp.example.com/
			//     http://example.com/sp/
			'baseurl' => null,

			// Service Provider Data that we are deploying
			'sp' => array (
				// Identifier of the SP entity  (must be a URI)
				'entityId' => $glpiUrl.'/',
				// Specifies info about where and how the <AuthnResponse> message MUST be
				// returned to the requester, in this case our SP.
				'assertionConsumerService' => array (
					// URL Location where the <Response> from the IdP will be returned
					'url' => $glpiUrl. "/plugins/phpsaml/front/acs.php",
				),
				// If you need to specify requested attributes, set a
				// attributeConsumingService. nameFormat, attributeValue and
				// friendlyName can be omitted. Otherwise remove this section.
				
				// Specifies info about where and how the <Logout Response> message MUST be
				// returned to the requester, in this case our SP.
				'singleLogoutService' => array (
					// URL Location where the <Response> from the IdP will be returned
					'url' => $glpiUrl ."/plugins/phpsaml/front/slo.php",
				),
				'x509cert' => (isset($config['saml_sp_certificate']) ? $config['saml_sp_certificate'] : ''),
				'privateKey' => (isset($config['saml_sp_certificate_key']) ? $config['saml_sp_certificate_key'] : ''),
				// Specifies constraints on the name identifier to be used to
				// represent the requested subject.
				// Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
				'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:'.(isset($config['saml_sp_nameid_format']) ? $config['saml_sp_nameid_format'] : 'unspecified'),
			),

			// Identity Provider Data that we want connect with our SP
			'idp' => array (
				// Identifier of the IdP entity  (must be a URI)
				'entityId' => (isset($config['saml_idp_entity_id']) ? $config['saml_idp_entity_id'] : ''),
				// SSO endpoint info of the IdP. (Authentication Request protocol)
				'singleSignOnService' => array (
					// URL Target of the IdP where the SP will send the Authentication Request Message
					'url' => (isset($config['saml_idp_single_sign_on_service']) ? $config['saml_idp_single_sign_on_service'] : ''),
				),
				// SLO endpoint info of the IdP.
				'singleLogoutService' => array (
					// URL Location of the IdP where the SP will send the SLO Request
					'url' => (isset($config['saml_idp_single_logout_service']) ? $config['saml_idp_single_logout_service'] : ''),
				),
				// Public x509 certificate of the IdP
				'x509cert' => (isset($config['saml_idp_certificate']) ? $config['saml_idp_certificate'] : ''),
				
			),
			// Compression settings
			// Handle if the getRequest/getResponse methods will return the Request/Response deflated.
			// But if we provide a $deflate boolean parameter to the getRequest or getResponse
			// method it will have priority over the compression settings.
			'compress' => array (
				'requests' => true,
				'responses' => true
			),

			// Security settings
			'security' => array (

				/** signatures and encryptions offered */

				// Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
				// will be encrypted.
				'nameIdEncrypted' => (isset($config['saml_security_nameidencrypted']) && $config['saml_security_nameidencrypted'] == 1 ? true : false),

				// Indicates whether the <samlp:AuthnRequest> messages sent by this SP
				// will be signed.              [The Metadata of the SP will offer this info]
				'authnRequestsSigned' => (isset($config['saml_security_authnrequestssigned']) && $config['saml_security_authnrequestssigned'] == 1 ? true : false),

				// Indicates whether the <samlp:logoutRequest> messages sent by this SP
				// will be signed.
				'logoutRequestSigned' => (isset($config['saml_security_logoutrequestsigned']) && $config['saml_security_logoutrequestsigned'] == 1 ? true : false),

				// Indicates whether the <samlp:logoutResponse> messages sent by this SP
				// will be signed.
				'logoutResponseSigned' => (isset($config['saml_security_logoutresponsesigned']) && $config['saml_security_logoutresponsesigned'] == 1 ? true : false),

				/* Sign the Metadata
				 False || True (use sp certs) || array (
															keyFileName => 'metadata.key',
															certFileName => 'metadata.crt'
														)
				*/
				//'signMetadata' => false,


				/** signatures and encryptions required **/

				// Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
				// <samlp:LogoutResponse> elements received by this SP to be signed.
				//'wantMessagesSigned' => false,

				// Indicates a requirement for the <saml:Assertion> elements received by
				// this SP to be encrypted.
				//'wantAssertionsEncrypted' => false,

				// Indicates a requirement for the <saml:Assertion> elements received by
				// this SP to be signed.        [The Metadata of the SP will offer this info]
				//'wantAssertionsSigned' => false,

				// Indicates a requirement for the NameID element on the SAMLResponse received
				// by this SP to be present.
				//'wantNameId' => true,

				// Indicates a requirement for the NameID received by
				// this SP to be encrypted.
				//'wantNameIdEncrypted' => false,

				// Authentication context.
				// Set to false and no AuthContext will be sent in the AuthNRequest,
				// Set true or don't present this parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
				// Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
				'requestedAuthnContext' => self::getAuthn($config["requested_authn_context"]),

				// Allows the authn comparison parameter to be set, defaults to 'exact' if
				// the setting is not present.
				'requestedAuthnContextComparison' => (isset($config["requested_authn_context_comparison"]) ? $config["requested_authn_context_comparison"] : 'exact'),

				// Indicates if the SP will validate all received xmls.
				// (In order to validate the xml, 'strict' and 'wantXMLValidation' must be true).
				'wantXMLValidation' => true,

				// If true, SAMLResponses with an empty value at its Destination
				// attribute will not be rejected for this fact.
				'relaxDestinationValidation' => false,

				// Algorithm that the toolkit will use on signing process. Options:
				//    'http://www.w3.org/2000/09/xmldsig#rsa-sha1'
				//    'http://www.w3.org/2000/09/xmldsig#dsa-sha1'
				//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
				//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384'
				//    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512'
				// Notice that sha1 is a deprecated algorithm and should not be used
				'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
				//'signatureAlgorithm' => XMLSecurityKey::RSA_SHA256,

				// Algorithm that the toolkit will use on digest process. Options:
				//    'http://www.w3.org/2000/09/xmldsig#sha1'
				//    'http://www.w3.org/2001/04/xmlenc#sha256'
				//    'http://www.w3.org/2001/04/xmldsig-more#sha384'
				//    'http://www.w3.org/2001/04/xmlenc#sha512'
				// Notice that sha1 is a deprecated algorithm and should not be used
				'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',

				// ADFS URL-Encodes SAML data as lowercase, and the toolkit by default uses
				// uppercase. Turn it True for ADFS compatibility on signature verification
				'lowercaseUrlencoding' => true,
			),
		);
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
