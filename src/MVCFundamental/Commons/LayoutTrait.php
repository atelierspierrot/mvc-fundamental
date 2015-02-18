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

use \MVCFundamental\AppKernel;
use \MVCFundamental\FrontController;
use \Patterns\Traits\OptionableTrait;

/**
 * LayoutTrait
 */
trait LayoutTrait
{

    /**
     * This trait inherits from \MVCFundamental\Commons\TemplateTrait and \Patterns\Traits\OptionableTrait
     */
    use TemplateTrait, OptionableTrait;

// -------------------------------
// LayoutInterface
// -------------------------------

    /**
     * @param   string $view
     * @return  $this
     */
    public function setLayout($view)
    {
        return $this->setView($view);
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->getView();
    }

    /**
     * @var array
     */
    protected $_children = array();

    /**
     * @param   string  $name
     * @param   string  $view
     * @param   array   $params
     * @return  $this
     */
    public function setChild($name, $view, array $params = array())
    {
        $this->_children[$name] = FrontController::getNew('template_item', $view, $params);
        return $this;
    }

    /**
     * @param   string  $name
     * @param   string  $content
     * @return  $this
     */
    public function setChildAsString($name, $content)
    {
        $this->_children[$name] = $content;
        return $this;
    }

    /**
     * @param   string  $name
     * @return  mixed
     */
    public function getChild($name)
    {
        return ($this->hasChild($name) ? $this->_children[$name] : null);
    }

    /**
     * @param   string  $name
     * @return  bool
     */
    public function hasChild($name)
    {
        return (bool) isset($this->_children[$name]);
    }

    /**
     * Build the global layout with all children contents
     *
     * @param   array   $params     An array of the parameters passed for the view parsing
     * @return  string  Returns the view file content rendering
     */
    public function renderLayout(array $params = array())
    {
        $params = array_merge($this->getParams(), $params);

        $children = array();
        foreach ($this->_children as $name=>$item) {
            if (Helper::classImplements($item, AppKernel::getApi('template'))) {
                $children[$name] = $item->render();
            } else {
                $children[$name] = $item;
            }
        }
        $params = array_merge($params, $children);
        $params['children'] = $children;

        return $this->render($this->getView(), $params);
    }

}

// Endfile