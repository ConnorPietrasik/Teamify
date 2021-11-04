<?php

declare(strict_types=1);

use Slim\Routing\RouteCollectorProxy;
use App\Controller\Auth\AuthMiddleware;
use App\Controller\Team\TeamAuthAdminMiddleware;
use App\Controller\Team\TeamAuthMemberMiddleware;

$app->get('/', 'App\Controller\Home:getHelp');
$app->get('/status', 'App\Controller\Home:getStatus');

//Routes that require team membership
$app->group('', function (RouteCollectorProxy $group){
    $group->get('/team/{team_id}/requests', App\Controller\Team\GetRequests::class);
})->add(new TeamAuthMemberMiddleware);

//Routes that require just user authentication
$app->group('', function (RouteCollectorProxy $group){
    $group->get('/checkauth', App\Controller\Auth\CheckAuth::class);
    $group->put('/user', App\Controller\User\Update::class);
    $group->delete('/user', App\Controller\User\Delete::class);
    $group->get('/user/requests', App\Controller\User\GetTeamRequests::class);
    $group->post('/env/{env_id}/open', App\Controller\Environment\PostOpen::class);
    $group->delete('/env/{env_id}/open', App\Controller\Environment\RemoveOpen::class);
    $group->post('/env/{env_id}/createteam', App\Controller\Team\CreateTeam::class);
    $group->post('/team/{team_id}/request', App\Controller\Team\RequestJoin::class);
})->add(new AuthMiddleware);

//Routes that require team admin rights
$app->group('', function (RouteCollectorProxy $group){
    $group->put('/team/{team_id}', App\Controller\Team\UpdateTeam::class);
    $group->delete('/team/{team_id}', App\Controller\Team\DeleteTeam::class);
    $group->post('/team/{team_id}/accept/{user_id}', App\Controller\Team\AcceptRequest::class);
    $group->post('/team/{team_id}/deny/{user_id}', App\Controller\Team\DenyRequest::class);
})->add(new TeamAuthAdminMiddleware);

//Routes that don't require any authentication
$app->post('/register', App\Controller\Auth\Register::class);
$app->post('/login', App\Controller\Auth\Login::class);
$app->post('/logout', App\Controller\Auth\Logout::class);
$app->get('/user/{id}', App\Controller\User\GetWhole::class);
$app->get('/env/{env_id}/open', App\Controller\Environment\GetOpen::class);
$app->post('/env/{env_id}/open/skill', App\Controller\Environment\GetOpenSkill::class);
$app->get('/env/{env_id}/user', App\Controller\Environment\getAllEnvUsers::class);
$app->get('/env/{env_id}/user/{user_id}', App\Controller\Environment\GetEnvUser::class);
$app->get('/env/{env_id}/teams', App\Controller\Environment\GetAllTeams::class);
$app->get('/team/{team_id}', App\Controller\Team\GetTeam::class);