# Private scope for Silex container

Create an instance of `ScopedApplication` to hide your services by default:

```php
$app = new Nassau\Silex\ScopedApplication([
    'version' => 1.0,
]);

$app['private-service'] = function () { };
$app['version']; // 1.0; services are registered as public if passed to constructor

$app['private-service']; // throws \Nassau\Silex\PrivateScopeViolationException
```

Create a public service by using `publish()` method. Private services will be available from inside the closure:

```php
$app = new Nassau\Silex\ScopedApplication;
$app['public-service'] = $app->publish(function (Silex\Application $app) { 
    return $app['private-service'];
});
$app['private-service'] = function () { return "private" }

$app['public-service']; // "private";
```
