<?php
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
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
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
);
