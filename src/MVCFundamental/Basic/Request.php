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

use \HttpFundamental\Request as BaseRequest;
use \MVCFundamental\Interfaces\RequestInterface;

/**
 * Class Request
 *
 * @author  piwi <me@e-piwi.fr>
 */
class Request
    extends BaseRequest
    implements RequestInterface
{

    /**
     * @param null|string $url
     * @param string $method
     * @param array $data
     */
    public function __construct($url = null, $method = null, array $data = array())
    {
        parent::__construct($url);
        if (!empty($method)) {
            $this->setMethod($method);
        }
        if (!empty($data)) {
            $this->setData($data);
        }
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return dirname($_SERVER['SCRIPT_NAME']);
    }

    /**
     * @return string
     */
    public function getUri()
    {
        //        $uri            = $_SERVER['REQUEST_URI'];
//        $uri            = $this->getUrl();
        $uri            = parse_url($this->getUrl(), PHP_URL_PATH);
        $query          = parse_url($this->getUrl(), PHP_URL_QUERY);
        $document_root  = $_SERVER['SCRIPT_NAME'];
        if (!empty($query)) {
            $uri .= '?'.$query;
        }
        if (substr_count($uri, $document_root)) {
            $return = str_replace($document_root, '', $uri);
        } else {
            $return = str_replace(dirname($document_root), '', $uri);
        }
        if ($return{0}=='?') {
            $return = '/'.substr($return, 1);
        }
        if (substr($return, -1)=='/') {
            $return = substr($return, 0, -1);
        }
        if (empty($return)) {
            $return = '/';
        }
        return $return;
    }
}
