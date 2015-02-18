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

namespace MVCFundamental\Basic;

use \MVCFundamental\Exception\AccessForbiddenException;
use \MVCFundamental\Interfaces\RouterInterface;
use \MVCFundamental\Interfaces\RouteInterface;
use \Patterns\Commons\Collection;

/**
 * Class Router
 */
class Router
    implements RouterInterface
{

    /**
     * @var \Patterns\Commons\Collection
     */
    protected $_collection;

    /**
     *
     */
    public function __construct()
    {
        $this->_collection = new Collection();
    }

    /**
     * @param string $uri
     * @param array $arguments
     * @param string $method
     * @return array ( callback , parameters )
     * @throws AccessForbiddenException
     */
    public function distribute($uri, array $arguments = array(), $method = 'get')
    {
        // strip double slashes
        $uri        = str_replace('//', '/', $uri);

        // strip query arguments
        if (false!==($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }

        // routes matching
        foreach ($this->_collection as $route) {
            if (false!==preg_match('~^'.$route->getMatch().'$~', $uri, $uri_matches) && count($uri_matches)>0) {
                $callback = $route->getCallback();
                foreach ($uri_matches as $var=>$val) {
                    if (is_string($var)) {
                        $arguments[$var] = $val;
                    }
                }
                if ($route->getMethod()!=='get' && $route->getMethod()!==strtolower($method)) {
                    throw new AccessForbiddenException('Wrong data type!');
                }
                return array($callback, $arguments);
            }
        }

        // automatic routing if uri is not empty
        if (strlen($uri)>1) {

            // case controller/method
            if (strpos($uri, '/', 1)!==false) {
                $parts = explode('/', substr($uri, 1));
                $callback = array($parts[0], $parts[1]);
                array_shift($parts);
                array_shift($parts);
                $index      = null;
                foreach ($parts as $item) {
                    if (is_null($index)) {
                        $index = $item;
                    } else {
                        $arguments[$index] = $item;
                        $index = null;
                    }
                }
                return array($callback, $arguments);

            // case method only
            } else {
                return array(substr($uri, 1), $arguments);
            }
        }

        return null;
    }

    /**
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    /**
     * @param \MVCFundamental\Interfaces\RouteInterface $route
     * @return $this
     */
    public function addRoute(RouteInterface $route)
    {
        $this->_collection[] = $route;
        return $this;
    }

}

// Endfile