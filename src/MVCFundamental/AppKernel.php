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

use \Patterns\Traits\SingletonTrait;
use \Library\ServiceContainer\ServiceContainer;
use \MVCFundamental\Interfaces\FrontControllerInterface;
use \MVCFundamental\Interfaces\AppKernelInterface;
use \MVCFundamental\Exception\ErrorException;
use \MVCFundamental\Commons\ServiceContainerProviderTrait;
use \MVCFundamental\Commons\Helper;
use \Patterns\Commons\Collection;
use \Library\Helper\Directory as DirectoryHelper;
use \Library\Logger;

/**
 * Class AppKernel: the root core of the application
 */
class AppKernel
    extends ServiceContainer
    implements  AppKernelInterface
{

    /**
     * This class inherits from \Patterns\Traits\SingletonTrait
     * This class inherits from \MVCFundamental\Commons\ServiceContainerProviderTrait
     */
    use SingletonTrait, ServiceContainerProviderTrait;

// -------------------------
// System environment
// -------------------------

    /**
     * @var array
     */
    protected static $_api = array(
        'controller'        => 'MVCFundamental\Interfaces\ControllerInterface',
        'error_controller'  => 'MVCFundamental\Interfaces\ErrorControllerInterface',
        'response'          => 'MVCFundamental\Interfaces\ResponseInterface',
        'request'           => 'MVCFundamental\Interfaces\RequestInterface',
        'router'            => 'MVCFundamental\Interfaces\RouterInterface',
        'route'             => 'MVCFundamental\Interfaces\RouteInterface',
        'template_engine'   => 'MVCFundamental\Interfaces\TemplateEngineInterface',
        'template'          => 'MVCFundamental\Interfaces\TemplateInterface',
        'layout'            => 'MVCFundamental\Interfaces\LayoutInterface',
        'locator'           => 'MVCFundamental\Interfaces\LocatorInterface',
        'event'             => 'MVCFundamental\Interfaces\EventInterface',
        'event_manager'     => 'MVCFundamental\Interfaces\EventManagerInterface',
        'logger'            => 'Psr\Log\LoggerInterface',
    );

    /**
     * @var array
     */
    protected static $_constructors = array(
        'route_item'        => 'route',
        'template_item'     => 'template',
        'layout_item'       => 'layout',
        'event_item'        => 'event',
    );

// -------------------------
// SingletonInterface
// -------------------------

    /**
     * Avoid public construction of the object and prefer the `getInstance()` static singleton access
     */
    public function __construct()
    {
        $this->init();
    }

// -------------------------
// Service Container system
// -------------------------

    /**
     * @param   string  $name
     * @param   object  $object
     * @return  bool
     * @throws  \MVCFundamental\Exception\ErrorException
     */
    protected function _validateService($name, $object)
    {
        if (array_key_exists($name, self::$_api) && false===self::isApiValid($name, $object)) {
            throw new ErrorException(
                sprintf('A "%s" service must implement interface "%s" (get "%s")!',
                    $name, self::$_api[$name], get_class($object))
            );
        }
        return true;
    }

// -------------------------
// API Manager
// -------------------------

    /**
     * @param   string  $name
     * @return  null|string
     * @api
     */
    public static function getApi($name)
    {
        return (isset(self::$_api[$name]) ? self::$_api[$name] : null);
    }

    /**
     * @param   string          $name
     * @param   object|callable $object
     * @return  bool
     * @api
     */
    public static function isApiValid($name, $object)
    {
        return (bool) (
            array_key_exists($name, self::$_api) &&
            is_object($object) &&
            Helper::classImplements($object, self::$_api[$name])
        );
    }

    /**
     * @param   string  $name
     * @param   string  $class
     * @param   array   $arguments
     * @return  object
     * @throws  \MVCFundamental\Exception\ErrorException
     * @api
     */
    public static function apiFactory($name, $class, array $arguments = array())
    {
        if (!class_exists($class)) {
            $cls_name = self::getApi($class);
        } else {
            $cls_name = $class;
        }
        if (class_exists($cls_name)) {
            $objectReflection = new \ReflectionClass($cls_name);
            $object = $objectReflection->newInstanceArgs($arguments);
            if (!self::isApiValid($name, $object)) {
                throw new ErrorException(
                    sprintf('A "%s" service must implement interface "%s" (get "%s")!',
                        $name, self::$_api[$name], get_class($object))
                );
            }
            return $object;
        } else {
            throw new ErrorException(
                sprintf('Class "%s" not found!', $cls_name)
            );
        }
    }

// -------------------------
// FrontControllerAwareInterface
// -------------------------

    /**
     * @param \MVCFundamental\Interfaces\FrontControllerInterface $app
     */
    public static function setFrontController(FrontControllerInterface $app)
    {
        self::set('front_controller', $app, true);
    }

    /**
     * @return \MVCFundamental\Interfaces\FrontControllerInterface
     */
    public static function getFrontController()
    {
        return self::get('front_controller');
    }

// -------------------------
// AppKernelInterface
// -------------------------

    /**
     * @param   \MVCFundamental\Interfaces\FrontControllerInterface $app
     * @return  void
     * @throws  \MVCFundamental\Exception\ErrorException
     */
    public static function boot(FrontControllerInterface $app)
    {
        // stores the FrontController
        self::setFrontController($app);

        // define internal handlers
        set_exception_handler(array(__CLASS__, 'handleException'));
        register_shutdown_function(array(__CLASS__, 'handleShutdown'));
        if (self::getFrontController()->getOption('convert_error_to_exception')==true) {
            set_error_handler(array(__CLASS__, 'handleError'));
        }

        // the required temporary directory
        $tmp_dir = self::getFrontController()->getOption('temp_dir');
        if (
            empty($tmp_dir) ||
            (!file_exists($tmp_dir) && !@DirectoryHelper::create($tmp_dir)) ||
            !is_dir($tmp_dir) ||
            !is_writable($tmp_dir)
        ) {
            $tmp_dir = DirectoryHelper::slashDirname(sys_get_temp_dir()).'mvc-fundamental';
            if (!@DirectoryHelper::ensureExists($tmp_dir)) {
                throw new ErrorException(
                    sprintf('The "%s" temporary directory can not be created or is not writable (and a default one could not be taken)!', $tmp_dir)
                );
            }
            self::getFrontController()->setOption('temp_dir', $tmp_dir);
        }

        // the application logger
        if (self::getFrontController()->getOption('minimum_log_level')==null) {
            self::getFrontController()->setOption('log_level',
                self::getFrontController()->isMode('production') ? Logger::WARNING : Logger::DEBUG
            );
        }
        $logger_options = array(
            'duplicate_errors'  => false,
            'directory'         => self::getFrontController()->getOption('temp_dir'),
            'minimum_log_level' => self::getFrontController()->getOption('log_level'),
        );
        $logger_class = self::getFrontController()->getOption('app_logger');
        if (!class_exists($logger_class) || !Helper::classImplements($logger_class, self::getApi('logger'))) {
            throw new ErrorException(
                sprintf('A logger must exist and implement the "%s" interface (for class "%s")!',
                    self::getApi('logger'), $logger_class)
            );
        }
        self::set('logger', new $logger_class($logger_options));

        // load services
        foreach (self::getFrontController()->getOptions() as $var=>$val) {

            if (array_key_exists($var, self::$_api)) {
                self::set($var, self::apiFactory($var, $val));
            }

            if (array_key_exists($var, self::$_constructors)) {
                $cls_index  = self::$_constructors[$var];
                self::getInstance()->setProvider(
                    $var, function ($app, $name, array $arguments = array()) use ($val, $cls_index) {
                        return $app::apiFactory($cls_index, $val, $arguments);
                    }
                );
            }
        }

        self::get('event_manager')->setEventClass(self::getFrontController()->getOption('event_item'));
    }

    /**
     * @var bool Does the system already correctly terminated
     */
    private static $_terminated = false;

    /**
     * This must be called when the system terminates its run
     *
     * @param FrontControllerInterface $app
     * @return mixed
     */
    public static function terminate(FrontControllerInterface $app)
    {
        if (!self::$_terminated) {

        }
    }

    /**
     * This must abort a runtime safely
     *
     * @param   \Exception $e
     * @return  void
     */
    public static function abort(\Exception $e)
    {

    }

    /**
     * @param   \Exception $e
     * @return  void
     * @throws  \MVCFundamental\Exception\ErrorException
     */
    public static function handleException(\Exception $e)
    {
        $error_controller = self::getFrontController()->getOption('error_controller');
        try {
            self::getFrontController()->callControllerAction(
                $error_controller, 'index', array($e)
            );
        } catch (\Exception $f) {
            throw new ErrorException(
                $f->getMessage(), $f->getCode(),
                method_exists($f, 'getSeverity') ? $f->getSeverity() : 0,
                $f->getFile(), $f->getLine(), $e
            );
        }
    }

    /**
     * @param   int     $errno
     * @param   string  $errstr
     * @param   string  $errfile
     * @param   int     $errline
     * @param   array   $errcontext
     * @return  void
     * @throws  \MVCFundamental\Exception\ErrorException
     */
    public static function handleError($errno = 0, $errstr = '', $errfile = '', $errline = 0, array $errcontext = array())
    {
        if (!(error_reporting() & $errno)) {
            return true;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * This must handle runtime shutdown
     *
     * @return void
     */
    public static function handleShutdown()
    {
        self::terminate(self::getFrontController());
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public static function log($level, $message, array $context = array())
    {
        if (self::getInstance()->hasService('logger')) {
            self::get('logger')->log($level, $message, $context);
        }
    }

}

// Endfile