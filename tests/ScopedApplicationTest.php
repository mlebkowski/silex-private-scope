<?php

use Nassau\Silex\ScopedApplication;
use Silex\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ScopedApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function testInternalServicesArePublic()
	{
		$application = new ScopedApplication();

		$kernel = $application['kernel'];

		$this->assertInstanceOf(HttpKernelInterface::class, $kernel);
	}


	/**
	 * @expectedException Nassau\Silex\PrivateScopeViolationException
	 * @expectedExceptionMessage You cannot access private service `service`
	 */
	public function testDefaultServiceCantBeRetrieved()
	{

		$application = new ScopedApplication();

		$application['service'] = new StdClass;

		$application->boot();

		$application['service'];

	}

	public function testApplicationRecognizesPublicServices()
	{

		$application = new ScopedApplication();

		$application['public-service'] = $application->publish(new \StdClass);

		$this->assertInstanceOf(\StdClass::class, $application['public-service']);

	}

	public function testPrivateServicesAreAvailableFromOtherServices()
	{
		$appplication = new ScopedApplication();

		$privateService = new StdClass;

		$appplication['public-service'] = $appplication->publish(function (Application $application)
		{
			return $application['private-service'];
		});

		$appplication['private-service'] = $privateService;

		$result = $appplication['public-service'];

		$this->assertSame($privateService, $result);
	}

	/**
	 * @expectedException Nassau\Silex\PrivateScopeViolationException
	 */
	public function testUnsettingClearsScope()
	{
		$appplication = new ScopedApplication();

		$appplication['public-service'] = $appplication->publish(null);

		$appplication->offsetUnset('public-service');

		$appplication['public-service'] = null; // not so public any more!

		$appplication['public-service'];
	}

}
