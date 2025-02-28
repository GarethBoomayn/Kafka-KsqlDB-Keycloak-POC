<?php
require __DIR__ . '/../vendor/autoload.php'; // Updated path

use League\OAuth2\Client\Provider\GenericProvider;
use Dotenv\Dotenv;

session_start();

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configure OAuth2 client
$provider = new GenericProvider([
    'clientId'                => $_ENV['KEYCLOAK_CLIENT_ID'], 
    'clientSecret'            => $_ENV['KEYCLOAK_CLIENT_SECRET'], 
    'redirectUri'             => $_ENV['REDIRECT_URI'],
    'urlAuthorize'            => $_ENV['KEYCLOAK_URL'] . '/realms/test-realm/protocol/openid-connect/auth',
    'urlAccessToken'          => $_ENV['KEYCLOAK_URL'] . '/realms/test-realm/protocol/openid-connect/token',
    'urlResourceOwnerDetails' => $_ENV['KEYCLOAK_URL'] . '/realms/test-realm/protocol/openid-connect/userinfo'
]);

// Get authorization URL
$authorizationUrl = $provider->getAuthorizationUrl();

// Store state in session to protect against CSRF attacks
$_SESSION['oauth2state'] = $provider->getState();

// Redirect to Keycloak
header('Location: ' . $authorizationUrl);
exit;
