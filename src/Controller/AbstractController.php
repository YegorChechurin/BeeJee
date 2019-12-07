<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Twig\Loader\FilesystemLoader;
use Twig\Environment as TwigEnv;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

abstract class AbstractController
{
	private const FORM_THEME = 'bootstrap_4_layout.html.twig';

	protected $request;

	private static $csrfManager;

	protected $template;

	protected $twig;

	protected $formFactory;

	abstract public function loadWebPage();

	public function __construct(Request $request, string $template)
	{
		$this->request = $request;

		$this->template = $template;

		$this->twig = self::bootTwig();

		$this->formFactory = self::bootFormFactory();
	}

	private static function bootTwig()
	{
		$appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
		$vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());

		$viewsDirectory = dirname(__DIR__, 2).'/templates';

		$twig = new TwigEnv(new FilesystemLoader([
		    $viewsDirectory,
		    $vendorTwigBridgeDirectory.'/Resources/views/Form',])
		);

		$formEngine = new TwigRendererEngine([self::FORM_THEME], $twig);

		$session = new Session();
		$csrfGenerator = new UriSafeTokenGenerator();
		$csrfStorage = new SessionTokenStorage($session);
		self::$csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

		$twig->addRuntimeLoader(new FactoryRuntimeLoader([
		    FormRenderer::class => function () use ($formEngine) {
		            return new FormRenderer($formEngine, self::$csrfManager);
		        },
		]));
		$twig->addExtension(new FormExtension());

		$translator = new Translator('en');
		$translator->addLoader('xlf', new XliffFileLoader());
		$twig->addExtension(new TranslationExtension($translator));

		return $twig;
	}

	private static function bootFormFactory()
	{
		$validator = Validation::createValidator();

		$formFactory = Forms::createFormFactoryBuilder()
		    ->addExtension(new HttpFoundationExtension())
		    ->addExtension(new CsrfExtension(self::$csrfManager))
		    ->addExtension(new ValidatorExtension($validator))
		    ->getFormFactory();

		return $formFactory;
	}

	protected function renderView(array $viewParameters = [])
	{
		echo $this->twig->render($this->template, $viewParameters);
	}
}