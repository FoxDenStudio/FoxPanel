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

namespace Helpers;

class Url
{
    public static function redirect($url = null, $fullpath = false, $code = 200)
    {
        $url = ($fullpath === false) ? DIR . $url : $url;

        if ($code == 200) {
            header('Location: ' . $url);
        } else {
            header('Location: ' . $url, true, $code);
        }
        exit;
    }

    public static function detectUri()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        $pathName = dirname($scriptName);

        if (strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        } else if (strpos($requestUri, $pathName) === 0) {
            $requestUri = substr($requestUri, strlen($pathName));
        }

        if (($requestUri == '/') || empty($requestUri)) {
            return '/';
        }

        $uri = parse_url($requestUri, PHP_URL_PATH);

        return str_replace(array('//', '../'), '/', ltrim($uri, '/'));
    }

    public static function templatePath($custom = false)
    {
        if ($custom == true) {
            return DIR . 'app/templates/' . $custom . '/';
        } else {
            return DIR . 'app/templates/' . TEMPLATE . '/';
        }
    }

    public static function relativeTemplatePath($custom = false)
    {
        if ($custom) {
            return 'app/templates/' . $custom . '/';
        } else {
            return 'app/templates/' . TEMPLATE . '/';
        }
    }

    public static function autoLink($text, $custom = null)
    {
        $regex = '@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@';

        if ($custom === null) {
            $replace = '<a href="http$2://$4">$1$2$3$4</a>';
        } else {
            $replace = '<a href="http$2://$4">' . $custom . '</a>';
        }

        return preg_replace($regex, $replace, $text);
    }

    public static function generateSafeSlug($slug)
    {
        setlocale(LC_ALL, 'en_US.utf8');

        $slug = preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $slug));

        $slug = htmlentities($slug, ENT_QUOTES, 'UTF-8');

        $pattern = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $slug = preg_replace($pattern, '$1', $slug);

        $slug = html_entity_decode($slug, ENT_QUOTES, 'UTF-8');

        $pattern = '~[^0-9a-z]+~i';
        $slug = preg_replace($pattern, '-', $slug);

        return strtolower(trim($slug, '-'));
    }

    public static function previous()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public static function segments()
    {
        return explode('/', $_SERVER['REQUEST_URI']);
    }

    public static function getSegment($segments, $id)
    {
        if (array_key_exists($id, $segments)) {
            return $segments[$id];
        }
    }

    public static function lastSegment($segments)
    {
        return end($segments);
    }

    public static function firstSegment($segments)
    {
        return $segments[0];
    }
}
