<?php

declare(strict_types=1);

namespace Tests\integration;

if ( !isset( $_SESSION ) ) $_SESSION = array(  );

class TeamTest extends TestCase{
    protected $backupGlobalsBlacklist = array( '_SESSION' );
    protected $team_id;

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

    //Successfully creates a team
    /**
     * @depends testRegisterPass
     */
    public function testCreateTeam(): int {
        $params = [
            'name' => 'testTeam',
            'description' => 'this is a test',
            'tags' => ['test', 'testytest'],
            'looking_for' => ['testing', 'being very good at testing']
        ];
        $req = $this->createRequest('POST', '/env/1/createteam');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('team_id', $result);
        return json_decode($result)->team_id;
    }

    //Successfully updates the team
    /**
     * @depends testCreateTeam
     */
    public function testUpdateTeam($team_id): void {
        $params = [
            'name' => 'testTeamUPDATED',
            'tags' => ['testTag'],
        ];
        $req = $this->createRequest('PUT', '/team/'.$team_id);
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    //Verifies that the team info is all correct
    /**
     * @depends testUpdateTeam
     */
    public function testGetTeam($team_id): void {
        $request = $this->createRequest('GET', '/team/'.$team_id);
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('testTeamUPDATED', $result);
        $this->assertStringContainsString('this is a test', $result);
        $this->assertStringContainsString('testTag', $result);
        $this->assertStringContainsString('being very good at testing', $result);
    }

    //Successfully deletes the user
    /**
     * @depends testRegisterPass
     */
    public function testDeleteUser(): void {
        $request = $this->createRequest('DELETE', '/user');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
