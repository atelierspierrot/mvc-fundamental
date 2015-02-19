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

use \Patterns\Interfaces\ViewInterface;
use \Patterns\Interfaces\OptionableInterface;

/**
 * Interface LayoutInterface
 *
 * @api
 */
interface LayoutInterface
    extends ViewInterface, OptionableInterface
{

    /**
     * @param array $options
     */
    public function __construct(array $options = array());

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param   string $view
     * @return  $this
     */
    public function setLayout($view);

    /**
     * @return string
     */
    public function getLayout();

    /**
     * @param   string  $name
     * @param   string  $view
     * @param   array   $params
     * @return  $this
     */
    public function setChild($name, $view, array $params = array());

    /**
     * @param   string  $name
     * @param   string  $content
     * @return  $this
     */
    public function setChildAsString($name, $content);

    /**
     * @param   string  $name
     * @return  mixed
     */
    public function getChild($name);

    /**
     * @param   string  $name
     * @return  bool
     */
    public function hasChild($name);

    /**
     * Build the global layout with all children contents
     *
     * @param   array   $params     An array of the parameters passed for the view parsing
     * @return  string  Returns the view file content rendering
     */
    public function renderLayout(array $params = array());

}

// Endfile