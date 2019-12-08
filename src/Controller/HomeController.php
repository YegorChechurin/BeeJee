<?php

namespace App\Controller;

use App\Controller\AbstractController;
use App\Database\Database;
use App\Repository\TaskRepository;
use App\Service\FormBuilder;
use App\Service\TaskPaginator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends AbstractController
{
	public function loadWebPage()
	{
		$DB = new Database();
		$repo = new TaskRepository($DB);
		$paginator = new TaskPaginator($repo);

		$pageNumber = $this->request->query->get('page');
		if (!$pageNumber) {
			$pageNumber = 1;
		}

		$tasks = $paginator->getTasksForPage((int)$pageNumber);

		$paginationLimits = $paginator->getPaginationLimits((int)$pageNumber);

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
				'page_number' => $pageNumber,
				'left_limit' => $paginationLimits['left_limit'],
				'right_limit' => $paginationLimits['right_limit'],
				'url' => strtok($this->request->getUri(), '?'),
			]
		);
	}
}