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
    );

    /**
     * @var array
     */
    protected static $_constructors = array(
        'route_item'        => 'route',
        'template_item'     => 'template',
        'layout_item'       => 'layout',
    );

// -------------------------
// SingletonInterface
// -------------------------

    /**
     * Avoid public construction of the object and prefer the `getInstance()` static singleton access
     */
    public function __construct()
    {
        $this->_initServiceContainer();
    }

// -------------------------
// Service Container system
// -------------------------

    /**
     * Use this constant to NOT throw error when trying to get an unknown service
     */
    const FAIL_GRACEFULLY = 1;

    /**
     * Use this constant to throw error when trying to get an unknown service
     *
     * This is the default behavior.
     */
    const FAIL_WITH_ERROR = 2;

    /**
     * @var \Patterns\Commons\Collection
     */
    private $_services;

    /**
     * @var \Patterns\Commons\Collection
     */
    private $_services_constructors;

    /**
     * @var \Patterns\Commons\Collection
     */
    private $_services_protected;

    /**
     * Initialize the service container system
     */
    protected function _initServiceContainer()
    {
        $this->_services                = new Collection();
        $this->_services_protected      = new Collection();
        $this->_services_constructors   = new Collection();
    }

    /**
     * Define a service constructor like `array( name , callback , overwritable )` or a closure
     *
     * @param   string  $name
     * @param   array   $constructor A service array constructor like `array( name , callback , overwritable )`
     *          callable $constructor A callback as a closure that must return the service object: function ($name, $arguments) {}
     * @return mixed
     * @api
     */
    public function setConstructor($name, $constructor)
    {
        $this->_services_constructors->offsetSet($name, $constructor);
    }

    /**
     * Construct a service based on the `setConstructors()` item
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  void
     * @throws  ErrorException
     */
    protected function _constructService($name, array $arguments = array())
    {
        if ($this->_services_constructors->offsetExists($name)) {
            $data = $this->_services_constructors->offsetGet($name);
            if (is_callable($data) || ($data instanceof \Closure)) {
                try {
                    $item = call_user_func_array(
                        $data, array($this, $name, $arguments)
                    );
                    $this->setService($name, $item);
                } catch (\Exception $e) {
                    throw new ErrorException(
                        sprintf('An error occurred while trying to create a "%s" instance!', $name),
                        0, 1, __FILE__, __LINE__, $e
                    );
                }
            } elseif (is_array($data)) {
                $this->setService(
                    $name,
                    $data[1],
                    isset($data[2]) ? $data[2] : true
                );
            } else {
                throw new ErrorException(
                    sprintf('A service constructor must be a valid callback (for service "%s")!', $name)
                );
            }
        }
    }

    /**
     * Register a new service called `$name` declared as NOT overwritable by default
     *
     * @param   string          $name
     * @param   object|callable $callback
     * @param   bool            $overwritable
     * @return  $this
     * @throws  \MVCFundamental\Exception\ErrorException
     * @api
     */
    public function setService($name, $callback, $overwritable = true)
    {
        if ($this->hasService($name) && $this->_services_protected->offsetExists($name)) {
            throw new ErrorException(
                sprintf('Over-write a "%s" service is forbidden!', $name)
            );
        }
        if (array_key_exists($name, self::$_api) && false===self::isApiValid($name, $callback)) {
            throw new ErrorException(
                sprintf('A "%s" service must implement interface "%s" (get "%s")!',
                    $name, self::$_api[$name], get_class($callback))
            );
        }
        $this->_services->setEntry($name, $callback);
        if ($overwritable===false) {
            $this->_services_protected->setEntry($name, true);
        }
        return $this;
    }

    /**
     * Get a service called `$name` throwing an error by default if it does not exist yet and can not be created
     *
     * @param   string  $name
     * @param   array   $arguments
     * @param   int     $failure
     * @return  mixed
     * @throws  \MVCFundamental\Exception\ErrorException
     * @api
     */
    public function getService($name, array $arguments = array(), $failure = self::FAIL_WITH_ERROR)
    {
        if ($this->hasService($name)) {
            return $this->_services->offsetGet($name);
        } elseif ($this->_services_constructors->offsetExists($name)) {
            $this->_constructService($name, $arguments);
            if ($this->hasService($name)) {
                return $this->_services->offsetGet($name);
            }
        }
        if ($failure & self::FAIL_WITH_ERROR) {
            throw new ErrorException(
                sprintf('Service "%s" not known and can not be created!', $name)
            );
        }
        return null;
    }

    /**
     * Test if a service exists in the container
     *
     * @param   string  $name
     * @return  mixed
     * @api
     */
    public function hasService($name)
    {
        return (bool) $this->_services->offsetExists($name);
    }

    /**
     * Unset a service if it is overwritable
     *
     * @param   string  $name
     * @return  mixed
     * @throws  \MVCFundamental\Exception\ErrorException
     * @api
     */
    public function unsetService($name)
    {
        if ($this->hasService($name)) {
            if (
                !$this->_services_protected->offsetExists($name) ||
                $this->_services_protected->offsetGet($name)!==true
            ) {
                $this->_services->offsetUnset($name);
            } else {
                throw new ErrorException(
                    sprintf('Can not unset a protected service (for "%s")!', $name)
                );
            }
        }
        return $this;
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
        self::set('front_controller', $app, false);
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
        if (!class_exists($logger_class) || !Helper::classImplements($logger_class, 'Psr\Log\LoggerInterface')) {
            throw new ErrorException(
                sprintf('A logger must exist and implement the "%s" interface (for class "%s")!',
                    'Psr\Log\LoggerInterface', $logger_class)
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
                self::getInstance()->setConstructor(
                    $var, function ($app, $name, array $arguments = array()) use ($val, $cls_index) {
                    return $app::apiFactory($cls_index, $val, $arguments);
                }
                );
            }
        }

    }

    /**
     * @param   \Exception $e
     * @return  mixed
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
     * @return  mixed
     * @throws  \MVCFundamental\Exception\ErrorException
     */
    public static function handleError($errno = 0, $errstr = '', $errfile = '', $errline = 0, array $errcontext = array())
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
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