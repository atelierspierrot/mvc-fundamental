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
 * Interface TemplateEngineInterface
 *
 * @api
 */
interface TemplateEngineInterface
{

    /**
     * @param   string  $view
     * @param   array   $params
     * @return  \MVCFundamental\Interfaces\TemplateInterface
     */
    public function getNewTemplate($view, array $params = array());

    /**
     * @param   string  $view
     * @param   array   $params
     * @param   array   $options
     * @return  \MVCFundamental\Interfaces\LayoutInterface
     */
    public function getNewLayout($view, array $params = array(), array $options = array());

    /**
     * @param   string  $view
     * @param   array   $params
     * @return  string
     */
    public function renderTemplate($view, array $params = array());

    /**
     * Get the default parameters for all views
     *
     * @return  array   The array of default parameters
     */
    public function getDefaultViewParams();

    /**
     * Search a view file in the current file system
     *
     * @param   string  $name   The file path to search
     * @return  string  The path of the file found
     */
    public function getTemplate($name);

    /**
     * @param string $content
     * @param null|string $title
     * @param array $params
     * @return string
     */
    public function renderDefault($content, $title = null, array $params = array());

    /**
     * @param array $arguments
     * @return mixed
     */
    public function getDefaultLayout(array $arguments = array());
}
