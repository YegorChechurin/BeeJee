<?php

namespace App\Entity;

class Task
{
	private $id;

	private $username;

	private $email;

	private $text;

	public function getId(): int
	{
		return $this->id;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setUsername(string $username)
	{
		$this->username = $username; 
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email)
	{
		$this->email = $email;
	}

	public function function getText(): string
	{
		return $this->text;
	}

	public function setText(string $text)
	{
		$this->text = $text;
	}
}