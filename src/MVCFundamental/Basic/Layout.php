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

use \MVCFundamental\Exception\ErrorException;
use \MVCFundamental\Interfaces\LayoutInterface;
use \MVCFundamental\Commons\ViewFileTrait;
use \Patterns\Traits\OptionableTrait;
use \MVCFundamental\FrontController;
use \MVCFundamental\AppKernel;
use \MVCFundamental\Commons\Helper;

/**
 * Class Layout
 */
class Layout
    implements LayoutInterface
{

    /**
     * This trait inherits from \MVCFundamental\Commons\TemplateTrait and \Patterns\Traits\OptionableTrait
     */
    use ViewFileTrait, OptionableTrait;

    /**
     * @var array
     */
    protected $_defaults = array();

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions(array_merge($this->_defaults, $options));
        $this->_init();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->renderLayout();
    }

    /**
     * Distribute object's options
     * @return $this
     */
    protected function _init()
    {
        foreach ($this->getOptions() as $name=>$opts) {
            switch ($name) {
                case 'layout': $this->setLayout($opts); break;
                case 'params': $this->setParams($opts); break;
                case 'children':
                    foreach ($opts as $type=>$view) {
                        $this->setChild($type, $view);
                    }
                    break;
            }
        }
        return $this;
    }

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
        $this->_children[$name] = FrontController::get('template_engine')
                                        ->getNewTemplate($view, $params);
        return $this;
    }

    /**
     * @param   string  $name
     * @param   string  $param_name
     * @param   mixed   $param_value
     * @return  $this
     * @throws  \MVCFundamental\Exception\ErrorException
     */
    public function setChildParam($name, $param_name, $param_value)
    {
        $child = $this->getChild($name);
        if (is_object($child)) {
            $child->addParam($param_name, $param_value);
        } else {
            throw new ErrorException(
                sprintf('Can not add a parameter for a string child (with name "%s"!', $name)
            );
        }
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
     * @param   string  $name
     * @param   array   $params
     * @return  string
     */
    public function renderChild($name, array $params = array())
    {
        $child = $this->getChild($name);
        $_params = array_merge($this->getParams(), $params);
        if (!empty($child)) {
            if (is_object($child)) {
                return $child->render(null, $_params);
            } else {
                return $child;
            }
        }
        return '';
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
