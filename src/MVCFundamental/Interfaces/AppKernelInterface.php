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

/**
 * Interface AppKernelInterface
 */
interface AppKernelInterface
    extends SingletonInterface
{

    /**
     * @param FrontControllerInterface $app
     * @return mixed
     */
    public static function boot(FrontControllerInterface $app);

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function log($level, $message, array $context = array());

    /**
     * @param   \Exception $e
     * @return  void
     */
    public static function handleException(\Exception $e);

    /**
     * @param   int     $errno
     * @param   string  $errstr
     * @param   string  $errfile
     * @param   int     $errline
     * @param   array   $errcontext
     * @return  void
     */
    public static function handleError($errno = 0, $errstr = '', $errfile = '', $errline = 0, array $errcontext = array());

}

// Endfile