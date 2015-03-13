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

namespace MVCFundamental;

use \MVCFundamental\Interfaces\FrontControllerInterface;
use \MVCFundamental\Interfaces\ResponseInterface;
use \MVCFundamental\Interfaces\RequestInterface;
use \MVCFundamental\Exception\Exception;
use \MVCFundamental\Exception\ErrorException;
use \MVCFundamental\Exception\NotFoundException;
use \MVCFundamental\Exception\InternalServerErrorException;
use \MVCFundamental\Exception\AccessForbiddenException;
use \MVCFundamental\Commons\ServiceContainerProviderTrait;
use \MVCFundamental\Commons\Helper;
use \Patterns\Traits\OptionableTrait;
use \Patterns\Traits\SingletonTrait;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * The default FrontController
 *
 * @author  piwi <me@e-piwi.fr>
 */
class FrontController
    implements FrontControllerInterface
{

    /**
     * This class inherits from \Patterns\Traits\OptionableTrait
     * This class inherits from \Patterns\Traits\SingletonTrait
     * This class inherits from \MVCFundamental\Commons\ServiceContainerProviderTrait
     */
    use OptionableTrait, SingletonTrait, ServiceContainerProviderTrait;

    /**
     * @var array
     */
    protected static $_defaults = array(
        // API
        'router'                    => 'MVCFundamental\Basic\Router',
        'route_item'                => 'MVCFundamental\Basic\Route',
        'response'                  => 'MVCFundamental\Basic\Response',
        'request'                   => 'MVCFundamental\Basic\Request',
        'template_engine'           => 'MVCFundamental\Basic\TemplateEngine',
        'template_item'             => 'MVCFundamental\Basic\Template',
        'layout_item'               => 'MVCFundamental\Basic\Layout',
        'locator'                   => 'MVCFundamental\Basic\Locator',
        'error_controller'          => 'MVCFundamental\Basic\ErrorController',
        'event_manager'             => 'MVCFundamental\Basic\EventManager',
        'event_item'                => 'MVCFundamental\Basic\Event',
        'controller_locator'        => null,
        'view_file_locator'         => null,
        'controller_name_finder'    => '%sController',
        'action_name_finder'        => '%sAction',
        'default_controller_name'   => 'default',
        'default_action_name'       => 'index',
        // defaults
        'default_content_type'      => 'html',
        'default_charset'           => 'utf8',
        'routes'                    => array(),
        'default_template'          => 'default.php',
        'default_layout'            => 'layout.php',
        'default_layout_class'      => 'MVCFundamental\Commons\DefaultLayout',
        // default error messages
        '500_error_info'            => 'An internal error occurred :(',
        '404_error_info'            => 'The requested page cannot be found :(',
        '403_error_info'            => 'Access to this page is forbidden :(',
        // app dev
        'mode'                      => 'production', // dev , test , production
        'convert_error_to_exception'=> false,
        'minimum_log_level'         => null, // one of the \Library\Logger levels
        'app_logger'                => 'Library\Logger',
    );

// -------------------------------
// Constructors
// -------------------------------

    /**
     * @var bool
     */
    protected static $_is_booted = false;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this
            ->setOptions(self::$_defaults)
            ->setOptions($options)
        ;
    }

    /**
     * @return $this
     */
    public function boot()
    {
        if (!self::$_is_booted) {
            if (empty($this->_options['temp_dir'])) {
                $this->_options['temp_dir'] =
                    DirectoryHelper::slashDirname(dirname($_SERVER['SCRIPT_FILENAME'])).'tmp';
            }

            if ($this->isMode('production')) {
                $this->setOption('convert_error_to_exception', true);
            }

            AppKernel::boot($this);

            $charset = $this->get('response')->getCharset();
            if (empty($charset)) {
                $this->get('response')->setCharset($this->getOption('default_charset'));
            }
            $content_type = $this->get('response')->getContentType();
            if (empty($content_type)) {
                $this->get('response')->setContentType($this->getOption('default_content_type'));
            }

            $routes = $this->getOption('routes');
            if (!empty($routes)) {
                $this->get('router')->setRoutes($routes);
            }

            self::$_is_booted = true;
            $this->trigger('boot');
        }
        return $this;
    }

    /**
     * @param string $mode
     * @return bool
     */
    public function isMode($mode)
    {
        if (!isset($this->_options['mode']) || !in_array($this->_options['mode'], array('dev', 'test', 'production'))) {
            $this->_options['mode'] = 'production';
        }
        return (strtolower($mode)==$this->_options['mode']);
    }

