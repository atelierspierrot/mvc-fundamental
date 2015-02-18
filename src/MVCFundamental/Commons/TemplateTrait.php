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

use \MVCFundamental\FrontController;
use \MVCFundamental\Exception\Exception;
use \Patterns\Traits\TemplateViewTrait;

/**
 * TemplateTrait
 */
trait TemplateTrait
{

    /**
     * This trait inherits from \Patterns\Traits\TemplateViewTrait
     */
    use TemplateViewTrait;

    /**
     * Building of a view content including a view file passing it parameters
     *
     * @param   string  $view       The view filename (which must exist)
     * @param   array   $params     An array of the parameters passed for the view parsing
     * @return  string  Returns the view file content rendering
     * @throws  \MVCFundamental\Exception\Exception
     */
    public function render($view = null, array $params = array())
    {
        if (empty($view)) {
            $view = $this->getView();
        }
        if (empty($params)) {
            $params = $this->getParams();
        }
        $_view = $this->getTemplate($view);
        if ($_view && @file_exists($_view)) {
            extract($this->getDefaultViewParams(), EXTR_OVERWRITE);
            if (!empty($params)) {
                extract($params, EXTR_OVERWRITE);
            }
            ob_start();
            include $_view;
            $this->setOutput( ob_get_contents() );
            ob_end_clean();
        } else {
            throw new Exception(
                sprintf('View "%s" can\'t be found!', $view)
            );
        }

        return $this->getOutput();
    }

    /**
     * Get the default parameters for all views
     *
     * @return  array   The array of default parameters
     */
    public function getDefaultViewParams()
    {
        return FrontController::get('template_engine')->getDefaultViewParams();
    }

    /**
     * Search a view file in the current file system
     *
     * @param   string  $name   The file path to search
     * @return  string  The path of the file found
     */
    public function getTemplate($name)
    {
        return FrontController::get('template_engine')->getTemplate($name);
    }

}

// Endfile