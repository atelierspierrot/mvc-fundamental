<?php
/**
 * This file is part of the MVC-Fundamental package.
 *
 * Copyright (c) 2013-2016 Pierre Cassat <me@e-piwi.fr> and contributors
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

/**
 * Interface ServiceContainerProviderInterface
 *
 * @author  piwi <me@e-piwi.fr>
 * @api
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
