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

use \MVCFundamental\Commons\Helper;
use \MVCFundamental\FrontController;
use \MVCFundamental\Interfaces\LocatorInterface;

/**
 * Class Locator
 *
 * @author  piwi <me@e-piwi.fr>
 */
class Locator
    implements LocatorInterface
{

    /**
     * @param   string  $name
     * @return  mixed
     */
    public function locateController($name)
    {
        // case $name is a real class name
        if (is_object($name) || class_exists($name)) {
            return $name;
        }

        // case $name is a short name
        $mask = FrontController::getInstance()->getOption('controller_name_finder');
        $full_name = sprintf($mask, Helper::getPropertyName($name));
        if (class_exists($full_name)) {
            return $full_name;
        }

        // a user defined locator
        $locator = FrontController::getInstance()->getOption('controller_locator');
        if (!is_null($locator) && is_callable($locator)) {
            $result = call_user_func($locator, $name);
            if (!empty($result) && class_exists($result)) {
                return $result;
            }
        }

        // loop over all defined classes
        $n_length   = strlen($name);
        $fn_length  = strlen($full_name);
        foreach (get_declared_classes() as $classname) {
            if (
                substr($classname, -$n_length)==$name ||
                substr($classname, -$fn_length)==$full_name
            ) {
                return $classname;
            }
        }

        return null;
    }

    /**
     * @param   string  $name
     * @param   string|\MVCFundamental\Interfaces\ControllerInterface  $controller
     * @return  mixed
     */
    public function locateControllerAction($name, $controller)
    {
        $controller = $this->locateController($controller);
        if (!is_null($controller)) {
            if (method_exists($controller, $name)) {
                return $name;
            }

            $mask = FrontController::getInstance()->getOption('action_name_finder');
            $full_name = sprintf($mask, Helper::getPropertyName($name, '_', false));
            if (method_exists($controller, $full_name)) {
                return $full_name;
            }
        }
        return null;
    }

    /**
     * @param   string $name
     * @return  mixed
     */
    public function locateTemplate($name)
    {
        if (empty($name)) {
            return null;
        }

        if (file_exists($name)) {
            return realpath($name);
        }

        // a user defined locator
        $locator = FrontController::getInstance()->getOption('view_file_locator');
        if (!is_null($locator) && is_callable($locator)) {
            $result = call_user_func($locator, $name);
            if (!empty($result) && file_exists($result)) {
                return $result;
            }
        }

        // in package's resources
        $f = __DIR__.'/../Resources/templates/'.$name;
        if (file_exists($f)) {
            return realpath($f);
        }

        return null;
    }

}

// Endfile