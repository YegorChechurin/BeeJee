<?php

namespace App\Service;

use App\Repository\TaskRepository;

class TaskPaginator
{
	private const TASKS_PER_PAGE = 3;

	private const PAGE_RANGE = 3;

	private $taskRepository;

	private $amountOfPages;

	public function __construct(TaskRepository $taskRepository)
	{
		$this->taskRepository = $taskRepository;
	}

	public function getTasksForPage(int $pageNumber): array
	{
		$offset = ($pageNumber - 1) * self::TASKS_PER_PAGE;

		return $this->taskRepository
		           ->fetchTasksByLimit($offset, self::TASKS_PER_PAGE);
	}

	public function getPaginationLimits(int $pageNumber): array
	{
		$totAmounOfTasks = $this->taskRepository->getTotalAmountOfTasks();
		$amountOfPages = ceil($totAmounOfTasks/self::TASKS_PER_PAGE);

		if ($pageNumber > self::PAGE_RANGE) {
			$leftPageLimit = $pageNumber - self::PAGE_RANGE;
		} else {
			$leftPageLimit = 1;
		}

		if ($amountOfPages - $pageNumber < self::PAGE_RANGE) {
			$rightPageLimit = $amountOfPages;
		} else {
			$rightPageLimit = $pageNumber + self::PAGE_RANGE;
		}

		return [
			'left_limit' => $leftPageLimit,
			'right_limit' => $rightPageLimit,
		];
	}
}