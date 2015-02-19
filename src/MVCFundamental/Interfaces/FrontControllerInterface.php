<?php
/**
 * This file is part of the MVC-Fundamental package.
 *
 * Copyleft (ↄ) 2013-2015 Pierre Cassat <me@e-piwi.fr> and contributors
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
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