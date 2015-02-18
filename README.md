PHP MVC Fundamental
===================

An easy-to-use and light-weight (around 1Mo with default dependencies) 
MVC system to build simple web-apps.


## Installation

Add the package to your [Composer](http://getcomposer.org/) requirements:

    "require": {
        ...
        "atelierspierrot/mvc-fundamental": "dev-master"
    }


## Usage

The idea of this package is to build a simple web-app easily and quickly, 
with as few lines of code as possible on a robust basic architecture.

The default system embeds all basic "MVC web-app" objects: a `FrontController` 
receives a `Request` and ask to a `Router` the action to launch matching a
specific `Route` ; the action is often a `Controller`'s method (but not necessary)
that generates an output using a `Template` ; finally, the `FrontController` 
returns a `Response`.

All of these objects are overwritable but the whole application is designed
to use some API interfaces defined in the `\MVCFundamental\Interfaces` namespace.
Your custom objects MUST implement one of these interfaces (a good practice could
be to extend the default objects).

## Quick start

### The front controller

The entry point is to create a `FrontController` object with a set of options
(if wanted):

```php
$app = \MVCFundamental\FrontController::getInstance( array $options );
```

See the [options](#app-options) section below for a full review of possible
options.

The front controller creates an `AppKernel` instance to handle all the app
core logic such a dependencies container, the error management etc. Some 
shortcut aliases are defined in the front controller itself to let you 
retrieve a dependency using:

```php
$route = $app->get( 'router' );
$route = $app->set( 'request' , new Request );
```

### Routing

Your real application logic resides in the *routes* you define and corresponding
callbacks. The internal `Router` will try to fetch the correct callback according
to the request and your routes definition, or with an automatic system to fetch a 
controller's action.

To define a route, you can use:

```php
// direct callback
$app->addRoute('/my-route', function(){ return 'This is my route content';  })

// to use the controller, write
$app->addRoute('/my-route', function() use ($app){ return 'This is my route content';  })

// a controller's method - this will try to call MycontrollerController::methodAction()
$app->addRoute('/my-route', array( mycontroller , method ))

// a method of the default controller if so
$app->addRoute('/my-route', 'method')

// a direct view file path - the path must exists
$app->addRoute('/my-route', 'my-view-file.html')
```

The automatic system will try to find a match of current URI first parts as a `array( controller , method )`
correspondence, or as a simple `method` of the default controller:

-   a route like `/mycontroller/mymethod` will call `MycontrollerController::mymethodAction()`
-   a route like `/myaction` will call `DefaultController::myactionAction()`.

For each kind of route (defined or automatic), any URI arguments will be fetched as callback
parameters. For instance, a route like `/mycontroller/myaction/name/my-name` will call
`MycontrollerController::myactionAction( 'my-name' )`. You can "ask" for a parameter to
be kept from a route writing:

```php
// fetch a $name argument as a string (default)
$app->addRoute('/my-route/{name}', function($name){ return "Hello $name";  })

// fetch a $name argument as a string (default) and an ID argument as an integer
$app->addRoute('/my-route/{name}/{id:\d+}', function($name, $id){ return "Hello $name, I got ID $id";  })
```

As the callback's arguments are rearranged, you can write them in any order:

```php
// arguments order usage does not matter
$app->addRoute('/my-route/{name}/{id:\d+}', function($id, $name){ return "Hello $name, I got ID $id";  })
```

You can always use the three arguments below if necessary:

-   the `FrontController` itself as `\MVCFundamental\Interfaces\FrontControllerInterface $app`
-   the `Request` as `\MVCFundamental\Interfaces\RequestInterface $request`
-   the `Response` as `\MVCFundamental\Interfaces\ResponseInterface $response`
-   the `TemplateEngine` as `\MVCFundamental\Interfaces\TemplateEngineInterface $template_engine`
-   a `data` array with all request arguments as `array $data`

```php
$app->addRoute('/my-route', function($request, $response, $app){ return "Hello world";  })
```

### Callback return

A routing callback can return various types of things:

-   a simple string, that will be used as the final response content
-   an array like `array ( string $view_file , array $view_parameters )` to construct the
    final content calling the `$view_file` template with the `$view_parameters` arguments
-   a full `\MVCFundamental\Interfaces\ResponseInterface` object to replace actual response.

Inside a callback, you can:

-   render as many templates as required using:

```php
$app->render( $view_file, array $params = array() ) : string
```

-   "include" another controller's method using:

```php
$app->callControllerAction( $controller = null, $action = null, array $arguments = array() ) : string
```

-   call another route using:

```php
$app->callRoute( $route, array $arguments = array(), $method = 'get' ) : string
```

-   make a redirection or a forward to a new route using:

```php
$app->redirect( $route, $follow = false ) : string
```

### Templating system

The templates construction is handled by the `TemplateEngine` object that creates and aggregates
some instances of `Template`.

```php
$template_engine = $app->get( 'template_engine' ) : object

$new_template = $template_engine->renderTemplate( $view_file , array $arguments ) : string
```

The template engine works in couple with two kind of objects: the simple `Template` and
the `Layout`. A `Template` is a simple view file included with parameters while a `Layout`
is a kind of "full page" canvas handling predefined page parts, its `child` which can default
to a specific template file, and can be overwritten in the layout object.

### Error & exceptions

The package embeds a (quite) full set of custom exceptions in the `MVCFundamental\Exceptions`
namespace and an internal handler to handle them. Try to always use one of these
objects when you throw an exception. If you have a doubt, a shortcut can be used writing:

```php
$app->error( $message, $status = 500 )
```

### App options

The following options can be defined when constructing the front controller to
build a custom application:

```php
$options = array(

    // all these will overwrite the default app objects
    'router'                    => '\MVCFundamental\Basic\Router',
    'route_item'                => '\MVCFundamental\Basic\Route',
    'response'                  => '\MVCFundamental\Basic\Response',
    'request'                   => '\MVCFundamental\Basic\Request',
    'template_engine'           => '\MVCFundamental\Basic\TemplateEngine',
    'template_item'             => '\MVCFundamental\Basic\Template',
    'locator'                   => '\MVCFundamental\Basic\Locator',
    'error_controller'          => '\MVCFundamental\Basic\ErrorController',

    // this can be a callback to retrieve a controller class: function ($name) {}
    'controller_locator'        => null,

    // the controllers name mask
    'controller_name_finder'    => '%sController',

    // the actions name mask
    'action_name_finder'        => '%sAction',

    // name of the default controller
    'default_controller_name'   => 'default',

    // name of the default controller's action
    'default_action_name'       => 'index',

    // the default response content type
    'default_content_type'      => 'html',

    // the default response charset
    'default_charset'           => 'utf8',

    // set to `true` to transform errors in exceptions (with app rendering)
    'convert_error_to_exception'=> false,

    // the routes definition array
    'routes'                    => array(),

);
```

### API

When the system boots, all the following required components are created and stored 
in the container:

-   the **router**, which must implement the `\MVCFundamental\Interfaces\RouterInterface`
-   the **request**, which must implement the `\MVCFundamental\Interfaces\RequestInterface`
-   the **response**, which must implement the `\MVCFundamental\Interfaces\ResponseInterface`
-   the **template_engine**, which must implement the `\MVCFundamental\Interfaces\TemplateInterface`
-   the **locator**, which must implement the `\MVCFundamental\Interfaces\LocatorInterface`
-   the **error_controller**, which must implement the `\MVCFundamental\Interfaces\ErrorControllerInterface`

More, any controller must implement the `\MVCFundamental\Interfaces\ControllerInterface`
and the router must handle a collection of routes implementing the `\MVCFundamental\Interfaces\RouteInterface`.

They all default to their implementation in the `\MVCFundamental\Basic` namespace
but you can overwrite all of them.


## Author & License

>    PHP MVC Fundamental

>    http://github.com/atelierspierrot/mvc-fundamental

>    Copyleft (ↄ) 2013-2015 Pierre Cassat and contributors

>    Licensed under the GPL Version 3 license.

>    http://opensource.org/licenses/GPL-3.0

>    ----

>    Les Ateliers Pierrot - Paris, France

>    <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
