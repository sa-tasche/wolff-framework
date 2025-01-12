<?php

namespace Wolff\Core;

use Wolff\Utils\Str;
use Wolff\Exception\BadControllerCallException;

class Controller
{

    const NAMESPACE = 'Controller\\';
    const EXISTS_ERROR = 'The controller class \'%s\' does not exists';
    const METHOD_EXISTS_ERROR = 'The controller class \'%s\' does not have a \'%s\' method';


    /**
     * Returns the controller with the giving name
     *
     * @param  string  $path  the controller path
     *
     * @return \Wolff\Core\Controller the controller
     */
    public static function get(string $path)
    {
        $path = Str::sanitizePath($path);

        //load controller default function and return it
        if (($controller = Factory::controller($path)) === null) {
            throw new BadControllerCallException(self::EXISTS_ERROR, $path);
        }

        return $controller;
    }


    /**
     * Returns the return value of the controller's method
     * or null in case of errors
     *
     * @param  string  $path  the controller path
     * @param  string  $method  the controller method
     * @param  array  $args  the method arguments
     *
     * @return mixed the return value of the controller's method
     * or null in case of errors
     */
    public static function method(string $path, string $method = 'index', array $args = [])
    {
        $controller = Factory::controller($path);

        if (!method_exists($controller, $method)) {
            throw new BadControllerCallException(self::METHOD_EXISTS_ERROR, $path, $method);
        }

        return call_user_func_array([$controller, $method], $args);
    }


    /**
     * Returns true if the controller exists,
     * false otherwise
     *
     * @param  string  $path  the path of the controller
     *
     * @return boolean true if the controller exists, false otherwise
     */
    public static function exists(string $path)
    {
        return class_exists(self::getClassname($path));
    }


    /**
     * Returns true if the controller's method exists, false otherwise
     *
     * @param  string  $path  the controller path
     * @param  string  $method  the controller method name
     *
     * @return boolean true if the controller's method exists, false otherwise
     */
    public static function hasMethod(string $path, string $method)
    {
        if (!self::exists($path)) {
            return false;
        }

        $path = self::getClassname($path);

        if (method_exists($path, $method)) {
            $reflection_method = new \ReflectionMethod($path, $method);

            return $reflection_method->isPublic();
        }

        return false;
    }


    /**
     * Returns the controller classname of the given path
     *
     * @return string The controller classname of the given path
     */
    private static function getClassname(string $path)
    {
        return self::NAMESPACE . str_replace('/', '\\', $path);
    }
}
