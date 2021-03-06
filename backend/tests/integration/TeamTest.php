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
    public function testUpdateTeam($team_id): int {
        $params = [
            'name' => 'testTeamUPDATED',
            'tags' => ['testTag'],
        ];
        $req = $this->createRequest('PUT', '/team/'.$team_id);
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        return $team_id;
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
    
    //Gets all the teams from the default environment
    public function testGetEnvTeams(): void {
        $request = $this->createRequest('GET', '/env/1/teams');
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('testTeamUPDATED', $result);
        $this->assertStringContainsString('this is a test', $result);
        $this->assertStringContainsString('testTag', $result);
        $this->assertStringContainsString('being very good at testing', $result);
    }

    //Creates a second user to make the request, out here so the delete gets called even if request fails
    public function testCreateSecondUser(): void {
        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser2',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/register');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    //Updates the user to be interested in testTag, and then finds the match
    public function testGetMatchingTeams(): void {
        $params = [
            'interests' => [
                ['env_id' => 0, 'interest' => 'testTag']
            ]
        ];
        $req = $this->createRequest('PUT', '/user');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createRequest('GET', '/env/1/teams/match');
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('testTeamUPDATED', $result);
    }

    //Creates a new user and requests to join the new team
    /**
     * @depends testCreateTeam
     */
    public function testRequestJoin($team_id): array {
        $params = [
            'message' => 'this is a test',
        ];
        $req = $this->createRequest('POST', '/team/'.$team_id.'/request');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $ret = ['team_id' => $team_id, 'user_id' => $_SESSION['user_id']];

        return $ret;
    }

    //Checks that the request went through
    /**
     * @depends testRequestJoin
     */
    public function testGetRequests($info): void {
        $request = $this->createRequest('GET', '/user/requests');
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString("".$info['team_id'], $result);
    }

    //Creates a third user to test accepting invites
    /**
     * @depends testRequestJoin
     */
    public function testCreateThirdUser($info): array {
        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser3',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/register');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(201, $response->getStatusCode());

        $info['user3_id'] = $_SESSION['user_id'];
        return $info;
    }

    //Checks that the request went through
    /**
     * @depends testCreateThirdUser
     */
    public function testGetTeamRequests($info): array {
        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $this->getAppInstance()->handle($request);

        $request = $this->createRequest('GET', '/team/'.$info['team_id'].'/requests');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $result = (string) $response->getBody();
        $this->assertStringContainsString('test', $result);

        return $info;
    }

    //Invites the users
    /**
     * @depends testGetTeamRequests
     */
    public function testInviteUsers($info): array {
        $params = [
            'message' => 'this is a test',
        ];
        $req = $this->createRequest('POST', '/team/'.$info['team_id'].'/invite/'.$info['user_id']);
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $req = $this->createRequest('POST', '/team/'.$info['team_id'].'/invite/'.$info['user3_id']);
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        return $info;
    }

    //Checks that the invite went through
    /**
     * @depends testInviteUsers
     */
    public function testCheckTeamInvites($info): void {
        $request = $this->createRequest('GET', '/team/'.$info['team_id'].'/invites');
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(''.$info['user_id'], $result);
    }

    //Checks that the invite went through
    /**
     * @depends testInviteUsers
     */
    public function testCheckUserInvites($info): void {
        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser2',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $this->getAppInstance()->handle($request);

        $request = $this->createRequest('GET', '/user/invites');
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(''.$info['team_id'], $result);

        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $this->getAppInstance()->handle($request);
    }

    //Denies the invite
    /**
     * @depends testInviteUsers
     */
    public function testDenyInvite($info): array {
        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser3',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $this->getAppInstance()->handle($request);

        $request = $this->createRequest('POST', '/team/'.$info['team_id'].'/deny');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $this->getAppInstance()->handle($request);

        return $info;
    }

    //Checks that the invite went through
    /**
     * @depends testInviteUsers
     */
    public function testAcceptInvite($info): array {
        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser3',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $this->getAppInstance()->handle($request);

        $request = $this->createRequest('POST', '/team/'.$info['team_id'].'/accept');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createRequest('POST', '/logout');
        $this->getAppInstance()->handle($request);

        $params = [
            'username' => 'testUser',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $this->getAppInstance()->handle($request);

        return $info;
    }

    //Checks that the invite went through
    /**
     * @depends testAcceptInvite
     */
    public function testCheckInvAccepted($info): void {
        $request = $this->createRequest('GET', '/team/'.$info['team_id']);
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(''.$info['user3_id'], $result);
    }

    //Denies the request
    /** 
     * @depends testInviteUsers
     */
    public function testDenyRequest($info): array {
        $request = $this->createRequest('POST', '/team/'.$info['team_id'].'/deny/'.$info['user_id']);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        return $info;
    }
    
    //Accepts the user into the team
    /** 
     * @depends testInviteUsers
     */
    public function testAcceptRequest($info): array {
        $request = $this->createRequest('POST', '/team/'.$info['team_id'].'/accept/'.$info['user_id']);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        return $info;
    }

    //Makes sure the user is now a team member
    /**
     * @depends testAcceptRequest
     */
    public function testCheckAccepted($info): void {
        $request = $this->createRequest('GET', '/team/'.$info['team_id']);
        $response = $this->getAppInstance()->handle($request);

        $result = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(''.$info['user_id'], $result);
    }

    //Successfully deletes the created team
    /**
     * @depends testCreateTeam
     */
    public function testDeleteTeam($team_id): int {
        $request = $this->createRequest('DELETE', '/team/'.$team_id);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        return $team_id;
    }

    //Verifies that the team was deleted
    /**
     * @depends testDeleteTeam
     */
    public function testVerifyTeamDeleted($team_id): void {
        $request = $this->createRequest('GET', '/team/'.$team_id);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
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

    //Successfully deletes the second user
    /**
     * @depends testCreateSecondUser
     */
    public function testDeleteSecondUser(): void {
        $params = [
            'username' => 'testUser2',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createRequest('DELETE', '/user');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    //Successfully deletes the second user
    /**
     * @depends testCreateThirdUser
     */
    public function testDeleteThirdUser(): void {
        $params = [
            'username' => 'testUser3',
            'password' => 'test'
        ];
        $req = $this->createRequest('POST', '/login');
        $request = $req->withParsedBody($params);
        $response = $this->getAppInstance()->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $request = $this->createRequest('DELETE', '/user');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
