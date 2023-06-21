<?php /*


 Unpacked SamlResponse Methods:
Array
(
    [0] => __construct
    [1] => isValid
    [2] => getId
    [3] => getAssertionId
    [4] => getAssertionNotOnOrAfter
    [5] => checkStatus
    [6] => checkOneCondition
    [7] => checkOneAuthnStatement
    [8] => getAudiences
    [9] => getIssuers
    [10] => getNameIdData
    [11] => getNameId
    [12] => getNameIdFormat
    [13] => getNameIdNameQualifier
    [14] => getNameIdSPNameQualifier
    [15] => getSessionNotOnOrAfter
    [16] => getSessionIndex
    [17] => getAttributes
    [18] => getAttributesWithFriendlyName
    [19] => validateNumAssertions
    [20] => processSignedElements
    [21] => validateTimestamps
    [22] => validateSignedElements
    [23] => getErrorException
    [24] => getError
    [25] => getXMLDocument
)


 Unpacked SamlResponse vars:
Array
(
    [response] => <samlp:Response ID="_153a6044-7238-4332-a492-bf2594a69402" Version="2.0" IssueInstant="2023-06-21T21:43:30.805Z" Destination="https://mc.trippie.fun/glpi/plugins/phpsaml/front/acs.php" InResponseTo="ONELOGIN_ee5435f1ca13e34e2e0a884287324f9caa2fb98d" xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"><Issuer xmlns="urn:oasis:names:tc:SAML:2.0:assertion">https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/</Issuer><samlp:Status><samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/></samlp:Status><Assertion ID="_80a1f769-3a6f-4a2c-aeb0-22e86d2e0100" IssueInstant="2023-06-21T21:43:30.799Z" Version="2.0" xmlns="urn:oasis:names:tc:SAML:2.0:assertion"><Issuer>https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/</Issuer><Signature xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/><SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/><Reference URI="#_80a1f769-3a6f-4a2c-aeb0-22e86d2e0100"><Transforms><Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/><Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></Transforms><DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/><DigestValue>SYqtXuyrP4fYRX3VyfynXPuMdkBwGTiU21ZrvqrGGYg=</DigestValue></Reference></SignedInfo><SignatureValue>cYDwZtO89ABSgYpob2ywgWSg/p32FKhey938Q1MzEfDcCu4VG657slNLl0lrFFtOzRr33nqPOMW6l3PIP9tKC1Ut11lotWy3byBJm0L1Ozw5QDuBvAnGzt6G2JMDltc531PClmIaobdp0nPRcLZZdeVD7ECw27qi6pjwPgpdrs9nnUJNWQLMaVVIZ+CBwrmfrLVVhH35FBuqMT9/Ye/AwvxCo/K5WRebTmHm1+TRNJuGGRDYNVoGv6hak8mHkGrSeAuaNg0XWNMCT6K2RvaWRCZ3mVoGT/IyPb/5qqsWJaus1A31ZBGqqH69KI7jt7gGjpES9JI+UsoJG95PbUDdyA==</SignatureValue><KeyInfo><X509Data><X509Certificate>MIIC8DCCAdigAwIBAgIQY4aUyUa4fqtLXpe7AXzNzzANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMzA0MjkxMjQ3NDJaFw0yNjA0MjkxMjQ3NDRaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0WUM6By3pNWAOrrrKMUcgBBuwfDzv7/EKWAdckBmxQhWjA0a1ZxC7YUJjSbR/u+bQSlGeJmv++edU2LjkGmlhnsgX9GBE1JseSN1rXos8fWUzIAP3eshS/xI8tAe4b07KQPgk010bEyfrbw+rzDlQL42pu1Roow7cT5cmoCKMdPoKVx+qJjwWNgJyvhUk4S5aiEeaQ1S/bAnQLH9wxoPwetiATZBJOmbC8viutGJ1f41nhRkEVbW0gtQqhgp9uH40abcvmjiggBOCQNCUOJqp8kVZgNjlUCuelK86bm4FdY+zzkZRTc6rj9ZEWq3DF3wXeTUH9kwfVfSFJSJoKKZkQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQCGngFzmLIOp51pf/u5Dl86myzjXyzd2DnD4iAcxsz+zXFnfeXuzWzNQzdpZZx4TOHBhpAYRcHzXrEJg8+/r12i3EEo5BQ8j5Z57vSYH5eIg2At2kjYk2h4lFZt4qlixN3Hj2bWcy3NSaxhPuk3Q39fFeBFYOFMipKhjr8FHdz6dKyEtaPlzTxti7fbn2oAHBSj0PZhUcby1sIpfCMay/VMxBfrLtzrdYxoWcOeBaLXw15J20BAQmZZ7VwXuqGEctOrXxyiRP+diJBc0oCllwbHPi6FYQpzSkjxALb+PktPgt9cTzS7yXwcknlLWmzNDukMjWoTj952biQ1JEVWE73x</X509Certificate></X509Data></KeyInfo></Signature><Subject><NameID Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">chris.gralike@flevo-scouts.nl</NameID><SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer"><SubjectConfirmationData InResponseTo="ONELOGIN_ee5435f1ca13e34e2e0a884287324f9caa2fb98d" NotOnOrAfter="2023-06-21T22:43:30.648Z" Recipient="https://mc.trippie.fun/glpi/plugins/phpsaml/front/acs.php"/></SubjectConfirmation></Subject><Conditions NotBefore="2023-06-21T21:38:30.648Z" NotOnOrAfter="2023-06-21T22:43:30.648Z"><AudienceRestriction><Audience>https://mc.trippie.fun/glpi/</Audience></AudienceRestriction></Conditions><AttributeStatement><Attribute Name="http://schemas.microsoft.com/identity/claims/tenantid"><AttributeValue>3b530360-95d8-439e-bd69-3a8c331a2358</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/identity/claims/objectidentifier"><AttributeValue>58c25608-50c5-4ff7-bf60-bab58b65ade2</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/identity/claims/displayname"><AttributeValue>Chris Gralike | Flevo-Scouts</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/identity/claims/identityprovider"><AttributeValue>https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/claims/authnmethodsreferences"><AttributeValue>http://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/password</AttributeValue><AttributeValue>http://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/x509</AttributeValue><AttributeValue>http://schemas.microsoft.com/claims/multipleauthn</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname"><AttributeValue>Chris</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname"><AttributeValue>Gralike</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"><AttributeValue>chris.gralike@flevo-scouts.nl</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"><AttributeValue>chris.gralike@flevo-scouts.nl</AttributeValue></Attribute></AttributeStatement><AuthnStatement AuthnInstant="2023-04-01T19:08:08.039Z" SessionIndex="_80a1f769-3a6f-4a2c-aeb0-22e86d2e0100"><AuthnContext><AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Password</AuthnContextClassRef></AuthnContext></AuthnStatement></Assertion></samlp:Response>
    [document] => DOMDocument Object
        (
            [config] => 
            [doctype] => 
            [implementation] => (object value omitted)
            [documentElement] => (object value omitted)
            [actualEncoding] => 
            [encoding] => 
            [xmlEncoding] => 
            [standalone] => 1
            [xmlStandalone] => 1
            [version] => 1.0
            [xmlVersion] => 1.0
            [strictErrorChecking] => 1
            [documentURI] => /var/www/html/glpi/plugins/phpsaml/front/
            [formatOutput] => 
            [validateOnParse] => 
            [resolveExternals] => 
            [preserveWhiteSpace] => 1
            [recover] => 
            [substituteEntities] => 
            [firstElementChild] => (object value omitted)
            [lastElementChild] => (object value omitted)
            [childElementCount] => 1
            [nodeName] => #document
            [nodeValue] => 
            [nodeType] => 9
            [parentNode] => 
            [childNodes] => (object value omitted)
            [firstChild] => (object value omitted)
            [lastChild] => (object value omitted)
            [previousSibling] => 
            [nextSibling] => 
            [attributes] => 
            [ownerDocument] => 
            [namespaceURI] => 
            [prefix] => 
            [localName] => 
            [baseURI] => /var/www/html/glpi/plugins/phpsaml/front/
            [textContent] => https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/SYqtXuyrP4fYRX3VyfynXPuMdkBwGTiU21ZrvqrGGYg=cYDwZtO89ABSgYpob2ywgWSg/p32FKhey938Q1MzEfDcCu4VG657slNLl0lrFFtOzRr33nqPOMW6l3PIP9tKC1Ut11lotWy3byBJm0L1Ozw5QDuBvAnGzt6G2JMDltc531PClmIaobdp0nPRcLZZdeVD7ECw27qi6pjwPgpdrs9nnUJNWQLMaVVIZ+CBwrmfrLVVhH35FBuqMT9/Ye/AwvxCo/K5WRebTmHm1+TRNJuGGRDYNVoGv6hak8mHkGrSeAuaNg0XWNMCT6K2RvaWRCZ3mVoGT/IyPb/5qqsWJaus1A31ZBGqqH69KI7jt7gGjpES9JI+UsoJG95PbUDdyA==MIIC8DCCAdigAwIBAgIQY4aUyUa4fqtLXpe7AXzNzzANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMzA0MjkxMjQ3NDJaFw0yNjA0MjkxMjQ3NDRaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0WUM6By3pNWAOrrrKMUcgBBuwfDzv7/EKWAdckBmxQhWjA0a1ZxC7YUJjSbR/u+bQSlGeJmv++edU2LjkGmlhnsgX9GBE1JseSN1rXos8fWUzIAP3eshS/xI8tAe4b07KQPgk010bEyfrbw+rzDlQL42pu1Roow7cT5cmoCKMdPoKVx+qJjwWNgJyvhUk4S5aiEeaQ1S/bAnQLH9wxoPwetiATZBJOmbC8viutGJ1f41nhRkEVbW0gtQqhgp9uH40abcvmjiggBOCQNCUOJqp8kVZgNjlUCuelK86bm4FdY+zzkZRTc6rj9ZEWq3DF3wXeTUH9kwfVfSFJSJoKKZkQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQCGngFzmLIOp51pf/u5Dl86myzjXyzd2DnD4iAcxsz+zXFnfeXuzWzNQzdpZZx4TOHBhpAYRcHzXrEJg8+/r12i3EEo5BQ8j5Z57vSYH5eIg2At2kjYk2h4lFZt4qlixN3Hj2bWcy3NSaxhPuk3Q39fFeBFYOFMipKhjr8FHdz6dKyEtaPlzTxti7fbn2oAHBSj0PZhUcby1sIpfCMay/VMxBfrLtzrdYxoWcOeBaLXw15J20BAQmZZ7VwXuqGEctOrXxyiRP+diJBc0oCllwbHPi6FYQpzSkjxALb+PktPgt9cTzS7yXwcknlLWmzNDukMjWoTj952biQ1JEVWE73xchris.gralike@flevo-scouts.nlhttps://mc.trippie.fun/glpi/3b530360-95d8-439e-bd69-3a8c331a235858c25608-50c5-4ff7-bf60-bab58b65ade2Chris Gralike | Flevo-Scoutshttps://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/http://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/passwordhttp://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/x509http://schemas.microsoft.com/claims/multipleauthnChrisGralikechris.gralike@flevo-scouts.nlchris.gralike@flevo-scouts.nlurn:oasis:names:tc:SAML:2.0:ac:classes:Password
        )

    [decryptedDocument] => 
    [encrypted] => 
)


 POST:


 GET:
Array
(
)
