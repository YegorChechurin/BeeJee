<?php

namespace App\Controller;

use App\Controller\AbstractController;
use App\Database\Database;
use App\Repository\TaskRepository;
use App\Service\FormBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends AbstractController
{
	private const TASKS_PER_PAGE = 3;

	public function loadWebPage()
	{
		$DB = new Database();
		$repo = new TaskRepository($DB);

		$totAmounOfTasks = $repo->getTotalAmountOfTasks();
		$amountOfPages = ceil($totAmounOfTasks/self::TASKS_PER_PAGE);

		$pageNumber = $this->request->query->get('page');
		if ($pageNumber) {
			$offset = ($pageNumber - 1) * self::TASKS_PER_PAGE;
		} else {
			$offset = 0;
		}

		$tasks = $repo->fetchTasksByLimit($offset, self::TASKS_PER_PAGE);

		$form = FormBuilder::buildTaskCreationForm($this->formFactory);

		$form->handleRequest($this->request);

		if ($form->isSubmitted() && $form->isValid()) {
		    $repo->storeTask($form->getData());

		    $response = new RedirectResponse($this->request->getUri());
		    $response->send();
		}

		$this->renderView(
			[
				'form' => $form->createView(),
				'tasks' => $tasks,
			]
		);
	}
}