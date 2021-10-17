<?php

declare(strict_types=1);

namespace Tests\integration;

if ( !isset( $_SESSION ) ) $_SESSION = array(  );

class AuthTest extends TestCase{
    protected $backupGlobalsBlacklist = array( '_SESSION' );

    //Successfully registers a testuser with a password
    public function testRegisterPass(): void {
        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/register');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('user_id', $result);
        $this->assertStringNotContainsString('error', $result);
    }

    //Successfully checks that testUser is logged in
    public function testCheckAuth(): void {
        $request = $this->createRequest('GET', '/checkauth');
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('user_id', $result);
        $this->assertStringNotContainsString('error', $result);
    }

    //Successfully logs the user out
    public function testLogOut(): void {
        $request = $this->createRequest('POST', '/logout');
        $response = $this->getAppInstance()->handle($request);

        echo $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
    }

    //Makes sure the user is no longer logged in
    public function testCheckAuthFail(): void {
        $request = $this->createRequest('GET', '/checkauth');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    //Fails to create another account with the same username (using password)
    public function testRegisterPassFail(): void {
        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/register');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(409, $response->getStatusCode());
    }

    //Successfully logs the user back in
    public function testLoginPass(): void {
        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('user_id', $result);
        $this->assertStringNotContainsString('error', $result);
    }

    //Successfully checks that testUser is logged in again
    public function testCheckAuthAgain(): void {
        $request = $this->createRequest('GET', '/checkauth');
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('user_id', $result);
        $this->assertStringNotContainsString('error', $result);
    }

    //Successfully deletes the user
    public function testDelete(): void {
        $request = $this->createRequest('DELETE', '/user');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    //Fails to log in because user is deleted
    public function testLoginPassFail(): void {
        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
