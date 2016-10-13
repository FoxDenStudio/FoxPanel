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

class Error extends Controller
{
    private $error = null;

    public function __construct($error)
    {
        parent::__construct();
        $this->error = $error;
    }

    public static function display($error, $class = 'alert alert-danger')
    {
        $row = '';
        if (is_array($error)) {
            foreach ($error as $error) {
                $row .= "<div class='$class'>$error</div>";
            }

            return $row;
        } else {
            if (isset($error)) {
                return "<div class='$class'>$error</div>";
            }
        }
    }

    public function index()
    {
        header('HTTP/1.0 404 Not Found');

        $data['title'] = '404';
        $data['error'] = $this->error;
        $this->view->set('title', '404');
        $this->view->set('error', $this->error);

        $this->view->renderTemplate('header');
        $this->view->render('error/404');
        $this->view->renderTemplate('footer');
    }
}
