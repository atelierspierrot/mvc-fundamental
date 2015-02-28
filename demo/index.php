<?php
/**
 * @see <http://github.com/atelierspierrot/mvc-fundamental>.
 */

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
$classLoader = new SplClassLoader("Demo", __DIR__.'/src/', SplClassLoader::FAIL_GRACEFULLY);
$classLoader->register();

// ----------------------------------
// FrontController usage
// ----------------------------------

/**
 * @TODOS
 * - security
 * - multiple templates / actions rendering in one page
 */


// front controller creation
$fctrl = \MVCFundamental\FrontController::getInstance(array(

    'mode'                      => 'dev',

    'default_controller_name'   => '\Demo\DefaultController',

    'controller_locator'        => function($name) {
        $class = '\Demo\\'.ucfirst($name).'Controller';
        return (class_exists($class) ? $class : null);
    },

    // uncomment the following to handle error as an exception
//    'convert_error_to_exception'=> true,

));

// load some predefined settings
$helper = new \Demo\TestHelper;

// test a log message
\MVCFundamental\AppKernel::log('info', 'Test log for request '.$fctrl->get('request')->getUri());

// routing definitions
$fctrl

    // the default welcome page
    ->addRoute('/', function () use ($fctrl) {
        $req = $fctrl->get('request')->getUrl();
        return $fctrl->get('template_engine')->renderDefault(
            <<<MESAGE
<p><strong>Welcome in the demo!</strong> You will find below various routes to test the package.</p>
<p>To learn how these routes are defined and a quick "how-to", have a look in the source code of file <code>demo/index.php</code>.</p>
<p>To get last source updates or report a bug, see <a href="http://github.com/atelierspierrot/mvc-fundamental">the "atelierspierrot/mvc-fundamental" repository</a>.</p>
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
    <li><a href="{$req}hello/your-name/id/4/alt">/hello/{name}/id/{id}/alt</a> : a route with a "name" string argument and a "id" integer one fetched in random order</li>
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
    <li><a href="{$req}test/event1">/test/event1</a> : test of event management triggering <code>event.1</code></li>
    <li><a href="{$req}test/event2">/test/event2</a> : test of event management triggering <code>event.2</code></li>
    <li><a href="{$req}test/json">/test/json</a> : test of a JSON response</li>
</ul>
<p>Various types of templating or aggregation composition:</p>
<ul>
    <li><a href="{$req}/compose_parts">/compose_parts</a> : content composed by aggregating different parts</li>
    <li><a href="{$req}/compose_layout">/compose_layout</a> : content composed using a Layout object</li>
    <li><a href="{$req}/default_layout">/default_layout</a> : test of the default layout (with lorem ipsum contents)</li>
    <li><a href="{$req}/callcontrolleraction">/callcontrolleraction</a> : call another controller's action</li>
    <li><a href="{$req}/callroute">/callroute</a> : call another route</li>
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
<p>Use the following links to debug:</p>
<ul>
    <li><a href="{$req}debug">/debug</a> : route to see a dump of the front controller</li>
    <li><a href="{$req}logs">/logs</a> : route to see log files contents</li>
</ul>
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

    // a simple test route with a 'name' argument as string and an 'id' argument as integer in random order
    ->addRoute('/hello/{name}/id/{id:\d+}/alt', 'altargs')

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
        \MVCFundamental\Commons\Helper::debug(
            \MVCFundamental\AppKernel::getInstance()
        );
    })

    // a "log" route to see logs
    ->addRoute('/logs', function () use ($fctrl) {
        $log_dir = $fctrl->getOption('temp_dir');
        $dh  = opendir($log_dir);
        $logs = array();
        $logs[] = 'LOGS DIR : '.$log_dir;
        while (false !== ($filename = readdir($dh))) {
            if (substr($filename, -4)=='.log') {
                $logs[] = '##### '.$filename;
                $logs[] = file_get_contents($log_dir.'/'.$filename);
            }
        }
        call_user_func_array(array('MVCFundamental\Commons\Helper', 'debug'), $logs);
    })

    // event listening
    ->on('event.1', array('Demo\DefaultController', 'eventHandler'))

    // trigger an event
    ->addRoute('/test/event2', function () use ($fctrl) {
        $fctrl->trigger('event.2');
        return 'Event 2 was triggered';
    })

    // app run
    ->run()
;

// we should never come here
header('Content-Type: text/plain');
echo PHP_EOL;
var_export($fctrl);
