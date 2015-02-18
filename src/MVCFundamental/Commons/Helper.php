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

class Helper
{

    public static function debug()
    {
        header('Content-Type: text/plain');
        foreach (func_get_args() as $arg) {
            var_export($arg);
            echo PHP_EOL;
        }
        exit('-- debug --');
    }

    public static function getDefaultEnvParameters()
    {
        return array(
            'app'               => FrontController::getInstance(),
            'template_engine'   => FrontController::get('template_engine'),
            'request'           => FrontController::get('request'),
            'response'          => FrontController::get('response'),
        );
    }

    public static function classImplements($class_name, $interface_name)
    {
        return CodeHelper::implementsInterface($class_name, $interface_name);
    }

    public static function getPropertyName($name = '', $replace = '_', $capitalize_first_char = true)
    {
        return TextHelper::toCamelCase($name, $replace, $capitalize_first_char);
    }

    public static function getMethodName($property_name)
    {
        return CodeHelper::getPropertyMethodName($property_name);
    }

    public static function fetchArguments($method_name = null, $arguments = null, $class_name = null)
    {
        return CodeHelper::fetchArguments($method_name, $arguments, $class_name);
    }

}

// Endfile