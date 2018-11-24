<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// GET USER LOGIN
$app->get('/user/login', \UserController::class . ':login');

// GET USER REGISTRATION
$app->get('/user/register', \UserController::class . ':register');

// POST LOGIN
$app->post('/user/create_session', \UserController::class . ':createSession');

// POST REGISTER
$app->post('/user/create_user', \UserController::class . ':createUser');
