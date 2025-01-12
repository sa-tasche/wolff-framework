<?php

namespace {

    if (!function_exists('bytesToString')) {

        /**
         * Returns the given size (in bytes) as a human-readable string
         *
         * @param  int  $size  size (in bytes)
         * @param  int  $precision  number of digits after the decimal point
         * @return string the size as a human-readable string
         */
        function bytesToString($size, $precision = 0)
        {
            $sizes = [ 'YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'KB', 'B' ];
            $total = count($sizes);

            while ($total-- && $size > 1024) {
                $size /= 1024;
            }

            return round($size, $precision) . $sizes[$total];
        }
    }

    if (!function_exists('arrayRemove')) {

        /**
         * Removes an element from the given array based on its value
         *
         * @param  array  $arr  the array
         * @param  mixed  $needle  the value to remove
         *
         * @return bool true if the element has been removed, false otherwise
         */
        function arrayRemove(array &$arr, $needle)
        {
            return \Wolff\Core\Helper::arrayRemove($arr, $needle);
        }
    }

    if (!function_exists('validateCsrf')) {

        /**
         * Returns true if the current request is safe from csrf
         * (cross site request forgery), false otherwise.
         *
         * This method combined with the 'csrf' tag in the template engine
         * is perfect for making secure forms that prevent csrf.
         *
         * @return bool true if the current request is safe from csrf,
         * false otherwise
         */
        function validateCsrf()
        {
            $key = WOLFF_CONFIG['csrf_key'];

            return ($_SERVER['REQUEST_METHOD'] === 'POST' &&
                isset($_POST[$key], $_COOKIE[$key]) &&
                $_POST[$key] === $_COOKIE[$key]) ||
                ($_SERVER['REQUEST_METHOD'] === 'GET' &&
                isset($_GET[$key], $_COOKIE[$key]) &&
                $_GET[$key] === $_COOKIE[$key]);
        }
    }

    if (!function_exists('path')) {

        /**
         * Returns the absolute path of the given relative path (supposed to be relative
         * to the project root).
         *
         * @param  string  $path  the relative path
         *
         * @return string the absolute path
         */
        function path(string $path = '')
        {
            return \Wolff\Core\Helper::getRoot($path);
        }
    }

    if (!function_exists('config')) {

        /**
         * Returns the given key of the configuration array or null
         * if it does not exists.
         * The key must be in dot syntax. Like 'user.name'.
         *
         * @param  string|null  $key  the configuration array key
         *
         * @return mixed the given key of the configuration array or null
         * if it does not exists.
         */
        function config(string $key = null)
        {
            $keys = explode('.', $key);
            $arr = \Wolff\Core\Config::get();

            if (!isset($key)) {
                return $arr;
            }

            foreach ($keys as $key) {
                if (!is_array($arr) || !array_key_exists($key, $arr)) {
                    return null;
                }

                $arr = &$arr[$key];
            }

            return $arr;
        }
    }

    if (!function_exists('getPublic')) {

        /**
         * Returns the public directory of the project
         *
         * @param  string  $path  the optional path to append
         *
         * @return string the public directory of the project
         */
        function getPublic(string $path = '')
        {
            return \Wolff\Core\Helper::getRoot('public/' . ltrim($path, '/'));
        }
    }

    if (!function_exists('wolffVersion')) {

        /**
         * Returns the current version of Wolff
         * @return string the current version of Wolff
         */
        function wolffVersion()
        {
            return WOLFF_CONFIG['version'];
        }
    }

    if (!function_exists('isAssoc')) {

        /**
         * Returns true if the given array is
         * associative (numbers as keys), false otherwise.
         *
         * @param  array  $arr  the array
         *
         * @return bool true if the given array is associative,
         * false otherwise
         */
        function isAssoc(array $arr)
        {
            return \Wolff\Core\Helper::isAssoc($arr);
        }
    }

    if (!function_exists('val')) {

        /**
         * Returns the key value of the
         * given array, or null if it doesn't exists.
         *
         * The key param can use the dot notation.
         *
         * @param  array  $arr  the array
         * @param  string|null  $key  the array key to obtain
         *
         * @return mixed the value of the specified key in the array
         */
        function val(array $arr, string $key = null)
        {
            $keys = explode('.', $key);

            if (is_null($key)) {
                return $arr;
            }

            foreach ($keys as $key) {
                if (!is_array($arr) ||
                    !array_key_exists($key, $arr)) {
                    return null;
                }

                $arr = &$arr[$key];
            }

            return $arr;
        }
    }

    if (!function_exists('echod')) {

        /**
         * Print a string and die
         */
        function echod(...$args)
        {
            foreach ($args as $arg) {
                echo $arg;
            }

            die();
        }
    }

    if (!function_exists('printr')) {

        /**
         * Print the given values in a nice looking way
         */
        function printr(...$args)
        {
            echo '<pre>';
            array_map('print_r', $args);
            echo '</pre>';
        }
    }

    if (!function_exists('printrd')) {

        /**
         * Print the given values in a nice looking way and die
         */
        function printrd(...$args)
        {
            echo '<pre>';
            array_map('print_r', $args);
            echo '</pre>';

            die();
        }
    }

    if (!function_exists('dumpd')) {

        /**
         * Var dump the given values and die
         */
        function dumpd(...$args)
        {
            array_map('var_dump', $args);
            die();
        }
    }

    if (!function_exists('redirect')) {

        /**
         * Make a redirection
         *
         * @param  string|null  $url  the url to redirect to
         * @param  int  $status  the HTTP status code
         */
        function redirect(string $url = null, int $status = 302)
        {
            //Set url to the homepage when null
            if (!isset($url)) {
                $http = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://';

                $project_dir = '';
                $root = \Wolff\Core\Helper::getRoot();
                if (strpos($root, $_SERVER['DOCUMENT_ROOT']) === 0) {
                    $project_dir = substr($root, strlen($_SERVER['DOCUMENT_ROOT']));
                }

                $directory = str_replace('\\', '/', $project_dir);

                if (substr($directory, -1) !== '/') {
                    $directory .= '/';
                }

                $url = $http . $_SERVER['HTTP_HOST'] . $directory;
            }

            header("Location: $url", true, $status);
            exit;
        }
    }

    if (!function_exists('isJson')) {

        /**
         * Returns true if the given string is a Json, false otherwise.
         * Notice: This function modifies the 'json_last_error' value
         *
         * @param  string  $str  the string
         *
         * @return bool true if the given string is a Json, false otherwise
         */
        function isJson(string $str)
        {
            json_decode($str);
            return json_last_error() === JSON_ERROR_NONE;
        }
    }

    if (!function_exists('toArray')) {

        /**
         * Returns the given variable as an associative array
         *
         * @param  string  $obj  the object
         *
         * @return mixed the given variable as an associative array
         */
        function toArray($obj)
        {
            //Json
            if (is_string($obj)) {
                json_decode($obj);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $obj = json_decode($obj);
                }
            }

            $new = [];

            //Object
            if (is_object($obj)) {
                $obj = (array) $obj;
            }

            //Array
            if (is_array($obj)) {
                foreach ($obj as $key => $val) {
                    $new[$key] = toArray($val);
                }
            } else {
                $new = $obj;
            }

            return $new;
        }
    }

