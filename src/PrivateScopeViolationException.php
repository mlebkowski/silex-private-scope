<?php

namespace Nassau\Silex;

class PrivateScopeViolationException extends \Exception
{
	public function __construct($key)
	{
		parent::__construct("You cannot access private service `$key`");
	}
}