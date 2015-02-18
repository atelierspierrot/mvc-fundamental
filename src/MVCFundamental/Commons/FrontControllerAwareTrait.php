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

use \MVCFundamental\Interfaces\FrontControllerInterface;

/**
 * FrontControllerAwareTrait
 */
trait FrontControllerAwareTrait
{

    /**
     * @var \MVCFundamental\Interfaces\FrontControllerInterface
     */
    protected static $_front_controller;

    /**
     * @param \MVCFundamental\Interfaces\FrontControllerInterface $app
     */
    public static function setFrontController(FrontControllerInterface $app)
    {
        self::$_front_controller = $app;
    }

    /**
     * @return \MVCFundamental\Interfaces\FrontControllerInterface
     */
    public static function getFrontController()
    {
        return self::$_front_controller;
    }

}

// Endfile