    if (!function_exists('url')) {

        /**
         * Returns the complete url relative to the local site
         *
         * @param  string  $url  the url to redirect to
         *
         * @return string the complete url relative to the local site
         */
        function url(string $url = '')
        {
            $http = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://';

            $project_dir = '';
            $root = \Wolff\Core\Helper::getRoot();
            if (strpos($root, $_SERVER['DOCUMENT_ROOT']) === 0) {
                $project_dir = substr($root, strlen($_SERVER['DOCUMENT_ROOT']));
            }

            $directory = str_replace('\\', '/', $project_dir);

            if (substr($directory, -1) !== '/') {
                $directory .= '/';
            }

            return $http . $_SERVER['HTTP_HOST'] . $directory . $url;
        }
    }

    if (!function_exists('local')) {

        /**
         * Returns true if the current script is running in localhost,
         * false otherwise
         *
         * @return bool true if the current script is running in localhost,
         * false otherwise
         */
        function local()
        {
            $remote_addr = $_SERVER['REMOTE_ADDR'] ?? '::1';
            return $remote_addr === '127.0.0.1' || $remote_addr === '::1';
        }
    }

    if (!function_exists('average')) {

        /**
         * Returns the average value of the given array
         *
         * @param  array  $arr  the array with the numeric values
         * @return float|int the average value of the given array
         */
        function average(array $arr)
        {
            return array_sum($arr) / count($arr);
        }
    }

    if (!function_exists('getClientIP')) {

        /**
         * Returns the current client IP
         * @return string the current client IP
         */
        function getClientIP()
        {
            return \Wolff\Core\Helper::getClientIP();
        }
    }

    if (!function_exists('getCurrentPage')) {

        /**
         * Returns the current page relative to the project url
         * @return string the current page relative to the project url
         */
        function getCurrentPage()
        {
            $url = $_SERVER['REQUEST_URI'];
            $root = \Wolff\Core\Helper::getRoot();

            //Remove possible project folder from url
            if (strpos($root, $_SERVER['DOCUMENT_ROOT']) === 0) {
                $project_dir = substr($root, strlen($_SERVER['DOCUMENT_ROOT']));
                $url = substr($url, strlen($project_dir));
            }

            return $url;
        }
    }

    if (!function_exists('getPureCurrentPage')) {

        /**
         * Returns the current page without arguments
         * @return string the current page without arguments
         */
        function getPureCurrentPage()
        {
            $host = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

            if (($question_index = strpos($_SERVER['REQUEST_URI'], '?')) === false) {
                return $host . $_SERVER['REQUEST_URI'];
            }

            return $host . substr($_SERVER['REQUEST_URI'], 0, $question_index);
        }
    }

    if (!function_exists('getBenchmark')) {

        /**
         * Returns the time between the page load start and the current time
         * @return float the time between the page load start and the current time
         */
        function getBenchmark()
        {
            return microtime(true) - WOLFF_CONFIG['start'];
        }
    }

    if (!function_exists('isInt')) {

        /**
         * Returns true if the given variable
         * complies with an int, false otherwise
         *
         * @param  mixed  $int  the variable
         */
        function isInt($int)
        {
            return filter_var($int, FILTER_VALIDATE_INT) !== false;
        }
    }

    if (!function_exists('isFloat')) {

        /**
         * Returns true if the given variable
         * complies with an float, false otherwise
         *
         * @param  mixed  $float  the variable
         */
        function isFloat($float)
        {
            return filter_var($float, FILTER_VALIDATE_FLOAT) !== false;
        }
    }

    if (!function_exists('isBool')) {

        /**
         * Returns true if the given variable complies with an boolean,
         * false otherwise
         * Only the numeric values 1 and 0, and the strings
         * 'true', 'false', '1' and '0' are counted as boolean.
         *
         * @param  mixed  $bool  the variable
         */
        function isBool($bool)
        {
            $bool = strval($bool);
            return $bool === 'true' || $bool === 'false' ||
                $bool === '1' || $bool === '0';
        }
    }

}
