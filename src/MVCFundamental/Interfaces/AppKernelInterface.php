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

/**
 * Interface AppKernelInterface
 */
interface AppKernelInterface
    extends SingletonInterface, ServiceContainerProviderInterface, FrontControllerAwareInterface
{

    /**
     * This must be called when the system boots
     *
     * @param FrontControllerInterface $app
     * @return mixed
     */
    public static function boot(FrontControllerInterface $app);

    /**
     * This must be called when the system terminates its run
     *
     * @param FrontControllerInterface $app
     * @return mixed
     */
    public static function terminate(FrontControllerInterface $app);

    /**
     * This must abort a runtime safely
     *
     * @param   \Exception $e
     * @return  void
     */
    public static function abort(\Exception $e);

    /**
     * This must handle a logging system
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function log($level, $message, array $context = array());

    /**
     * This must handle caught exceptions
     *
     * @param   \Exception $e
     * @return  void
     */
    public static function handleException(\Exception $e);

    /**
     * This must handle caught errors
     *
     * @param   int     $errno
     * @param   string  $errstr
     * @param   string  $errfile
     * @param   int     $errline
     * @param   array   $errcontext
     * @return  void
     */
    public static function handleError($errno = 0, $errstr = '', $errfile = '', $errline = 0, array $errcontext = array());

    /**
     * This must handle runtime shutdown
     *
     * @return void
     */
    public static function handleShutdown();

}

// Endfile