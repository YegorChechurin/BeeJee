<?php

namespace App\Router;

use Symfony\Component\HttpFoundation\Request;

class Router
{
	private $request;

	private $routes;

	public function __construct(Request $request, array $routes)
	{
		$this->request = $request;
		$this->routes = $routes;
	}

	public function dispatch()
	{
		foreach ($this->routes as $r) {
			$urlMatch = preg_match('%/tasks'.$r['url'].'%', $this->request->getPathInfo());

			$methodMatch = (in_array($this->request->getMethod(), $r['methods'])) 
			    || 'any' == ($r['method']);

			if ($urlMatch && $methodMatch) {
				$controllerLiteral = '\\App\\Controller\\'.$r['controller'].'Controller';

				$controller = new $controllerLiteral($this->request, 
					$r['template']);
				$controller->loadWebPage();

				break;
			}
		}
	}
}