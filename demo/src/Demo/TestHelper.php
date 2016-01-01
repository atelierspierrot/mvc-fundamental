<?php
/**
 * Created by PhpStorm.
 * User: pierrecassat
 * Date: 22/02/15
 * Time: 19:22
 */

namespace Demo;

use \MVCFundamental\FrontController;

class TestHelper
{

    public function __construct()
    {
        FrontController::getInstance()
            ->on('event.2', array('Demo\DefaultController', 'eventHandler'))
            ->on('event.1', array('Demo\TestController', 'eventHandler'))
        ;
    }
}
