<?php

require '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use App\Router\Router;

$routes = [
	[
		'url' => '/home',
		'methods' => ['GET', 'POST'],
		'controller' => 'Home',
		'template' => 'home.html.twig',
	],
];

$request = Request::createFromGlobals();

$router = new Router($request, $routes);

$router->dispatch();
