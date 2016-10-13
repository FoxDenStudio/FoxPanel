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

class View
{
    public $data = [];
    private $headers = [];

    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }


    public function render($path, $error = false)
    {
        self::sendHeaders();

        require ROOT_DIR . "app/views/$path.php";
    }

    public function sendHeaders()
    {
        if (!headers_sent()) {
            foreach ($this->headers as $header) {
                header($header, true);
            }
        }
    }

    public function renderModule($path, $error = false)
    {
        self::sendHeaders();

        require ROOT_DIR . "app/Modules/$path.php";
    }

    public function renderTemplate($path, $custom = TEMPLATE)
    {
        self::sendHeaders();

        require ROOT_DIR . "app/templates/$custom/$path.php";
    }

    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    public function addHeaders(array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
    }
}