// -------------------------------
// OptionableInterface
// -------------------------------

    /**
     * @param   array   $options
     * @return  $this
     */
    public function setOptions(array $options)
    {
        $this->_options = array_merge($this->_options, $options);
        return $this;
    }

// ---------------------------
// FrontControllerInterface
// ---------------------------

    /**
     * @param   \MVCFundamental\Interfaces\RequestInterface $request
     * @return  void
     * @throws  \MVCFundamental\Exception\Exception
     * @throws  \MVCFundamental\Exception\NotFoundException
     */
    public function run(RequestInterface $request = null)
    {
        $this->boot();
        $response = $this->handle($request);
        $this->send($response);
        AppKernel::terminate($this);
    }

    /**
     * @param   \MVCFundamental\Interfaces\RequestInterface $request
     * @return  \MVCFundamental\Interfaces\ResponseInterface
     */
    public function handle(RequestInterface $request = null)
    {
        $this->boot();
        if (is_null($request)) {
            $request = $this->get('request');
        }
        $arguments  = $request->getArguments();
        $response   = $this->callRoute(
            $request->getUri(),
            !empty($arguments) ? $arguments : array(),
            $request->getMethod()
        );
        if (is_string($response)) {
            $this->get('response')->addContent($response);
            $response = $this->get('response');
        }
        return $response;
    }

    /**
     * @param   \MVCFundamental\Interfaces\ResponseInterface $response
     * @return  void
     */
    public function send(ResponseInterface $response = null)
    {
        $this->boot();
        if (is_null($response)) {
            $response = $this->get('response');
        }
        $response->send();
    }

    /**
     * @param   string  $route
     * @param   mixed   $callback
     * @param   string  $method
     * @return  $this
     */
    public function addRoute($route, $callback, $method = 'get')
    {
        $this->boot();
        $class = $this->getOption('route_item');
        $this->get('router')->addRoute(new $class($route, $callback, $method));
        return $this;
    }

    /**
     * @param   string  $view_file
     * @param   array   $params
     * @param   string  $type
     * @return  string
     */
    public function render($view_file, array $params = array(), $type = 'global')
    {
        $this->boot();
        return $this->get('template_engine')->renderTemplate($view_file, $params);
    }

    /**
     * @param   string  $route
     * @param   array   $arguments
     * @param   string  $method
     * @return  string
     * @throws  \MVCFundamental\Exception\Exception
     * @throws  \MVCFundamental\Exception\InternalServerErrorException
     * @throws  \MVCFundamental\Exception\NotFoundException
     */
    public function callRoute($route, array $arguments = array(), $method = 'get')
    {
        $this->boot();

        @list($callback, $params) = $this->get('router')->distribute(
            $route, $arguments, $method
        );

/*/
header('Content-Type: text/plain');
echo "route is: ".var_export($route,1).PHP_EOL;
echo "callback is: ".var_export($callback,1).PHP_EOL;
echo "arguments are: ".var_export($arguments,1).PHP_EOL;
exit('-- out --');
//*/

        if (empty($callback)) {
            throw new NotFoundException(
                sprintf('Route "%s" not found!', $route)
            );
        }

        if (!empty($params)) {
            $arguments = array_merge($arguments, $params);
        }

        // callable callback
        if (is_callable($callback)) {
            $return = Helper::fetchArguments($callback, $arguments);
            if (!empty($return) && is_string($return)) {
                return $return;
            }

        // array like ( controller , method )
        } elseif (is_array($callback)) {
            $ctrl_name = $this->get('locator')->locateController($callback[0]);
            if (empty($ctrl_name)) {
                throw new Exception(
                    sprintf('Unknown controller "%s"!', $callback[0])
                );
            }

            $action_name = $this->get('locator')->locateControllerAction($callback[1], $ctrl_name);
            if (empty($action_name)) {
                throw new Exception(
                    sprintf('Unknown controller\'s action "%s" (in controller "%s")!', $callback[1], $ctrl_name)
                );
            }

            return $this->callControllerAction($ctrl_name, $action_name, $arguments);

        // string
        } elseif (is_string($callback)) {

            // controller name
            $ctrl_name = $this->get('locator')->locateController($callback);
            if (!empty($ctrl_name)) {
                $default_action_name    = $this->getOption('default_action_name');
                $action_name            = $this->get('locator')->locateControllerAction($default_action_name, $ctrl_name);
                if (empty($action_name)) {
                    throw new Exception(
                        sprintf('Unknown controller\'s default action "%s" (in controller "%s")!', $default_action_name, $ctrl_name)
                    );
                }
                return $this->callControllerAction($ctrl_name, $action_name, $arguments);

            } else {

                // default controller method
                $default_ctrl_name  = $this->getOption('default_controller_name');
                $ctrl_name          = $this->get('locator')->locateController($default_ctrl_name);
                $action_name        = $this->get('locator')->locateControllerAction($callback, $ctrl_name);
                if (!empty($action_name)) {
                    return $this->callControllerAction($ctrl_name, $action_name, $arguments);

                } else {

                    // view file path
                    $view_file = $this->get('template_engine')->getTemplate($callback);
                    if (!empty($view_file)) {
                        return $this->render($view_file, $arguments);
                    }
                }
            }
        }

        throw new NotFoundException('Route result not understood!');
    }

    /**
     * @param   null|string     $controller
     * @param   null|string     $action
     * @param   array           $arguments
     * @return  string
     * @throws  \MVCFundamental\Exception\InternalServerErrorException
     */
    public function callControllerAction($controller = null, $action = null, array $arguments = array())
    {
        $this->boot();
        $controller_name = $controller;

        if (empty($controller)) {
            $controller = $this->getOption('default_controller_name');
        }
        if (empty($action)) {
            $action = $this->getOption('default_action_name');
        }
        if (!empty($controller)) {
            if (!is_object($controller)) {
                $ctrl = $this->get('locator')->locateController($controller);
                if (!empty($ctrl)) {
                    $controller = new $ctrl();
                } else {
                    throw new InternalServerErrorException(
                        sprintf('Unknown controller "%s"!', $controller_name)
                    );
                }
            }

            $arguments  = array_merge(
                Helper::getDefaultEnvParameters(), $arguments
            );

            $action = $this->get('locator')->locateControllerAction($action, $controller);
            if (!method_exists($controller, $action) || !is_callable(array($controller, $action))) {
                throw new InternalServerErrorException(
                    sprintf('Action "%s" in controller "%s" is not known or not callable!', $action, $controller_name)
                );
            }
            $result = Helper::fetchArguments(
                $action, $arguments, $controller
            );

            // result as a raw content: a string
            if (is_string($result)) {
                return $result;

            // result as an array like ( view_file , params )
            } elseif (is_array($result)) {
                $view_file = $this->get('template_engine')->getTemplate($result[0]);
                if (!empty($view_file)) {
                    return $this->render(
                        $view_file,
                        isset($result[1]) && is_array($result[1]) ? array_merge($arguments, $result[1]) : $arguments
                    );
                }

            // a reponse object
            } elseif (is_object($result) && ($ri = AppKernel::getApi('response')) && ($result instanceof $ri)) {
                $this->set('response', $result);
            }
        }

        return '';
    }

    /**
     * @param   string  $message
     * @param   int     $status
     * @param   int     $code
     * @param   string  $filename
     * @param   int     $lineno
     * @return  void
     * @throws  \MVCFundamental\Exception\AccessForbiddenException
     * @throws  \MVCFundamental\Exception\ErrorException
     * @throws  \MVCFundamental\Exception\InternalServerErrorException
     * @throws  \MVCFundamental\Exception\NotFoundException
     */
    public function error($message, $status = 500, $code = 0, $filename = __FILE__, $lineno = __LINE__)
    {
        $this->boot();
        switch ($status) {
            case 500:
                throw new InternalServerErrorException($message, $code, 1, $code, $filename, $lineno);
                break;
            case 404:
                throw new NotFoundException($message, $code);
                break;
            case 403:
                throw new AccessForbiddenException($message, $code);
                break;
            default:
                throw new ErrorException($message, $code, 1, $code, $filename, $lineno);
                break;
        }
    }

    /**
     * @param   string  $url
     * @param   bool    $follow
     * @return  void
     */
    public function redirect($url, $follow = false)
    {
        $this->boot();
        $base_url = $this->get('request')->getBaseUrl();
        $url = $base_url.str_replace($base_url, '', $url);
        if ($follow) {
            $this->get('response')->redirect($url);
        } else {
            $req_cls = get_class($this->get('request'));
            $this->set('request', new $req_cls($url, 'get'));
            $this->run();
        }
    }

    /**
     * @param $event
     * @param $callback
     * @throws \Exception
     * @return $this
     */
    public function on($event, $callback)
    {
        try {
            $this
                ->boot()
                ->get('event_manager')->addListener($event, $callback);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * @param $event
     * @param $callback
     * @throws \Exception
     * @return $this
     */
    public function off($event, $callback)
    {
        try {
            $this
                ->boot()
                ->get('event_manager')->removeListener($event, $callback);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * @param string $event
     * @param mixed $subject
     * @throws \Exception
     * @return $this
     */
    public function trigger($event, $subject = null)
    {
        try {
            $this
                ->boot()
                ->get('event_manager')->triggerEvent($event, $subject);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

}

// Endfile