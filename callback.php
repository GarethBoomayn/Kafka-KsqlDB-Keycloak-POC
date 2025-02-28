<?php
require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\GenericProvider;

session_start();

$provider = new GenericProvider([
    'clientId'                => 'php-app',             // Your client ID
    'clientSecret'            => 'YOUR_CLIENT_SECRET',  // Your client secret
    'redirectUri'             => 'http://localhost:8000/callback',
    'urlAuthorize'            => 'http://keycloak:8080/realms/test-realm/protocol/openid-connect/auth',
    'urlAccessToken'          => 'http://keycloak:8080/realms/test-realm/protocol/openid-connect/token',
    'urlResourceOwnerDetails' => 'http://keycloak:8080/realms/test-realm/protocol/openid-connect/userinfo'
]);

// Check given state against previously stored one to mitigate CSRF attack
if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

if (!isset($_GET['code'])) {
    exit('Authorization code not received');
}

try {
    // Try to get an access token using the authorization code grant.
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // We can now use the access token to fetch the user's details.
    $resourceOwner = $provider->getResourceOwner($accessToken);
    $user = $resourceOwner->toArray();

    // For demo, print the token and user info.
    echo '<h3>Access Token</h3>';
    echo '<pre>' . print_r($accessToken->getToken(), true) . '</pre>';

    echo '<h3>User Info</h3>';
    echo '<pre>' . print_r($user, true) . '</pre>';

} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    exit($e->getMessage());
}
