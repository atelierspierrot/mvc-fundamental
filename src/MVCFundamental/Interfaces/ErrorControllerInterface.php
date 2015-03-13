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

/**
 * Interface ErrorControllerInterface
 *
 * @author  piwi <me@e-piwi.fr>
 * @api
 */
interface ErrorControllerInterface
    extends ControllerInterface
{

    /**
     * Default error page
     *
     * @param \Exception $e
     * @return string
     */
    public function error(\Exception $e);

    /**
     * 500 error page
     *
     * @param \Exception $e
     * @return string
     */
    public function fatalError(\Exception $e);

    /**
     * 404 error page
     *
     * @param \Exception $e
     * @return string
     */
    public function notFoundError(\Exception $e);

    /**
     * 403 error page
     *
     * @param \Exception $e
     * @return string
     */
    public function authorizationError(\Exception $e);

}

// Endfile