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

// Verify state against stored state to prevent CSRF attacks
if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

// Check for authorization code
if (!isset($_GET['code'])) {
    exit('Authorization code not received');
}

try {
    // Get access token using the authorization code
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Fetch user details
    $resourceOwner = $provider->getResourceOwner($accessToken);
    $user = $resourceOwner->toArray();

    // Print token and user info
    echo '<h3>Access Token</h3>';
    echo '<pre>' . print_r($accessToken->getToken(), true) . '</pre>';

    echo '<h3>User Info</h3>';
    echo '<pre>' . print_r($user, true) . '</pre>';

} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    exit($e->getMessage());
}
