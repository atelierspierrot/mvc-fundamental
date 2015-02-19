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

/**
 * Interface ServiceContainerProviderInterface
 */
interface ServiceContainerProviderInterface
{

    /**
     * This must allow some shortcuts to access a service
     *
     * Usage:
     *
     *      $obj->get('request') == $obj->getService('request')
     *      $obj->getRequest() == $obj->getService('request')
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     */
    public function __call($name, array $arguments);

    /**
     * This must allow some shortcuts to access a service
     *
     * Usage:
     *
     *      $obj::get('request') == $obj::getInstance()->getService('request')
     *      $obj::getRequest() == $obj::getInstance()->getService('request')
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     */
    public static function __callStatic($name, array $arguments);

}

// Endfile