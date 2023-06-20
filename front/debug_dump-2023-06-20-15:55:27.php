<?php /*
Array
(
    [SAMLResponse] => PHNhbWxwOlJlc3BvbnNlIElEPSJfMDE4MGQyZjYtMTIxNS00ZTRkLThiNDQtOTVkN2EyZTU5ZTc4IiBWZXJzaW9uPSIyLjAiIElzc3VlSW5zdGFudD0iMjAyMy0wNi0yMFQxNTo1NDo0NC40NDlaIiBEZXN0aW5hdGlvbj0iaHR0cHM6Ly9tYy50cmlwcGllLmZ1bi9nbHBpL3BsdWdpbnMvcGhwc2FtbC9mcm9udC9hY3MucGhwIiBJblJlc3BvbnNlVG89Ik9ORUxPR0lOXzFkY2I1MTA0MzlhNGU2OTVmZjEzZjA3Y2RmZDliZjYxODQ1NTAxZTMiIHhtbG5zOnNhbWxwPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6cHJvdG9jb2wiPjxJc3N1ZXIgeG1sbnM9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphc3NlcnRpb24iPmh0dHBzOi8vc3RzLndpbmRvd3MubmV0LzNiNTMwMzYwLTk1ZDgtNDM5ZS1iZDY5LTNhOGMzMzFhMjM1OC88L0lzc3Vlcj48c2FtbHA6U3RhdHVzPjxzYW1scDpTdGF0dXNDb2RlIFZhbHVlPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6c3RhdHVzOlN1Y2Nlc3MiLz48L3NhbWxwOlN0YXR1cz48QXNzZXJ0aW9uIElEPSJfYjQ5ZDM0ZTgtY2FhNy00OTMxLTg4ZmEtODk2ZmNkYzE1YzAwIiBJc3N1ZUluc3RhbnQ9IjIwMjMtMDYtMjBUMTU6NTQ6NDQuNDQ0WiIgVmVyc2lvbj0iMi4wIiB4bWxucz0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmFzc2VydGlvbiI+PElzc3Vlcj5odHRwczovL3N0cy53aW5kb3dzLm5ldC8zYjUzMDM2MC05NWQ4LTQzOWUtYmQ2OS0zYThjMzMxYTIzNTgvPC9Jc3N1ZXI+PFNpZ25hdHVyZSB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyI+PFNpZ25lZEluZm8+PENhbm9uaWNhbGl6YXRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiLz48U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8wNC94bWxkc2lnLW1vcmUjcnNhLXNoYTI1NiIvPjxSZWZlcmVuY2UgVVJJPSIjX2I0OWQzNGU4LWNhYTctNDkzMS04OGZhLTg5NmZjZGMxNWMwMCI+PFRyYW5zZm9ybXM+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0dXJlIi8+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyIvPjwvVHJhbnNmb3Jtcz48RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8wNC94bWxlbmMjc2hhMjU2Ii8+PERpZ2VzdFZhbHVlPlBwYk5QZnNVdElQd3AyRU1MRE91T0F1QVJiQjNlZTc4QSs3VlI5aGdvV2c9PC9EaWdlc3RWYWx1ZT48L1JlZmVyZW5jZT48L1NpZ25lZEluZm8+PFNpZ25hdHVyZVZhbHVlPjBMMU1xYXQxN09rZloza2R4VjFZTmsxZHR5MmVqU3I2UUp5VEhQNGtGY2t5cDBXaDhYbDJvN3V2bjZ3Tk0wc1BaNFpUV2RvdkNFbzNaSXcrNHBGbGg3KytyeGdPbFZwbEZNSDhoMjNKZU91NU85WUo2dC81UkVHRFJpWE55MHBKTlM3VlBac3BiMS9ucXFldXhuMnVUbmh3K2dLTkk5YkhwSWp5eXk2SWZNME9IOTFvdHpPZ2VLc0ZSRFk3T1F5L0pWWTRYUTd3VGU4dk1TUmtCRnRud1dxa3llZUh5YnlkY05GS1pDeXFCeVg2b0RESFIrelMwU0Z6Tm43Y3crekpLY2hCOXJyNUlCMG1IUEZ4RDhPaTVOR0QyM0hTQnUrUUVjQk1zRDB5TWxwNjErQzNlNFBRZGZsUXVjMllWQ1EyZ3hMTys5enNjck5TNFZieGRudEROdz09PC9TaWduYXR1cmVWYWx1ZT48S2V5SW5mbz48WDUwOURhdGE+PFg1MDlDZXJ0aWZpY2F0ZT5NSUlDOERDQ0FkaWdBd0lCQWdJUVk0YVV5VWE0ZnF0TFhwZTdBWHpOenpBTkJna3Foa2lHOXcwQkFRc0ZBREEwTVRJd01BWURWUVFERXlsTmFXTnliM052Wm5RZ1FYcDFjbVVnUm1Wa1pYSmhkR1ZrSUZOVFR5QkRaWEowYVdacFkyRjBaVEFlRncweU16QTBNamt4TWpRM05ESmFGdzB5TmpBME1qa3hNalEzTkRSYU1EUXhNakF3QmdOVkJBTVRLVTFwWTNKdmMyOW1kQ0JCZW5WeVpTQkdaV1JsY21GMFpXUWdVMU5QSUVObGNuUnBabWxqWVhSbE1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBMFdVTTZCeTNwTldBT3JycktNVWNnQkJ1d2ZEenY3L0VLV0FkY2tCbXhRaFdqQTBhMVp4QzdZVUpqU2JSL3UrYlFTbEdlSm12KytlZFUyTGprR21saG5zZ1g5R0JFMUpzZVNOMXJYb3M4ZldVeklBUDNlc2hTL3hJOHRBZTRiMDdLUVBnazAxMGJFeWZyYncrcnpEbFFMNDJwdTFSb293N2NUNWNtb0NLTWRQb0tWeCtxSmp3V05nSnl2aFVrNFM1YWlFZWFRMVMvYkFuUUxIOXd4b1B3ZXRpQVRaQkpPbWJDOHZpdXRHSjFmNDFuaFJrRVZiVzBndFFxaGdwOXVINDBhYmN2bWppZ2dCT0NRTkNVT0pxcDhrVlpnTmpsVUN1ZWxLODZibTRGZFkrenprWlJUYzZyajlaRVdxM0RGM3dYZVRVSDlrd2ZWZlNGSlNKb0tLWmtRSURBUUFCTUEwR0NTcUdTSWIzRFFFQkN3VUFBNElCQVFDR25nRnptTElPcDUxcGYvdTVEbDg2bXl6alh5emQyRG5ENGlBY3hzeit6WEZuZmVYdXpXek5RemRwWlp4NFRPSEJocEFZUmNIelhyRUpnOCsvcjEyaTNFRW81QlE4ajVaNTd2U1lINWVJZzJBdDJrallrMmg0bEZadDRxbGl4TjNIajJiV2N5M05TYXhoUHVrM1EzOWZGZUJGWU9GTWlwS2hqcjhGSGR6NmRLeUV0YVBselR4dGk3ZmJuMm9BSEJTajBQWmhVY2J5MXNJcGZDTWF5L1ZNeEJmckx0enJkWXhvV2NPZUJhTFh3MTVKMjBCQVFtWlo3VndYdXFHRWN0T3JYeHlpUlArZGlKQmMwb0NsbHdiSFBpNkZZUXB6U2tqeEFMYitQa3RQZ3Q5Y1R6Uzd5WHdja25sTFdtek5EdWtNaldvVGo5NTJiaVExSkVWV0U3M3g8L1g1MDlDZXJ0aWZpY2F0ZT48L1g1MDlEYXRhPjwvS2V5SW5mbz48L1NpZ25hdHVyZT48U3ViamVjdD48TmFtZUlEIEZvcm1hdD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6MS4xOm5hbWVpZC1mb3JtYXQ6ZW1haWxBZGRyZXNzIj5jaHJpcy5ncmFsaWtlQGZsZXZvLXNjb3V0cy5ubDwvTmFtZUlEPjxTdWJqZWN0Q29uZmlybWF0aW9uIE1ldGhvZD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmNtOmJlYXJlciI+PFN1YmplY3RDb25maXJtYXRpb25EYXRhIEluUmVzcG9uc2VUbz0iT05FTE9HSU5fMWRjYjUxMDQzOWE0ZTY5NWZmMTNmMDdjZGZkOWJmNjE4NDU1MDFlMyIgTm90T25PckFmdGVyPSIyMDIzLTA2LTIwVDE2OjU0OjQ0LjM1NVoiIFJlY2lwaWVudD0iaHR0cHM6Ly9tYy50cmlwcGllLmZ1bi9nbHBpL3BsdWdpbnMvcGhwc2FtbC9mcm9udC9hY3MucGhwIi8+PC9TdWJqZWN0Q29uZmlybWF0aW9uPjwvU3ViamVjdD48Q29uZGl0aW9ucyBOb3RCZWZvcmU9IjIwMjMtMDYtMjBUMTU6NDk6NDQuMzU1WiIgTm90T25PckFmdGVyPSIyMDIzLTA2LTIwVDE2OjU0OjQ0LjM1NVoiPjxBdWRpZW5jZVJlc3RyaWN0aW9uPjxBdWRpZW5jZT5odHRwczovL21jLnRyaXBwaWUuZnVuL2dscGkvPC9BdWRpZW5jZT48L0F1ZGllbmNlUmVzdHJpY3Rpb24+PC9Db25kaXRpb25zPjxBdHRyaWJ1dGVTdGF0ZW1lbnQ+PEF0dHJpYnV0ZSBOYW1lPSJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL2lkZW50aXR5L2NsYWltcy90ZW5hbnRpZCI+PEF0dHJpYnV0ZVZhbHVlPjNiNTMwMzYwLTk1ZDgtNDM5ZS1iZDY5LTNhOGMzMzFhMjM1ODwvQXR0cmlidXRlVmFsdWU+PC9BdHRyaWJ1dGU+PEF0dHJpYnV0ZSBOYW1lPSJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL2lkZW50aXR5L2NsYWltcy9vYmplY3RpZGVudGlmaWVyIj48QXR0cmlidXRlVmFsdWU+NThjMjU2MDgtNTBjNS00ZmY3LWJmNjAtYmFiNThiNjVhZGUyPC9BdHRyaWJ1dGVWYWx1ZT48L0F0dHJpYnV0ZT48QXR0cmlidXRlIE5hbWU9Imh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vaWRlbnRpdHkvY2xhaW1zL2Rpc3BsYXluYW1lIj48QXR0cmlidXRlVmFsdWU+Q2hyaXMgR3JhbGlrZSB8IEZsZXZvLVNjb3V0czwvQXR0cmlidXRlVmFsdWU+PC9BdHRyaWJ1dGU+PEF0dHJpYnV0ZSBOYW1lPSJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL2lkZW50aXR5L2NsYWltcy9pZGVudGl0eXByb3ZpZGVyIj48QXR0cmlidXRlVmFsdWU+aHR0cHM6Ly9zdHMud2luZG93cy5uZXQvM2I1MzAzNjAtOTVkOC00MzllLWJkNjktM2E4YzMzMWEyMzU4LzwvQXR0cmlidXRlVmFsdWU+PC9BdHRyaWJ1dGU+PEF0dHJpYnV0ZSBOYW1lPSJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL2NsYWltcy9hdXRobm1ldGhvZHNyZWZlcmVuY2VzIj48QXR0cmlidXRlVmFsdWU+aHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2F1dGhlbnRpY2F0aW9ubWV0aG9kL3Bhc3N3b3JkPC9BdHRyaWJ1dGVWYWx1ZT48QXR0cmlidXRlVmFsdWU+aHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2F1dGhlbnRpY2F0aW9ubWV0aG9kL3g1MDk8L0F0dHJpYnV0ZVZhbHVlPjxBdHRyaWJ1dGVWYWx1ZT5odHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL2NsYWltcy9tdWx0aXBsZWF1dGhuPC9BdHRyaWJ1dGVWYWx1ZT48L0F0dHJpYnV0ZT48QXR0cmlidXRlIE5hbWU9Imh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL2dpdmVubmFtZSI+PEF0dHJpYnV0ZVZhbHVlPkNocmlzPC9BdHRyaWJ1dGVWYWx1ZT48L0F0dHJpYnV0ZT48QXR0cmlidXRlIE5hbWU9Imh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL3N1cm5hbWUiPjxBdHRyaWJ1dGVWYWx1ZT5HcmFsaWtlPC9BdHRyaWJ1dGVWYWx1ZT48L0F0dHJpYnV0ZT48QXR0cmlidXRlIE5hbWU9Imh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL2VtYWlsYWRkcmVzcyI+PEF0dHJpYnV0ZVZhbHVlPmNocmlzLmdyYWxpa2VAZmxldm8tc2NvdXRzLm5sPC9BdHRyaWJ1dGVWYWx1ZT48L0F0dHJpYnV0ZT48QXR0cmlidXRlIE5hbWU9Imh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL25hbWUiPjxBdHRyaWJ1dGVWYWx1ZT5jaHJpcy5ncmFsaWtlQGZsZXZvLXNjb3V0cy5ubDwvQXR0cmlidXRlVmFsdWU+PC9BdHRyaWJ1dGU+PC9BdHRyaWJ1dGVTdGF0ZW1lbnQ+PEF1dGhuU3RhdGVtZW50IEF1dGhuSW5zdGFudD0iMjAyMy0wNC0wMVQxOTowODowOC4wMzlaIiBTZXNzaW9uSW5kZXg9Il9iNDlkMzRlOC1jYWE3LTQ5MzEtODhmYS04OTZmY2RjMTVjMDAiPjxBdXRobkNvbnRleHQ+PEF1dGhuQ29udGV4dENsYXNzUmVmPnVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphYzpjbGFzc2VzOlBhc3N3b3JkPC9BdXRobkNvbnRleHRDbGFzc1JlZj48L0F1dGhuQ29udGV4dD48L0F1dGhuU3RhdGVtZW50PjwvQXNzZXJ0aW9uPjwvc2FtbHA6UmVzcG9uc2U+
    [RelayState] => http://192.168.1.98/glpi/?SSO=1
)


 Unpacked:
Array
(
    [response] => <samlp:Response ID="_0180d2f6-1215-4e4d-8b44-95d7a2e59e78" Version="2.0" IssueInstant="2023-06-20T15:54:44.449Z" Destination="https://mc.trippie.fun/glpi/plugins/phpsaml/front/acs.php" InResponseTo="ONELOGIN_1dcb510439a4e695ff13f07cdfd9bf61845501e3" xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"><Issuer xmlns="urn:oasis:names:tc:SAML:2.0:assertion">https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/</Issuer><samlp:Status><samlp:StatusCode Value="urn:oasis:names:tc:SAML:2.0:status:Success"/></samlp:Status><Assertion ID="_b49d34e8-caa7-4931-88fa-896fcdc15c00" IssueInstant="2023-06-20T15:54:44.444Z" Version="2.0" xmlns="urn:oasis:names:tc:SAML:2.0:assertion"><Issuer>https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/</Issuer><Signature xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/><SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/><Reference URI="#_b49d34e8-caa7-4931-88fa-896fcdc15c00"><Transforms><Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/><Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></Transforms><DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/><DigestValue>PpbNPfsUtIPwp2EMLDOuOAuARbB3ee78A+7VR9hgoWg=</DigestValue></Reference></SignedInfo><SignatureValue>0L1Mqat17OkfZ3kdxV1YNk1dty2ejSr6QJyTHP4kFckyp0Wh8Xl2o7uvn6wNM0sPZ4ZTWdovCEo3ZIw+4pFlh7++rxgOlVplFMH8h23JeOu5O9YJ6t/5REGDRiXNy0pJNS7VPZspb1/nqqeuxn2uTnhw+gKNI9bHpIjyyy6IfM0OH91otzOgeKsFRDY7OQy/JVY4XQ7wTe8vMSRkBFtnwWqkyeeHybydcNFKZCyqByX6oDDHR+zS0SFzNn7cw+zJKchB9rr5IB0mHPFxD8Oi5NGD23HSBu+QEcBMsD0yMlp61+C3e4PQdflQuc2YVCQ2gxLO+9zscrNS4VbxdntDNw==</SignatureValue><KeyInfo><X509Data><X509Certificate>MIIC8DCCAdigAwIBAgIQY4aUyUa4fqtLXpe7AXzNzzANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMzA0MjkxMjQ3NDJaFw0yNjA0MjkxMjQ3NDRaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0WUM6By3pNWAOrrrKMUcgBBuwfDzv7/EKWAdckBmxQhWjA0a1ZxC7YUJjSbR/u+bQSlGeJmv++edU2LjkGmlhnsgX9GBE1JseSN1rXos8fWUzIAP3eshS/xI8tAe4b07KQPgk010bEyfrbw+rzDlQL42pu1Roow7cT5cmoCKMdPoKVx+qJjwWNgJyvhUk4S5aiEeaQ1S/bAnQLH9wxoPwetiATZBJOmbC8viutGJ1f41nhRkEVbW0gtQqhgp9uH40abcvmjiggBOCQNCUOJqp8kVZgNjlUCuelK86bm4FdY+zzkZRTc6rj9ZEWq3DF3wXeTUH9kwfVfSFJSJoKKZkQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQCGngFzmLIOp51pf/u5Dl86myzjXyzd2DnD4iAcxsz+zXFnfeXuzWzNQzdpZZx4TOHBhpAYRcHzXrEJg8+/r12i3EEo5BQ8j5Z57vSYH5eIg2At2kjYk2h4lFZt4qlixN3Hj2bWcy3NSaxhPuk3Q39fFeBFYOFMipKhjr8FHdz6dKyEtaPlzTxti7fbn2oAHBSj0PZhUcby1sIpfCMay/VMxBfrLtzrdYxoWcOeBaLXw15J20BAQmZZ7VwXuqGEctOrXxyiRP+diJBc0oCllwbHPi6FYQpzSkjxALb+PktPgt9cTzS7yXwcknlLWmzNDukMjWoTj952biQ1JEVWE73x</X509Certificate></X509Data></KeyInfo></Signature><Subject><NameID Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">chris.gralike@flevo-scouts.nl</NameID><SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer"><SubjectConfirmationData InResponseTo="ONELOGIN_1dcb510439a4e695ff13f07cdfd9bf61845501e3" NotOnOrAfter="2023-06-20T16:54:44.355Z" Recipient="https://mc.trippie.fun/glpi/plugins/phpsaml/front/acs.php"/></SubjectConfirmation></Subject><Conditions NotBefore="2023-06-20T15:49:44.355Z" NotOnOrAfter="2023-06-20T16:54:44.355Z"><AudienceRestriction><Audience>https://mc.trippie.fun/glpi/</Audience></AudienceRestriction></Conditions><AttributeStatement><Attribute Name="http://schemas.microsoft.com/identity/claims/tenantid"><AttributeValue>3b530360-95d8-439e-bd69-3a8c331a2358</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/identity/claims/objectidentifier"><AttributeValue>58c25608-50c5-4ff7-bf60-bab58b65ade2</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/identity/claims/displayname"><AttributeValue>Chris Gralike | Flevo-Scouts</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/identity/claims/identityprovider"><AttributeValue>https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/</AttributeValue></Attribute><Attribute Name="http://schemas.microsoft.com/claims/authnmethodsreferences"><AttributeValue>http://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/password</AttributeValue><AttributeValue>http://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/x509</AttributeValue><AttributeValue>http://schemas.microsoft.com/claims/multipleauthn</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname"><AttributeValue>Chris</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname"><AttributeValue>Gralike</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"><AttributeValue>chris.gralike@flevo-scouts.nl</AttributeValue></Attribute><Attribute Name="http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"><AttributeValue>chris.gralike@flevo-scouts.nl</AttributeValue></Attribute></AttributeStatement><AuthnStatement AuthnInstant="2023-04-01T19:08:08.039Z" SessionIndex="_b49d34e8-caa7-4931-88fa-896fcdc15c00"><AuthnContext><AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Password</AuthnContextClassRef></AuthnContext></AuthnStatement></Assertion></samlp:Response>
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
            [textContent] => https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/https://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/PpbNPfsUtIPwp2EMLDOuOAuARbB3ee78A+7VR9hgoWg=0L1Mqat17OkfZ3kdxV1YNk1dty2ejSr6QJyTHP4kFckyp0Wh8Xl2o7uvn6wNM0sPZ4ZTWdovCEo3ZIw+4pFlh7++rxgOlVplFMH8h23JeOu5O9YJ6t/5REGDRiXNy0pJNS7VPZspb1/nqqeuxn2uTnhw+gKNI9bHpIjyyy6IfM0OH91otzOgeKsFRDY7OQy/JVY4XQ7wTe8vMSRkBFtnwWqkyeeHybydcNFKZCyqByX6oDDHR+zS0SFzNn7cw+zJKchB9rr5IB0mHPFxD8Oi5NGD23HSBu+QEcBMsD0yMlp61+C3e4PQdflQuc2YVCQ2gxLO+9zscrNS4VbxdntDNw==MIIC8DCCAdigAwIBAgIQY4aUyUa4fqtLXpe7AXzNzzANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMzA0MjkxMjQ3NDJaFw0yNjA0MjkxMjQ3NDRaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0WUM6By3pNWAOrrrKMUcgBBuwfDzv7/EKWAdckBmxQhWjA0a1ZxC7YUJjSbR/u+bQSlGeJmv++edU2LjkGmlhnsgX9GBE1JseSN1rXos8fWUzIAP3eshS/xI8tAe4b07KQPgk010bEyfrbw+rzDlQL42pu1Roow7cT5cmoCKMdPoKVx+qJjwWNgJyvhUk4S5aiEeaQ1S/bAnQLH9wxoPwetiATZBJOmbC8viutGJ1f41nhRkEVbW0gtQqhgp9uH40abcvmjiggBOCQNCUOJqp8kVZgNjlUCuelK86bm4FdY+zzkZRTc6rj9ZEWq3DF3wXeTUH9kwfVfSFJSJoKKZkQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQCGngFzmLIOp51pf/u5Dl86myzjXyzd2DnD4iAcxsz+zXFnfeXuzWzNQzdpZZx4TOHBhpAYRcHzXrEJg8+/r12i3EEo5BQ8j5Z57vSYH5eIg2At2kjYk2h4lFZt4qlixN3Hj2bWcy3NSaxhPuk3Q39fFeBFYOFMipKhjr8FHdz6dKyEtaPlzTxti7fbn2oAHBSj0PZhUcby1sIpfCMay/VMxBfrLtzrdYxoWcOeBaLXw15J20BAQmZZ7VwXuqGEctOrXxyiRP+diJBc0oCllwbHPi6FYQpzSkjxALb+PktPgt9cTzS7yXwcknlLWmzNDukMjWoTj952biQ1JEVWE73xchris.gralike@flevo-scouts.nlhttps://mc.trippie.fun/glpi/3b530360-95d8-439e-bd69-3a8c331a235858c25608-50c5-4ff7-bf60-bab58b65ade2Chris Gralike | Flevo-Scoutshttps://sts.windows.net/3b530360-95d8-439e-bd69-3a8c331a2358/http://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/passwordhttp://schemas.microsoft.com/ws/2008/06/identity/authenticationmethod/x509http://schemas.microsoft.com/claims/multipleauthnChrisGralikechris.gralike@flevo-scouts.nlchris.gralike@flevo-scouts.nlurn:oasis:names:tc:SAML:2.0:ac:classes:Password
        )

    [decryptedDocument] => 
    [encrypted] => 
)


 POST:


 GET:
Array
(
)


 SERVER
Array
(
    [USER] => www-data
    [HOME] => /home/www-data
    [SCRIPT_NAME] => /glpi/plugins/phpsaml/front/acs.php
    [REQUEST_URI] => /glpi/plugins/phpsaml/front/acs.php
    [QUERY_STRING] => 
    [REQUEST_METHOD] => POST
    [SERVER_PROTOCOL] => HTTP/1.1
    [GATEWAY_INTERFACE] => CGI/1.1
    [REMOTE_PORT] => 52244
    [SCRIPT_FILENAME] => /var/www/html/glpi/plugins/phpsaml/front/acs.php
    [SERVER_ADMIN] => support@goix.nl
    [CONTEXT_DOCUMENT_ROOT] => /var/www/html
    [CONTEXT_PREFIX] => 
    [REQUEST_SCHEME] => http
    [DOCUMENT_ROOT] => /var/www/html
    [REMOTE_ADDR] => 192.168.1.191
    [SERVER_PORT] => 80
    [SERVER_ADDR] => 192.168.1.98
    [SERVER_NAME] => 192.168.1.98
    [SERVER_SOFTWARE] => Apache/2.4.54
    [SERVER_SIGNATURE] => 
    [PATH] => /usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/snap/bin
    [HTTP_CONNECTION] => Keep-Alive
    [CONTENT_LENGTH] => 7308
    [HTTP_X_FORWARDED_SERVER] => mc.trippie.fun
    [HTTP_X_FORWARDED_HOST] => mc.trippie.fun
    [HTTP_X_FORWARDED_FOR] => 192.168.2.254
    [HTTP_ACCEPT_LANGUAGE] => nl,en;q=0.9,en-GB;q=0.8,en-US;q=0.7
    [HTTP_ACCEPT_ENCODING] => gzip, deflate, br
    [HTTP_REFERER] => https://login.microsoftonline.com/
    [HTTP_SEC_FETCH_DEST] => document
    [HTTP_SEC_FETCH_MODE] => navigate
    [HTTP_SEC_FETCH_SITE] => cross-site
    [HTTP_ACCEPT] => text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
    [HTTP_USER_AGENT] => Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.51
    [CONTENT_TYPE] => application/x-www-form-urlencoded
    [HTTP_ORIGIN] => https://login.microsoftonline.com
    [HTTP_UPGRADE_INSECURE_REQUESTS] => 1
    [HTTP_SEC_CH_UA_PLATFORM] => "Windows"
    [HTTP_SEC_CH_UA_MOBILE] => ?0
    [HTTP_SEC_CH_UA] => "Not.A/Brand";v="8", "Chromium";v="114", "Microsoft Edge";v="114"
    [HTTP_CACHE_CONTROL] => max-age=0
    [HTTP_HOST] => 192.168.1.98
    [proxy-nokeepalive] => 1
    [FCGI_ROLE] => RESPONDER
    [PHP_SELF] => /glpi/plugins/phpsaml/front/acs.php
    [REQUEST_TIME_FLOAT] => 1687276484.5785
    [REQUEST_TIME] => 1687276484
)
