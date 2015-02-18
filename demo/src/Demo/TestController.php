<?php
/**
 * This file is part of the CarteBlanche PHP framework.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Demo;

use \MVCFundamental\Interfaces\ControllerInterface;
use \MVCFundamental\Interfaces\FrontControllerInterface;
use \MVCFundamental\Interfaces\RequestInterface;
use \MVCFundamental\Interfaces\ResponseInterface;

class TestController
    implements ControllerInterface
{

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

}

// Endfile