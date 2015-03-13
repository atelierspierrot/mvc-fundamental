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

namespace MVCFundamental\Commons;

use \MVCFundamental\FrontController;
use \Library\Helper\Code as CodeHelper;
use \Library\Helper\Text as TextHelper;

/**
 * This is the global app Helper
 *
 * @author  piwi <me@e-piwi.fr>
 */
class Helper
{

    /**
     * Get the default environment vars to load in views and calling controller's actions
     * @return array
     */
    public static function getDefaultEnvParameters()
    {
        return array(
            'app'               => FrontController::getInstance(),
            'template_engine'   => FrontController::get('template_engine'),
            'request'           => FrontController::get('request'),
            'response'          => FrontController::get('response'),
            'data'              => FrontController::get('request')->getArguments(),
        );
    }

    /**
     * Test if a class implements an interface
     * @param $class_name
     * @param $interface_name
     * @return bool
     */
    public static function classImplements($class_name, $interface_name)
    {
        if (empty($interface_name) || !interface_exists($interface_name)) {
            return false;
        }
        return CodeHelper::implementsInterface($class_name, $interface_name);
    }

    /**
     * Get a safe class property name in camel case
     * @param string $name
     * @param string $replace
     * @param bool $capitalize_first_char
     * @return string
     */
    public static function getPropertyName($name = '', $replace = '_', $capitalize_first_char = true)
    {
        return TextHelper::toCamelCase($name, $replace, $capitalize_first_char);
    }

    /**
     * Get a safe class method name in camel case
     * @param $property_name
     * @return string
     */
    public static function getMethodName($property_name)
    {
        return CodeHelper::getPropertyMethodName($property_name);
    }

    /**
     * Call a callback fetching organized arguments depending on its declaration
     * @param null $method_name
     * @param null $arguments
     * @param null $class_name
     * @return mixed
     */
    public static function fetchArguments($method_name = null, $arguments = null, $class_name = null)
    {
        return CodeHelper::fetchArguments($method_name, $arguments, $class_name);
    }

// --------------------------
// Development utilities
// --------------------------

    /**
     * Hard debug anything
     */
    public static function debug()
    {
        header('Content-Type: text/plain');
        foreach (func_get_args() as $arg) {
            var_export($arg);
            echo PHP_EOL;
        }
        exit('-- debug --');
    }

    /**
     * Get a safe exception info
     * @param \Exception $e
     * @param bool $plain_text
     */
    public static function exceptionHandler(\Exception $e, $plain_text = false)
    {
        $nl     = $plain_text ? PHP_EOL : '<br />';
        $otag   = $plain_text ? '##'.PHP_EOL : '<pre>';
        $ctag   = $plain_text ? '##'.PHP_EOL : '</pre>';
        echo $otag,
            'ERROR : ', get_class($e), $nl,
            'Message: ', $e->getMessage(), ' (code ', $e->getCode(), ')', $nl,
            'File: ', $e->getFile(), ':', $e->getLine(), $nl,
            'Back trace:', $nl, $e->getTraceAsString(), $nl,
            $ctag;
    }

}

// Endfile