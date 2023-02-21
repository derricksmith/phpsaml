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

   @package   phpsaml
   @author    Chris Gralike
   @co-author
   @copyright Copyright (c) 2018 by Derrick Smith
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @since     2022

   ------------------------------------------------------------------------
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "result!?";
var_dump(function_exists('open_ssl_x509_parse'));

$cert = '-----BEGIN CERTIFICATE-----
MIIFGTCCBAGgAwIBAgISBG5NM+VgEAAF5LQwOQMtDbZl
MA0GCSqGSIb3DQEBCwUAMDIxCzAJBgNVBAYTAlVTMRYw
FAYDVQQKEw1MZXQncyBFbmNyeXB0MQswCQYDVQQDEwJS
MzAeFw0yMTA1MTAyMzU0NDhaFw0yMTA4MDgyMzU0NDha
MBUxEzARBgNVBAMTCm1jLmdvaXgubmwwggEiMA0GCSqG
SIb3DQEBcDovL3IzLm8ubGVu
Y3Iub3JnMCIGCCsGAQUFBzAChhZodHRwOi8vcjMuaS5s
ZW5jci5vcmcvMBUGA1UdEQQOMAyCCm1jLmdvaXgubmww
TAYDVR0gBEUwQzAIBgZngQwBAgEwNwYLKwYBBAGC3xMB
AQEwKDAmBggrBgEFBQcCARYaaHR0cDovL2Nwcy5sZXRz
ZW5jcnlwdC5vcmcwggEDBgorBgEEAdZ5AgQCBIH0BIHx
AO8AdgBc3EOS/uarRUSxXprUVuYQN/vV+kfcoXOUsl7m
9scOygAAAXlY61RmAAAEAwBHMEUCIET16FsoGzJj14gy
9QjZsR33v7cShtp8JEuAJ7A0tXQ5AiEAyVRchX8hno8n
0pAvLWB
-----END CERTIFICATE-----';

$cert = preg_replace('/\r\n|\r|\n/','',$cert);
preg_match('/(-+BEGIN CERTIFICATE-+)(.+?)(-+END CERTIFICATE-+)/', $cert, $m);
// There should be exactly 4 matches!
if (count($m) == 4) {
  $cert = $m['1']."\n".$m['2']."\n".$m['3'];
}
// Test the outcome!
echo "<pre>";
var_dump(openssl_x509_parse($cert, false));



//print_r($matches);


//v
