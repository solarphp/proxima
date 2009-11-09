<?php
// get the system dir
$system = $config['Solar']['system'];

// set the domain value
if (! empty($_SERVER['HTTP_HOST'])) {
    // http host
    $domain = $_SERVER['HTTP_HOST'];
} elseif (! empty($_SERVER['SERVER_NAME'])) {
    // server name
    $domain = $_SERVER['SERVER_NAME'];
} else {
    // fake it
    $domain = 'example.com';
}

/**
 * when checking ownership access, use these methods for these instance types
 */
$config['Solar_Access']['owner_method']['Proxima_Model_Members_Record'] = 'accessIsOwner';
$config['Solar_Access']['owner_method']['Proxima_Model_Nodes_Record']   = 'accessIsOwner';

/**
 * Model config: members
 */
$config['Proxima_Model_Members_Record'] = array(
    'email_forgot_from' => "do-not-reply@{$domain}",
    'email_forgot_subj' => "[Proxima] Forgot Password",
    'email_forgot_body' => "Click on this link to reset your password:\n\n"
                         . "http://{$domain}/members/reset/{:confirm_hash}",
);
