<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Twig\Loader\FilesystemLoader;
use Twig\Environment as TwigEnv;

abstract class AbstractController
{
	protected $request;

	protected $template;

	protected $twig;

	abstract public function loadWebPage();

	public function __construct(Request $request, string $template)
	{
		$this->request = $request;

		$this->template = $template;

		$this->twig = new TwigEnv(
			new FilesystemLoader(dirname(__DIR__, 2).'/templates')
		);
	}

	protected function renderView(array $viewParameters = [])
	{
		echo $this->twig->render($this->template, $viewParameters);
	}
}