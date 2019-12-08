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

	public function storeTask(array $task): void
	{
		$fields = ['username', 'email', 'text', 'status'];

		$values = [
			$task['username'],
			$task['email'],
			$task['text'],
			'TODO'
		];

		$this->DB->insert(self::TABLE, $fields, $values);
	}

	public function fetchAllTasks(): array
	{
		return $this->DB->select(self::TABLE, Task::class);
	}
}