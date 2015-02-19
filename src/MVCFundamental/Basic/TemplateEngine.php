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

namespace MVCFundamental\Basic;

use \MVCFundamental\FrontController;
use \MVCFundamental\Commons\Helper;
use \MVCFundamental\Interfaces\TemplateEngineInterface;

/**
 * Class TemplateEngine
 */
class TemplateEngine
    implements TemplateEngineInterface
{

    /**
     * @param   string  $view
     * @param   array   $params
     * @return  \MVCFundamental\Interfaces\TemplateInterface
     */
    public function getNewTemplate($view, array $params = array())
    {
        return FrontController::getNew('template_item', $view, $params);
    }

    /**
     * @param   string  $view
     * @param   array   $params
     * @param   array   $options
     * @return  \MVCFundamental\Interfaces\LayoutInterface
     */
    public function getNewLayout($view, array $params = array(), array $options = array())
    {
        $layout = FrontController::getNew('layout_item', $options);
        $layout->setView($view)->setParams($params);
        return $layout;
    }

    /**
     * @param   string  $view
     * @param   array   $params
     * @return  string
     */
    public function renderTemplate($view, array $params = array())
    {
        $tpl = $this->getNewTemplate($view, $params);
        return $tpl->render();
    }

    /**
     * Get the default parameters for all views
     *
     * @return  array   The array of default parameters
     */
    public function getDefaultViewParams()
    {
        return Helper::getDefaultEnvParameters();
    }

    /**
     * Search a view file in the current file system
     *
     * @param   string  $name   The file path to search
     * @return  string  The path of the file found
     */
    public function getTemplate($name)
    {
        return FrontController::get('locator')->locateTemplate($name);
    }

    /**
     * @param string $content
     * @param null|string $title
     * @param array $params
     * @return string
     */
    public function renderDefault($content, $title = null, array $params = array())
    {
        $template = __DIR__.'/../Resources/templates/default.php';
        $view_file = $this->getTemplate($template);
        return $this->renderTemplate($view_file, array_merge($params, array(
            'title'     => $title,
            'content'   => $content
        )));
    }

}

// Endfile