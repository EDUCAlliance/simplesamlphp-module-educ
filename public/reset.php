<?php
/**
 * Deletes the cookie holding the selected IdP.
 */

use SimpleSAML\XHTML\Template;
use SimpleSAML\HTTP\RunnableResponse;

$config = SimpleSAML\Configuration::getInstance();
$session = SimpleSAML\Session::getSessionFromRequest();
$path = $config->getBasePath();
// Default name of the cookie
$cookieName = 'saml_idp';

if (isset($_GET['clear'])) {
    // Destroy the cookie
    setcookie($cookieName, '', [
        'expires' => time() - 3600,
        'path' => $path,
        'secure' => true,
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
    SimpleSAML\Logger::info('IdP selection cleared by user');
    $t = new Template($config, $templateName = 'educ:reset_complete.twig');
    echo $t->getContents();
    exit;
}

$t = new Template($config, $templateName = 'educ:reset_confirm');
echo $t->getContents();
