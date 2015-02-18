<?php

// ----------------------------------
// Env setup
// ----------------------------------

// Show errors at least initially
ini_set('display_errors','1'); error_reporting(-1);

// Set a default timezone to avoid PHP5 warnings
$dtmz = @date_default_timezone_get();
date_default_timezone_set($dtmz?:'Europe/Paris');

// required composer autoloader
if (file_exists($autoloader = __DIR__.'/../vendor/autoload.php')) {
    require_once $autoloader;
} else {
    die("Composer autoloader '$autoloader' not found!'");
}

// add the Demo namespace
if (file_exists($splloader = __DIR__.'/../src/SplClassLoader.php')) {
    require_once $splloader;
} else {
    die("SPL autoloader '$splloader' not found!'");
}
$classLoader = new SplClassLoader("Demo", __DIR__."/src");
$classLoader->register();

// ----------------------------------
// FrontController usage
// ----------------------------------

/**
 * @TODOS
 * - security
 * - organize arguments by declared class name
 * - multiple templates / actions rendering in one page
 */


// front controller creation
$fctrl = \MVCFundamental\FrontController::getInstance(array(

    'default_controller_name'   => '\Demo\DefaultController',

    'controller_locator'        => function($name) {
        $class = '\Demo\\'.ucfirst($name).'Controller';
        return (class_exists($class) ? $class : null);
    },

    // uncomment the following to handle error as an exception
//    'convert_error_to_exception'=> true,

));

