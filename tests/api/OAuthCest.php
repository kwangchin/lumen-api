<?php

use \Codeception\Util\Fixtures;

class OAuthCest
{
    protected $client_access_token, $refresh_token;

    public function _before(ApiTester $I)
    {
        // generate client access token
        $this->testGenerateClientAccessToken($I);
        $this->client_access_token = json_decode($I->grabResponse(), true)['access_token'];

        // generate user refresh token
        $this->testGenerateUserAccessToken($I);
        $this->refresh_token = json_decode($I->grabResponse(), true)['refresh_token'];
    }

    public function _after(ApiTester $I)
    {
    }

    public function testGenerateClientAccessToken(ApiTester $I)
    {
        $I->wantTo('test generate client access token');
        $I->sendPOST('/oauth/client_access_token', [
            "grant_type"    => "client_credentials",
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret'),
            "scope"         => Fixtures::get('client_scope')
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.access_token');
        $I->seeResponseJsonMatchesJsonPath('$.token_type');
        $I->seeResponseJsonMatchesJsonPath('$.expires_in');
    }

    public function testGenerateClientAccessTokenWithInvalidSecret(ApiTester $I)
    {
        $I->wantTo('test generate client access token with invalid client_secret');
        $I->sendPOST('/oauth/client_access_token', [
            "grant_type"    => "client_credentials",
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret') . 'XXX',
            "scope"         => Fixtures::get('client_scope')
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            "error"             => "invalid_client",
            "error_description" => "Client authentication failed."
        ]);
    }

    public function testGenerateUserAccessToken(ApiTester $I)
    {
        $I->wantTo('test generate user access token');
        $I->amBearerAuthenticated($this->client_access_token);
        $I->sendPOST('/oauth/access_token', [
            "username"      => Fixtures::get('username'),
            "password"      => Fixtures::get('password'),
            "grant_type"    => "password",
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret'),
            "scope"         => Fixtures::get('user_scope')
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.access_token');
        $I->seeResponseJsonMatchesJsonPath('$.token_type');
        $I->seeResponseJsonMatchesJsonPath('$.expires_in');
        $I->seeResponseJsonMatchesJsonPath('$.refresh_token');
    }

    public function testGenerateUserAccessTokenWithInvalidEmail(ApiTester $I)
    {
        $I->wantTo('test generate user access token with invalid email');
        $I->amBearerAuthenticated($this->client_access_token);
        $I->sendPOST('/oauth/access_token', [
            "username"      => Fixtures::get('username') . 'XYZ',
            "password"      => Fixtures::get('password'),
            "grant_type"    => "password",
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret'),
            "scope"         => Fixtures::get('user_scope')
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            "error"             => "invalid_credentials",
            "error_description" => "The user credentials were incorrect."
        ]);
    }

    public function testGenerateUserAccessTokenWithInvalidPassword(ApiTester $I)
    {
        $I->wantTo('test generate user access token with invalid password');
        $I->amBearerAuthenticated($this->client_access_token);
        $I->sendPOST('/oauth/access_token', [
            "username"      => Fixtures::get('username'),
            "password"      => Fixtures::get('password') . 'XYZ',
            "grant_type"    => "password",
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret'),
            "scope"         => Fixtures::get('user_scope')
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            "error"             => "invalid_credentials",
            "error_description" => "The user credentials were incorrect."
        ]);
    }

    public function testGenerateUserAccessTokenWithInvalidClientAccessToken(ApiTester $I)
    {
        $I->wantTo('test generate user access token with invalid client access token');
        $I->amBearerAuthenticated($this->client_access_token . 'XYZ');
        $I->sendPOST('/oauth/access_token', [
            "username"      => Fixtures::get('username'),
            "password"      => Fixtures::get('password'),
            "grant_type"    => "password",
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret'),
            "scope"         => Fixtures::get('user_scope')
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            "error"             => "access_denied",
            "error_description" => "The resource owner or authorization server denied the request."
        ]);
    }

    public function testGenerateUserAccessTokenWithoutClientAccessToken(ApiTester $I)
    {
        $I->wantTo('test generate user access token without client access token');
        $I->amBearerAuthenticated('');
        $I->sendPOST('/oauth/access_token', [
            "username"      => Fixtures::get('username'),
            "password"      => Fixtures::get('password'),
            "grant_type"    => "password",
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret'),
            "scope"         => Fixtures::get('user_scope')
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            "error"             => "invalid_request",
            "error_description" => "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed. Check the \"access token\" parameter."
        ]);
    }

    public function testRefreshUserAccessToken(ApiTester $I)
    {
        $I->wantTo('test refresh user access token');
        $I->amBearerAuthenticated($this->client_access_token);
        $I->sendPOST('/oauth/access_token', [
            "grant_type"    => "refresh_token",
            "refresh_token" => $this->refresh_token,
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret')
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.access_token');
        $I->seeResponseJsonMatchesJsonPath('$.token_type');
        $I->seeResponseJsonMatchesJsonPath('$.expires_in');
        $I->seeResponseJsonMatchesJsonPath('$.refresh_token');
    }

    public function testRefreshUserAccessTokenWithInvalidToken(ApiTester $I)
    {
        $I->wantTo('test refresh user access token with invalid refresh token');
        $I->amBearerAuthenticated($this->client_access_token);
        $I->sendPOST('/oauth/access_token', [
            "grant_type"    => "refresh_token",
            "refresh_token" => $this->refresh_token . 'XYZ',
            "client_id"     => Fixtures::get('client_id'),
            "client_secret" => Fixtures::get('client_secret')
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            "error"             => "invalid_request",
            "error_description" => "The refresh token is invalid."
        ]);
    }
}
