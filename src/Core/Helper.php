<?php

namespace Wolff\Core;

class Helper
{

    /**
     * Returns true if the given array is
     * associative (numbers as keys), false otherwise.
     *
     * @param  array  $arr  the array
     *
     * @return bool true if the given array is associative,
     * false otherwise
     */
    public static function isAssoc(array $arr)
    {
        return (array_keys($arr) !== range(0, count($arr) - 1));
    }


    /**
     * Returns the current client IP
     * @return string the current client IP
     */
    public static function getClientIP()
    {
        $http_client_ip = filter_var($_SERVER['HTTP_CLIENT_IP'] ?? '', FILTER_VALIDATE_IP);
        $http_forwarded = filter_var($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '', FILTER_VALIDATE_IP);

        if (!empty($http_client_ip)) {
            return $http_client_ip;
        }

        if (!empty($http_forwarded)) {
            return $http_forwarded;
        }

        return $_SERVER['REMOTE_ADDR'] ?? '';
    }


    /**
     * Returns true if the given url array matches the route array,
     * false otherwise.
     *
     * @param  string  $route  the route that must be matched.
     * @param  array  $url  the url to test.
     * @param  int  $url_length  the url length.
     *
     * @return bool true if the given url array matches the route array,
     * false otherwise.
     */
    public static function matchesRoute(string $route, array $url, int $url_length)
    {
        $route = explode('/', $route);
        $route_length = count($route) - 1;

        for ($i = 0; $i <= $route_length && $i <= $url_length; $i++) {
            if ($route[$i] !== $url[$i] && $route[$i] !== '*') {
                break;
            }

            if ($route[$i] === '*' ||
                ($i === $url_length && $i === $route_length)) {
                return true;
            }
        }

        return false;
    }
}