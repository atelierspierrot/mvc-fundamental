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

use \MVCFundamental\Basic\Layout;
use \MVCFundamental\FrontController;
use \MVCFundamental\AppKernel;
use \MVCFundamental\Commons\Helper;

/**
 * Class Layout
 */
class DefaultLayout
    extends Layout
{

    /**
     * @var array
     */
    protected $_defaults = array(
        'layout'    => 'layout.php',
        'children'  => array(
            'content'   => 'layout_content.php',
            'header'    => 'layout_header.php',
            'footer'    => 'layout_footer.php',
            'extra'     => 'layout_extra.php',
            'aside'     => 'layout_aside.php',
        ),
        'params'    => array(
            'title'     => 'Default layout',
        ),
    );

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
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