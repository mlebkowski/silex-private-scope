<?php

namespace Nassau\Silex;

use Silex\Application;

class ScopedApplication extends Application
{
	const SCOPE_PRIVATE = 'private';
	const SCOPE_PUBLIC = 'public';

	private $publicServices = [];

	private $currentScope;
	private $defaultScope;

	public function __construct(array $values = [])
	{
		$this->defaultScope = self::SCOPE_PUBLIC;
		parent::__construct($values);

		$this->defaultScope = self::SCOPE_PRIVATE;
		$this->currentScope = self::SCOPE_PUBLIC;
	}

	public function offsetSet($id, $value)
	{
		$isPublic = $this->isDefaultNewServicesPublic();

		if ($value instanceof PublicService)
		{
			$isPublic = true;
			$value = $value->getValue();
		}

		parent::offsetSet($id, $value);

		if ($isPublic)
		{
			$this->publicServices[$id] = true;
		}
	}

	public function offsetUnset($id)
	{
		parent::offsetUnset($id);
		unset($this->publicServices[$id]);
	}

	public function isServicePublished($id)
	{
		return isset($this->publicServices[$id]);
	}

	public function publish($value)
	{
		return new PublicService($value);
	}


	public function offsetGet($id)
	{
		if ($this->isPublicScope() && false === $this->isServicePublished($id))
		{
			throw new PrivateScopeViolationException($id);
		}

		$scope = $this->enterPrivateScope();

		$service = parent::offsetGet($id);

		$this->leavePrivateScope($scope);

		return $service;
	}

	private function enterPrivateScope()
	{
		$current = $this->currentScope;

		$this->currentScope = self::SCOPE_PRIVATE;

		return $current;
	}

	private function leavePrivateScope($previous)
	{
		$this->currentScope = $previous;
	}


	private function isDefaultNewServicesPublic()
	{
		return self::SCOPE_PUBLIC === $this->defaultScope;
	}

	private function isPublicScope()
	{
		return self::SCOPE_PUBLIC === $this->currentScope;
	}
}