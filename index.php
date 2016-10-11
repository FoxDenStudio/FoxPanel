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
 * Time: 11:41 AM
 */

define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', dirname(__FILE__) . DS . 'app');
define('PUBLIC_ROOT', dirname(__FILE__) . DS . 'public');

define('DEVELOPMENT_ENVIRONMENT', true);

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'FoxPanel');
define('DB_CHARSET', 'utf-8');

define('PATH', 'http://foxpanel.local/');
define('WEBSITE_TITLE', 'FoxPanel');

define('DEFAULT_CONTROLLER', 'Index');

$url = isset($_GET['url']) ? $_GET['url'] : 'index';

function __autoload($FQCN)
{
    if (file_exists(APP_ROOT . DS . $FQCN . '.php')) {
        require_once(APP_ROOT . DS . $FQCN . '.php');
    }
}

use Core\Router;

Router::route($url);