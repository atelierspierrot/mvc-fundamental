<?php
/**
 * This file is part of the MVC-Fundamental package.
 *
 * Copyright (c) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * The source code of this package is available online at
 * <http://github.com/atelierspierrot/mvc-fundamental>.
 */

namespace MVCFundamental\Interfaces;

use \Patterns\Interfaces\SingletonInterface;
use \Patterns\Interfaces\OptionableInterface;

/**
 * Interface FrontControllerInterface
 */
interface FrontControllerInterface
    extends SingletonInterface, OptionableInterface, ServiceContainerProviderInterface
{

    /**
     * @param string $mode
     * @return bool
     */
    public function isMode($mode);

    /**
     * @return void
     */
    public function run();

    /**
     * @return void
     */
    public function display();

    /**
     * @param string $message
     * @param int $status
     * @param int $code
     * @param string $filename
     * @param int $lineno
     * @return void
     */
    public function error($message, $status = 500, $code = 0, $filename = __FILE__, $lineno = __LINE__);

    /**
     * @param $view_file
     * @param array $params
     * @return string
     */
    public function render($view_file, array $params = array());

    /**
     * @param string $route
     * @param callable $callback
     * @param string $method
     * @return void
     */
    public function addRoute($route, $callback, $method = 'get');

    /**
     * @param string $route
     * @param array $arguments
     * @param string $method
     * @return string
     */
    public function callRoute($route, array $arguments = array(), $method = 'get');

    /**
     * @param null|string $controller
     * @param null|string $action
     * @param array $arguments
     * @return string
     */
    public function callControllerAction($controller = null, $action = null, array $arguments = array());

    /**
     * @param string $url
     * @param bool $follow
     * @return void
     */
    public function redirect($url, $follow = false);

}

// Endfile