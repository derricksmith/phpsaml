<?php
// Capture the post before GLPI does.
$post = $_POST;
$_POST = [];
// Load GLPI includes
include('../../../inc/includes.php');

// Peform assertion
$acs = new PluginPhpsamlAcs();
if(array_key_exists('SAMLResponse', $post)){
    $acs->assertSaml($post);
} else {
    $acs->printError('no SAMLResponse found in POST header');
}
