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

namespace MVCFundamental\Commons;

use \MVCFundamental\AppKernel;

/**
 * ServiceContainerProviderTrait
 *
 * This implements the \MVCFundamental\Interfaces\ServiceContainerProviderInterface
 *
 * Use with caution: this trait defines magic accessors you
 * must not overwrite in your classes.
 */
trait ServiceContainerProviderTrait
{

    /**
     * This must allow some shortcuts to access a service
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        return self::__callStatic($name, $arguments);
    }

    /**
     * This must allow some shortcuts to access a service
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     */
    public static function __callStatic($name, array $arguments)
    {
        // getNew('name')
        if ($name=='getNew') {
            $service_name = isset($arguments[0]) ? $arguments[0] : null;
            array_shift($arguments);
            if (!empty($service_name)) {
                return AppKernel::getInstance()
                    ->unsetService($service_name)
                    ->getService($service_name, $arguments);
            }
            return null;

        // get('name') or getName()
        } elseif (substr($name, 0, 3)=='get') {
            if (strlen($name) > 3) {
                $service_name = substr($name, 3);
            } else {
                $service_name = isset($arguments[0]) ? $arguments[0] : null;
                array_shift($arguments);
            }
            if (!empty($service_name)) {
                return AppKernel::getInstance()->getService($service_name, $arguments);
            }
            return null;

        // set('name', $obj) or setName($obj)
        } elseif (substr($name, 0, 3)=='set') {
            if (strlen($name) > 3) {
                $service_name       = substr($name, 3);
                $service_callback   = isset($arguments[0]) ? $arguments[0] : null;
                $service_overwrite  = isset($arguments[1]) ? $arguments[1] : null;
            } else {
                $service_name       = isset($arguments[0]) ? $arguments[0] : null;
                $service_callback   = isset($arguments[1]) ? $arguments[1] : null;
                $service_overwrite  = isset($arguments[2]) ? $arguments[2] : null;
            }
            if (!empty($service_name) && !empty($service_callback)) {
                if (!empty($service_overwrite)) {
                    AppKernel::getInstance()->setService(
                        $service_name, $service_callback, $service_overwrite
                    );
                } else {
                    AppKernel::getInstance()->setService(
                        $service_name, $service_callback
                    );
                }
            }
        }
        return null;
    }
}
