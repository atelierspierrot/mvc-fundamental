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

use \MVCFundamental\Interfaces\RouteInterface;

/**
 * Class Route
 *
 * @author  piwi <me@e-piwi.fr>
 */
class Route
    implements RouteInterface
{

    /**
     * @var string
     */
    protected $_route;

    /**
     * @var string
     */
    protected $_match;

    /**
     * @var callable
     */
    protected $_callback;

    /**
     * @var string
     */
    protected $_method;

    /**
     * @param string $route
     * @param mixed $callback
     * @param string $method
     */
    public function __construct($route, $callback, $method = 'get')
    {
        $this->_route       = $route;
        $this->_callback    = $callback;
        $this->_method      = $method;
        $this->_prepare();
    }

    /**
     * @return void
     */
    protected function _prepare()
    {
        $this->_match = $this->_route;
        if (false!==preg_match_all('~{([^}]+)}~', $this->_route, $matches) && count($matches)>1 && !empty($matches[1])) {
            foreach ($matches[1] as $i=>$item) {
                if (false!==($pos = strpos($item, ':'))) {
                    @list($name, $mask) = explode(':', $item, 2);
                    $this->_match = str_replace('{'.$item.'}', '(?<'.$name.'>'.$mask.')', $this->_match);
                } else {
                    $this->_match = str_replace('{'.$item.'}', '(?<'.$item.'>[^/]+)', $this->_match);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->_route;
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->_match;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

}

// Endfile