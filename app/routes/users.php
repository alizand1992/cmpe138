<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// GET USER LOGIN
$app->get('/user/login', \UserController::class . ':login');

// GET USER REGISTRATION
$app->get('/user/register', \UserController::class . ':register');

// POST LOGIN
$app->post('/user/session_create', \UserController::class . ':session_create');
