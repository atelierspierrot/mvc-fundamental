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

use \HttpFundamental\Response as BaseResponse;
use \MVCFundamental\Interfaces\ResponseInterface;
use \HttpFundamental\HttpStatus;

/**
 * Class Response
 *
 * @author  piwi <me@e-piwi.fr>
 */
class Response
    extends BaseResponse
    implements ResponseInterface
{

    /**
     * @param null|string $content
     * @param null|string $status
     * @param null|string $content_type
     * @param null|string $charset
     */
    public function __construct(
        $content = null, $status = HttpStatus::OK, $content_type = null, $charset = null
    ) {
        parent::__construct();
        $this->setStatus($status);
        if (!is_null($content)) {
            $this->addContent($content);
        }
        if (!is_null($content_type)) {
            $this->setContentType($content_type);
        }
        if (!is_null($charset)) {
            $this->setCharset($charset);
        }
    }

    /**
     * @param string $name
     * @param mixed $content
     * @return $this
     */
    public function addContent($content, $name = 'global')
    {
        return parent::addContent($name, $content);
    }

    /**
     * @param null|string $content
     * @param null|string $type
     * @return void
     */
    public function send($content = null, $type = null)
    {
        if (!empty($content)) {
            self::addContent(null, $content);
        }
        if (!empty($type)) {
            parent::setContentType($type);
        }
        parent::send();
    }
}
