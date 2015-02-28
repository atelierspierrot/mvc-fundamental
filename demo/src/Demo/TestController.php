<?php
/**
 * @see <http://github.com/atelierspierrot/mvc-fundamental>.
 */

namespace Demo;

use \MVCFundamental\Interfaces\ControllerInterface;
use \MVCFundamental\Interfaces\FrontControllerInterface;
use \MVCFundamental\Interfaces\RequestInterface;
use \MVCFundamental\Interfaces\ResponseInterface;
use \MVCFundamental\Interfaces\EventInterface;

class TestController
    implements ControllerInterface
{

    public static function eventHandler(EventInterface $event)
    {
        echo "> event handler from ".__METHOD__." treating event ".$event->getName().PHP_EOL.'<br />';
        $name = @$event->name;
        if ($name) {
            echo "> getting the 'name' property from the event: ".$event->name.PHP_EOL.'<br />';
        }
    }

    public function testAction($name = null)
    {
        return 'Hello '.$name.' (from '.__METHOD__.')';
    }

    public function mymethodAction()
    {
        return 'Yeah! This method was called auto-magically ;)';
    }

    public function namemethodAction($name = 'Anonymous')
    {
        return 'Yeah! This method was called auto-magically ;)<br />'
            .'I received name='.$name;
    }

    public function argsMethod($name = 'Anonymous', RequestInterface $request, ResponseInterface $response)
    {
        return 'Call of '.__CLASS__.'::'.__METHOD__.'<br/>'
            .'I received arguments:<br />'
            .var_export(func_get_args(),1);
    }

    public function altargsMethod($name = 'Anonymous', RequestInterface $req, ResponseInterface $resp)
    {
        return 'Call of '.__CLASS__.'::'.__METHOD__.'<br/>'
        .'I received arguments:<br />'
        .var_export(func_get_args(),1);
    }

    public function forwardingAction(FrontControllerInterface $app)
    {
        $app->redirect('/test/forwarding_target');
    }

    public function forwardingTargetAction()
    {
        return 'This is the rendering of method '.__METHOD__;
    }

    public function redirectingAction(FrontControllerInterface $app)
    {
        $app->redirect('/test/redirecting_target', true);
    }

    public function redirectingTargetAction()
    {
        return 'This is the rendering of method '.__METHOD__;
    }

    public function event1Action(FrontControllerInterface $app)
    {
        $app->trigger('event.1', new \Demo\TestObservable());
        return 'Event 1 was triggered';
    }

    public function jsonAction(FrontControllerInterface $app)
    {
        $response = $app->get('response');
        $response
            ->setContentType('json')
            ->setContents(array("var"=>"val"))
        ;
        return $response;
    }

}

// Endfile