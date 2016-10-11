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

/**
 * Created by IntelliJ IDEA.
 * User: d4rkfly3r
 * Date: 10/11/2016
 * Time: 11:57 AM
 */

namespace Core;


class Router
{

    public static function route($url)
    {
        $url_array = explode("/", $url);

        // The first part of the URL is the controller name
        $controller = isset($url_array[0]) ? '\\Controllers\\' . ucwords($url_array[0]) : '';
        array_shift($url_array);

        // The second part is the method name
        $action = isset($url_array[0]) ? $url_array[0] : '';
        array_shift($url_array);

        // The third part are the parameters
        $query_string = $url_array;

        // if controller is empty, redirect to default controller
        if (empty($controller)) {
            $controller = \Controllers\default_controller();
        }

        // if action is empty, redirect to index page
        if (empty($action)) {
            $action = 'index';
        }

//        $controller_name = $controller;
        $controller = ucwords($controller);
        $dispatch = new $controller();//new $controller($controller_name, $action);

        if ((int)method_exists($controller, $action)) {
            call_user_func_array(array($dispatch, $action), $query_string);
        } else {
            echo "ERROR!";
            /* Error Generation Code Here */
        }
    }
}