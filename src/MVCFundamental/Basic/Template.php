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

namespace MVCFundamental\Basic;

use \MVCFundamental\Interfaces\TemplateInterface;
use \MVCFundamental\Commons\ViewFileTrait;

/**
 * Class Template
 */
class Template
    implements TemplateInterface
{

    /**
     * This trait inherits from \MVCFundamental\Commons\ViewFileTrait
     */
    use ViewFileTrait;

    /**
     * @param null|string $view_file
     * @param array $params
     */
    public function __construct($view_file = null, array $params = array())
    {
        if (!empty($view_file)) {
            $this->setView($view_file);
        }
        if (!empty($params)) {
            $this->setParams($params);
        }
    }

    /**
     * @return string
     * @throws \MVCFundamental\Exception\Exception
     */
    public function __toString()
    {
        $view   = $this->getView();
        $params = $this->getParams();
        return $this->render($view, $params);
    }
}
