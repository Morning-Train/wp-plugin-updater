# Morningtrain\WP\REST

Work easily with the WP REST API

## Getting started

To get started with the module simply construct an instance of `\Morningtrain\WP\Rest\Module()` and pass it to the `addModule()` method on your project instance.

### Example

```php
// functions.php
require __DIR__ . "/vendor/autoload.php";

use Morningtrain\WP\Core\Theme;

Theme::init();

// Add our module
Theme::getInstance()->addModule(new \Morningtrain\WP\Rest\Module());
```

## Create a Rest Route
Rest routes can be created by extending `Morningtrain\WP\Rest\Abstracts\RestRouteAbstract` and create a `register` method, and place it inside the `RestRoute/{VERSION}` folder.

### Example
```php
// RestRoutes/v1/Test.php

use Morningtrain\WP\Rest\Abstracts\RestRouteAbstract;

class Test extends RestRouteAbstract {

    public static string $version = 'v1';
    public static string $namespace = 'test-api';
    public static string $resource_name = 'test';

    public static function register($args) {
        static::registerRestRoute(array(
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [static::class, 'get'],
                'permission_callback' => '__return_true'
            )
        ));
    }
    
    public static function get(\WP_REST_Request $request) : \WP_REST_Response {
        return new \WP_REST_Response(array(
		    'test' => 'testing...'
		), 200);
    }
}
```
