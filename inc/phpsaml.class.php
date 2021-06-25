<?php

class PluginPhpsamlPhpsaml
{

    const SESSION_GLPI_NAME_ACCESSOR = 'glpiname';
    const SESSION_VALID_ID_ACCESSOR = 'valid_id';
	
	static private $init = false; 
	static private $docsPath = GLPI_PLUGIN_DOC_DIR.'/phpsaml/';
	static public $auth;
	static public $phpsamlsettings;
	static public $nameid;
	static public $userdata;
	static public $nameidformat;
	static public $sessionindex;
	static public $rightname = 'plugin_phpsaml_phpsaml';
	


	/**
     * Constructor
    **/
	function __construct() {
		self::init();
	}
	
	public static function init() 
	{
		if (!self::$init) {
			require_once('libs.php');
			//require_once(GLPI_ROOT .'/plugins/phpsaml/lib/php-saml/settings.php');
		
			self::$phpsamlsettings = self::getSettings();
			self::$init = true; 
		}
	}
	
	public static function auth(){
		if (!self::$auth){
			self::$auth = new OneLogin\Saml2\Auth(self::$phpsamlsettings);
		}
	}

	
    /**
     * @return bool
     */
    static public function isUserAuthenticated()
    {
        if (version_compare(GLPI_VERSION, '0.85', 'lt') && version_compare(GLPI_VERSION, '0.84', 'gt')) {
            return isset($_SESSION[self::SESSION_GLPI_NAME_ACCESSOR]);
        } else {
            return isset($_SESSION[self::SESSION_GLPI_NAME_ACCESSOR])
            && isset($_SESSION[self::SESSION_VALID_ID_ACCESSOR])
            && $_SESSION[self::SESSION_VALID_ID_ACCESSOR] === session_id();
        }
    }
	
	static public function glpiLogin($relayState = null)
    {
        $auth = new PluginPhpsamlAuth();
		
		if($auth->loadUserData(self::$nameid) && $auth->checkUserData()){
			Session::init($auth);
			self::redirectToMainPage($relayState);
			return;
		}
		
		$error = "User or NameID not found";
		Toolbox::logInFile("php-errors", $error . "\n", true);
		throw new Exception($error);
		sloRequest();
		
    }
	
	static public function glpiLogout()
	{
		$valid_id = $_SESSION['valid_id'];
		$cookie_key = array_search($valid_id, $_COOKIE);
		
		Session::destroy();
		
		//Remove cookie to allow new login
		$cookie_path = ini_get('session.cookie_path');
		
		if (isset($_COOKIE[$cookie_key])) {
		   setcookie($cookie_key, '', time() - 3600, $cookie_path);
		   unset($_COOKIE[$cookie_key]);
		}
	}
	
	static public function ssoRequest($redirect)
	{
		global $CFG_GLPI;
		
		try {
			self::auth();
			self::$auth->login($returnTo = $redirect);
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
	
	static public function sloRequest()
	{
		global $CFG_GLPI;
		
		$returnTo 		= null;
		$parameters 	= array();
		$nameId 		= null;
		$sessionIndex 	= null;
		$nameIdFormat 	= null;
		
		if (isset(self::$nameid)) {
			$nameId = self::$nameid;
		}
		if (isset(self::$sessionindex)) {
			$sessionIndex = self::$sessionindex;
		}
		if (isset(self::$nameidformat)) {
			$nameIdFormat = self::$nameidformat;
		}

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
	
    static public function redirectToMainPage($relayState = null)
    {
        global $CFG_GLPI;
        $REDIRECT = "";
        $destinationUrl = $CFG_GLPI['url_base'];

        if ($relayState) {
            $REDIRECT = "?redirect=" . rawurlencode($relayState);
        }

        if (isset($_SESSION["glpiactiveprofile"])) {
            if ($_SESSION["glpiactiveprofile"]["interface"] == "helpdesk") {
                if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']
                    && empty($REDIRECT)
                ) {
                    $destinationUrl .= "/front/helpdesk.public.php?create_ticket=1";
                } else {
                    $destinationUrl .= "/front/helpdesk.public.php$REDIRECT";
                }

            } else {
                if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']
                    && empty($REDIRECT)
                ) {
                    $destinationUrl .= "/front/ticket.form.php";
                } else {
                    $destinationUrl .= "/front/central.php$REDIRECT";
                }
            }
        }

        header("Location: " . $destinationUrl);
    }
	
	static public function getSettings(){
		global $CFG_GLPI;
		$glpiUrl = $CFG_GLPI['url_base'];

		$phpsamlconf = new PluginPhpsamlConfig();
		$config = $phpsamlconf->getConfig();
		
		

		$phpsamlsettings = array (
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
				//'nameIdEncrypted' => false,

				// Indicates whether the <samlp:AuthnRequest> messages sent by this SP
				// will be signed.              [The Metadata of the SP will offer this info]
				//'authnRequestsSigned' => true,

				// Indicates whether the <samlp:logoutRequest> messages sent by this SP
				// will be signed.
				//'logoutRequestSigned' => true,

				// Indicates whether the <samlp:logoutResponse> messages sent by this SP
				// will be signed.
				//'logoutResponseSigned' =>true,

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
		return $phpsamlsettings;
	}
	
	static public function getAuthn($value){
		if (!isset($value) || $value == ''){
			return false;
		}
		
		$array = explode(',', $value);
		$output = array();
		foreach ($array as $item){
			switch($item){
				case 'PasswordProtectedTransport':
					$output[] = 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport';
					break;
				case 'Password':
					$output[] = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Password';
					break;
				case 'X509':
					$output[] = 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509';
					break;
			}
		}
		return $output;
	}
}
