<?php
/**
 * This file is part of FoxPanel, licensed under the MIT License
 *
 * Copyright (c) 2016. FoxDenStudio - http://foxdenstudio.net/
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Core;

class Router
{
    public static $routes = [];

    public static $methods = [];

    public static $callbacks = [];

    public static $errorCallback;

    /** Set route patterns */
    public static $patterns = [
        ':any' => '[^/]+',
        ':num' => '-?[0-9]+',
        ':all' => '.*',
        ':hex' => '[[:xdigit:]]+',
        ':uuidV4' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}',
    ];

    public static function __callstatic($method, $params)
    {
        $uri = dirname($_SERVER['PHP_SELF']) . '/' . $params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }

    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace('//', '/', self::$routes);

        $query = '';
        $q_arr = [];
        if (strpos($uri, '&') > 0) {
            $query = substr($uri, strpos($uri, '&') + 1);
            $uri = substr($uri, 0, strpos($uri, '&'));
            $q_arr = explode('&', $query);
            foreach ($q_arr as $q) {
                $qobj = explode('=', $q);
                $q_arr[] = [$qobj[0] => $qobj[1]];
                if (!isset($_GET[$qobj[0]])) {
                    $_GET[$qobj[0]] = $qobj[1];
                }
            }
        }

        if (in_array($uri, self::$routes)) {
            $route_pos = array_keys(self::$routes, $uri);
            foreach ($route_pos as $route) {
                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    if (!is_object(self::$callbacks[$route])) {
                        self::invokeObject(self::$callbacks[$route]);
                        return;
                    } else {
                        call_user_func(self::$callbacks[$route]);
                        return;
                    }
                }
            }
        } else {
            $pos = 0;

            foreach (self::$routes as $route) {
                $route = str_replace('//', '/', $route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {

                        array_shift($matched);

                        if (!is_object(self::$callbacks[$pos])) {
                            self::invokeObject(self::$callbacks[$pos], $matched);
                            return;
                        } else {
                            call_user_func_array(self::$callbacks[$pos], $matched);
                            return;
                        }
                    }
                }
                $pos++;
            }
        }

        if (!self::$errorCallback) {
            self::$errorCallback = function () {
                header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");

                $data['title'] = '404';
                $data['error'] = 'Oops! Page not found..';
                $view = new View();
                $view->renderTemplate('header', $data);
                $view->render('Error/404', $data);
                $view->renderTemplate('footer', $data);
            };
        }
        if (!is_object(self::$errorCallback)) {
            self::invokeObject(self::$errorCallback, null, 'No routes found.');
            return;
        } else {
            call_user_func(self::$errorCallback);
            return;
        }

    }

    public static function invokeObject($callback, $matched = null, $msg = null)
    {
        $last = explode('/', $callback);
        $last = end($last);

        $segments = explode('@', $last);

        $controller = $segments[0];
        $method = $segments[1];

        $controller = new $controller($msg);

        call_user_func_array([$controller, $method], $matched ? $matched : []);
    }
}
