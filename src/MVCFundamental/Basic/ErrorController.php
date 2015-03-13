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
use \MVCFundamental\Interfaces\ControllerInterface;
use \MVCFundamental\Interfaces\ErrorControllerInterface;
use \MVCFundamental\Exception\NotFoundException;
use \MVCFundamental\Exception\AccessForbiddenException;
use \MVCFundamental\Exception\InternalServerErrorException;
use \HttpFundamental\HttpStatus;

/**
 * Class ErrorController
 *
 * @author  piwi <me@e-piwi.fr>
 */
class ErrorController
    implements ControllerInterface, ErrorControllerInterface
{

    /**
     * @param   \Exception $e
     * @return  string
     */
    public function indexAction(\Exception $e)
    {
        if ($e instanceof NotFoundException) {
            $this->notFoundError($e);
        } elseif ($e instanceof AccessForbiddenException) {
            $this->authorizationError($e);
        } elseif (FrontController::getInstance()->isMode('production')) {
            $this->fatalError($e);
        } else {
            $this->error($e);
        }
        return $e->getMessage();
    }

    /**
     * Default error page
     *
     * @param   \Exception $e
     * @return  string
     */
    public function error(\Exception $e)
    {
        $title      = 'Error';
        $previous   = $e->getPrevious();
        $message    = $this->_getExceptionMessage($e, true);
        if ($previous) {
            $message .= $this->_getExceptionMessage($previous, false);
        }
        $this->_render($message, 'Oups :(', HttpStatus::ERROR, array('page_title'=>$title));
    }

    /**
     * 500 error page
     *
     * @param   \Exception $e
     * @return  string
     */
    public function fatalError(\Exception $e)
    {
        $content = $this->_getProductionErrorMessage($e, 500);
        if (FrontController::getInstance()->isMode('dev')) {
            $content .= $this->_getExceptionMessage($e, true);
        }
        $this->_render(
            $content,
            'Internal server error',
            HttpStatus::ERROR
        );
    }

    /**
     * 404 error page
     *
     * @param   \Exception $e
     * @return  string
     */
    public function notFoundError(\Exception $e)
    {
        $content = $this->_getProductionErrorMessage($e, 404);
        if (FrontController::getInstance()->isMode('dev')) {
            $content .= $this->_getExceptionMessage($e, true);
        }
        $this->_render(
            $content,
            'Page not found',
            HttpStatus::NOT_FOUND
        );
    }

    /**
     * 403 error page
     *
     * @param   \Exception $e
     * @return  string
     */
    public function authorizationError(\Exception $e)
    {
        $content = $this->_getProductionErrorMessage($e, 403);
        if (FrontController::getInstance()->isMode('dev')) {
            $content .= $this->_getExceptionMessage($e, true);
        }
        $this->_render(
            $content,
            'Access forbidden',
            HttpStatus::UNAUTHORIZED
        );
    }

    /**
     * @param string $content
     * @param string $title
     * @param string $status
     * @param array $params
     */
    protected function _render($content, $title = 'Error', $status = HttpStatus::ERROR, array $params = array())
    {
        $fctrl = FrontController::getInstance();
        $content = $fctrl->get('template_engine')
            ->renderDefault($content, $title, $params);
        $fctrl->send(new Response(
            $content, $status, 'html', 'utf8'
        ));
    }

    /**
     * @param \Exception $e
     * @param int $type
     * @return string
     */
    protected function _getProductionErrorMessage(\Exception $e, $type = 500)
    {
        $content = '<p>'.FrontController::getInstance()->getOption($type.'_error_info').'</p>';
        if (FrontController::getInstance()->isMode('dev')) {
            $content .= '<blockquote>'.$e->getMessage().'</blockquote>';
        }
        return $content;
    }

    /**
     * @param   \Exception  $e
     * @param   int         $primary Flag: `0` means primary message, `1` means secondary one, `2` to only have a separator
     * @return  string
     */
    protected function _getExceptionMessage(\Exception $e, $primary = 0)
    {
        $errno      = $e->getCode();
        $errstr     = $e->getMessage();
        $errfile    = $e->getFile();
        $errline    = $e->getLine();
        $backtrace  = $e->getTraceAsString();
        $type       = get_class($e);
        $separator  = false;
        switch ($primary) {
            case 2:
                $separator  = true;
                $message    = "A '{$type}' error occurred"
                    .(!empty($errstr) ? ' with the following message:' : '.')
                ;
                break;
            case 1:
                $separator  = true;
                $message    = "Additionally, a '{$type}' error occurred previously"
                    .(!empty($errstr) ? ' with the following message:' : '.')
                ;
                break;
            default:
                $message    = "A '{$type}' error occurred previously"
                    .(!empty($errstr) ? ' with the following message:' : '.')
                ;
                break;
        }
        $content    = '';
        if ($separator) $content .= '<hr />';
        $content    .= '<p>'.$message.'</p>';
        if (!empty($errstr)) $content .= '<blockquote>'.$errstr.'</blockquote>';
        $content    .= <<<MESSAGE
<p class="text-muted">Error with code <code>{$errno}</code> thrown in file <code>{$errfile}</code> at line <code>{$errline}</code>.</p>
<p class="text-muted">Back trace:</p>
<pre>
{$backtrace}
</pre>
MESSAGE;
        return $content;
    }

}

// Endfile