// routing definitions
$fctrl

    // the default welcome page
    ->addRoute('/', function () use ($fctrl) {
        $req = $fctrl->get('request')->getUrl();
        return $fctrl->get('template_engine')->renderDefault(
            <<<MESAGE
<p><strong>Welcome in the test!</strong></p>
<p>Calls of various routes and callbacks:</p>
<ul>
    <li><a href="{$req}hello">/hello</a> : simple closure callback with no argument</li>
    <li><a href="{$req}hello/your-name">/hello/{name}</a> : simple closure callback with a "name" argument</li>
    <li><a href="{$req}hello/your-name/controller">/hello/{name}/controller</a> : a controller default action call with a "name" argument</li>
    <li><a href="{$req}hello/your-name/method">/hello/{name}/method</a> : a custom method of the default controller call with a "name" argument</li>
    <li><a href="{$req}hello/your-name/view">/hello/{name}/view</a> : a direct "view" file inclusion with a "name" argument</li>
    <li><a href="{$req}hello/your-name/myview">/hello/{name}/myview</a> : a controller action loading a "view" file with a "name" argument</li>
    <li><a href="{$req}hello/your-name/test">/hello/{name}/test</a> : a custom action of a custom controller with a "name" argument</li>
    <li><a href="{$req}hello/your-name/id/4">/hello/{name}/id/{id}</a> : a route with a "name" string argument and a "id" integer one</li>
</ul>
<p>Tests of internal features:</p>
<ul>
    <li><a href="{$req}test/mymethod">/test/mymethod</a> : an undefined route to test the automatic routing - must call <var>TestController::mymethodAction()</var></li>
    <li><a href="{$req}test/namemethod/name/my-name">/test/mymethod/name/{name}</a> : an undefined route to test the arguments management of automatic routing</li>
    <li><a href="{$req}test/namemethod?name=my-name">/test/mymethod?name={name}</a> : same test as above with query arguments</li>
    <li><a href="{$req}test/argsmethod">/test/argsmethod</a> : test automatic arguments passed to a controller's action</li>
    <li><a href="{$req}test/altargsmethod">/test/altargsmethod</a> : test automatic arguments passed to a controller's action with custom arguments names</li>
    <li><a href="{$req}test/forwarding">/test/forwarding</a> : test automatic forwarding - should show result of method <var>TestController::forwardingTargetAction()</var></li>
    <li><a href="{$req}test/redirecting">/test/redirecting</a> : test automatic redirecting - should end on route <var>/test/redirectTarget</var></li>
</ul>
<p>Various types of templating composition:</p>
<ul>
    <li><a href="{$req}/compose_parts">/compose_parts</a> : content composed by aggregating different parts</li>
    <li><a href="{$req}/compose_layout">/compose_layout</a> : content composed using a Layout object</li>
</ul>
<p>Tests of internal error handling:</p>
<ul>
    <li><a href="{$req}error">/error</a> : test the error page (requires to enable the <code>convert_error_to_exception</code> option)</li>
    <li><a href="{$req}exception">/exception</a> : test the exception error page</li>
    <li><a href="{$req}notfoundexception">/notfoundexception</a> : a "page not found" test</li>
    <li><a href="{$req}accessforbiddenexception">/accessforbiddenexception</a> : a "forbidden access" test</li>
    <li><a href="{$req}servererror">/servererror</a> : an "internal server error" test</li>
    <li><a href="{$req}twoexceptions">/twoexceptions</a> : a test of multiple errors</li>
</ul>
<p>Test of a form page:</p>
<ul>
    <li><a href="{$req}form">/form</a> : test form page itself</li>
    <li><a href="{$req}saveform">/saveform</a> : the submission page, that should throw an error while calling it directly</li>
</ul>
<p>At any time, use the <a href="{$req}debug">/debug</a> route to see a dump of the front controller.</p>
MESAGE
            , 'Hello'
        );
    })

    // a simple test route
    ->addRoute('/hello', function () use ($fctrl) {
        return 'Hello anonymous';
    })

    // a simple test route with a 'name' argument
    ->addRoute('/hello/{name}', function ($name) use ($fctrl) {
        return 'Hello '.$name;
    })

    // a simple test route with a 'name' argument calling the default action of the 'Demo\DefaultController'
    ->addRoute('/hello/{name}/controller', 'Demo\DefaultController')

    // a simple test route with a 'name' argument calling the ''mytest' action of the default controller
    ->addRoute('/hello/{name}/method', 'mytest')

    // a simple test route with a 'name' argument loading a view file
    ->addRoute('/hello/{name}/view', 'src/templates/test.php')

    // a simple test route with a 'name' argument calling a controller action loading a view file
    ->addRoute('/hello/{name}/myview', 'myview')

    // a simple test route with a 'name' argument as string and an 'id' argument as integer
    ->addRoute('/hello/{name}/id/{id:\d+}', 'args')

    // a simple test route with a 'name' argument calling an array like (controller, action)
    ->addRoute('/hello/{name}/test', array('\Demo\TestController', 'test'))

    // a simple form handling with a save action requiring a POST method
    ->addRoute('/form', 'form')
    ->addRoute('/saveform', 'saveForm', 'post')

    // an error test
    ->addRoute('/error', function () use ($fctrl) {
        trigger_error('a user test error ...', E_USER_ERROR);
    })

    // an exception test
    ->addRoute('/exception', function () use ($fctrl) {
        throw new \Exception('a user test exception ...');
    })

    // a "not found" exception test
    ->addRoute('/notfoundexception', function () use ($fctrl) {
        throw new \MVCFundamental\Exception\NotFoundException('a user test exception ...');
    })

    // an "access forbidden" exception test
    ->addRoute('/accessforbiddenexception', function () use ($fctrl) {
        throw new \MVCFundamental\Exception\AccessForbiddenException('a user test exception ...');
    })

    // an "internal server error" exception test
    ->addRoute('/servererror', function () use ($fctrl) {
        throw new \MVCFundamental\Exception\InternalServerErrorException('a user test exception ...');
    })

    // a two exceptions test
    ->addRoute('/twoexceptions', function () use ($fctrl) {
        throw new \MVCFundamental\Exception\InternalServerErrorException(
            'a user test exception ...', 1, new \MVCFundamental\Exception\Exception(
                'a secondary (previous) exception ...'
            )
        );
    })

    // a "debug" route to see a dump of the front controller
    ->addRoute('/debug', function () use ($fctrl) {
        header('Content-Type: text/plain');
        echo PHP_EOL;
        var_export(\MVCFundamental\AppKernel::getInstance());
        exit();
    })


    // app run
    ->run()
;

// we should never come here
header('Content-Type: text/plain');
echo PHP_EOL;
var_export($fctrl);
