<?php
require 'vendor/autoload.php';

use League\OAuth2\Client\Provider\GenericProvider;

session_start();

// Configure the OAuth2 client with Keycloak endpoints:
$provider = new GenericProvider([
    'clientId'                => 'php-app',             // Replace with your client ID
    'clientSecret'            => 'YOUR_CLIENT_SECRET',  // Replace with your client secret
    'redirectUri'             => 'http://localhost:8000/callback',
    'urlAuthorize'            => 'http://keycloak:8080/realms/test-realm/protocol/openid-connect/auth',
    'urlAccessToken'          => 'http://keycloak:8080/realms/test-realm/protocol/openid-connect/token',
    'urlResourceOwnerDetails' => 'http://keycloak:8080/realms/test-realm/protocol/openid-connect/userinfo'
]);

// Get the authorization URL; this returns a URL with the proper parameters:
$authorizationUrl = $provider->getAuthorizationUrl();

// Save the state generated for you and store it to the session.
$_SESSION['oauth2state'] = $provider->getState();

// Redirect the user to Keycloak for authorization
header('Location: ' . $authorizationUrl);
exit;
