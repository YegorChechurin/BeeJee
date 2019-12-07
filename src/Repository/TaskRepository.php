<?php

namespace App\Repository;

use App\Database\Database;
use App\Entity\Task;

class TaskRepository
{
	private const TABLE = 'task';

	private $DB;

	public function __construct(Database $database)
	{
		$this->DB = $database;
	}

	public function storeTask(Task $task): void
	{
		$fields = ['username', 'email', 'text'];

		$values = [
			$task->getUsername(),
			$task->getEmail(),
			$task->getText(),
		];

		$this->DB->insert(self::TABLE, $fields, $values);
	}

	public function fetchAllTasks(): array
	{
		return $this->DB->select(self::TABLE, Task::class);
	}
}