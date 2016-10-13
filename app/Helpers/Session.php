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

class Session
{
    private static $sessionStarted = false;

    public static function init()
    {
        if (self::$sessionStarted == false) {
            session_start();
            self::$sessionStarted = true;
        }
    }

    public static function set($key, $value = false)
    {
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $value) {
                $_SESSION[SESSION_PREFIX . $name] = $value;
            }
        } else {
            $_SESSION[SESSION_PREFIX . $key] = $value;
        }
    }

    public static function pull($key)
    {
        if (isset($_SESSION[SESSION_PREFIX . $key])) {
            $value = $_SESSION[SESSION_PREFIX . $key];
            unset($_SESSION[SESSION_PREFIX . $key]);

            return $value;
        }

        return;
    }

    public static function get($key, $secondKey = false)
    {
        if ($secondKey == true) {
            if (isset($_SESSION[SESSION_PREFIX . $key][$secondKey])) {
                return $_SESSION[SESSION_PREFIX . $key][$secondKey];
            }
        } else {
            if (isset($_SESSION[SESSION_PREFIX . $key])) {
                return $_SESSION[SESSION_PREFIX . $key];
            }
        }

        return;
    }

    public static function id()
    {
        return session_id();
    }

    public static function regenerate()
    {
        session_regenerate_id(true);

        return session_id();
    }

    public static function display()
    {
        return $_SESSION;
    }

    public static function destroy($key = '', $prefix = false)
    {
        /* only run if session has started */
        if (self::$sessionStarted == true) {
            if ($key == '' && $prefix == false) {
                session_unset();
                session_destroy();
            } elseif ($prefix == true) {
                foreach ($_SESSION as $key => $value) {
                    if (strpos($key, SESSION_PREFIX) === 0) {
                        unset($_SESSION[$key]);
                    }
                }
            } else {
                unset($_SESSION[SESSION_PREFIX . $key]);
            }
        }
    }

    public static function message($sessionName = 'success')
    {
        $msg = self::pull($sessionName);
        if (!empty($msg)) {
            return "<div class='alert alert-{$sessionName} alert-dismissable'>
                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
                    <h4><i class='fa fa-check'></i> " . $msg . '</h4>
                  </div>';
        }
    }
}
