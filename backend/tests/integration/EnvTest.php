<?php

declare(strict_types=1);

namespace Tests\integration;

if ( !isset( $_SESSION ) ) $_SESSION = array(  );

class EnvTest extends TestCase{
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
    }

    //Successfully updates the user's skills
    public function testUpdateSkills(): void {
        $params = [
            'skills' => [['env_id' => 0, 'skill' => 'this is a skill'], 
                        ['env_id' => 1, 'skill' => 'this is a second skill']]
        ];
        $req = $this->createRequest('PUT', '/user');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    //Successfully updates the user's availability
    public function testUpdateAvailability(): void {
        $params = [
            'availability' =>   [['day' => 0, 'time' => '1:00AM-3:00AM'], 
                                ['day' => 2, 'time' => '2:00PM-11:59PM']]
        ];
        $req = $this->createRequest('PUT', '/user');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    //Successfully updates the user's interests
    public function testUpdateInterests(): void {
        $params = [
            'interests' => [['env_id' => 0, 'interest' => 'interesting things'], 
                        ['env_id' => 1, 'interest' => 'water']]
        ];
        $req = $this->createRequest('PUT', '/user');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    //Gets the updated user and confirms it worked
    public function testGetUser(): void {
        $request = $this->createRequest('GET', '/user/'.$_SESSION['user_id']);
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Test Name', $result);
        $this->assertStringContainsString('Testing tester', $result);
        $this->assertStringContainsString('this is a skill', $result);
        $this->assertStringContainsString('this is a second skill', $result);
        $this->assertStringContainsString('1:00AM-3:00AM', $result);
        $this->assertStringContainsString('water', $result);
    }

    //Successfully deletes the user
    public function testDelete(): void {
        $request = $this->createRequest('DELETE', '/user');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
