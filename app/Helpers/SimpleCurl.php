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

class SimpleCurl
{
    public static function get($url, $params = [])
    {
        if (is_array($params) && count($params) > 0) {
            $url = $url . '?' . http_build_query($params, '', '&');
        }
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ];
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function post($url, $fields = [])
    {
        if (is_array($fields) && count($fields) > 0) {
            $postFieldsString = http_build_query($fields, '', '&');
        } else {
            $postFieldsString = '';
        }

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $postFieldsString,
            CURLOPT_POST => true,
            CURLOPT_USERAGENT => 'FoxPanel Agent',
        ];
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function put($url, $fields = [])
    {
        if (is_array($fields) && count($fields) > 0) {
            $postFieldsString = http_build_query($fields, '', '&');
        } else {
            $postFieldsString = '';
        }
        $ch = curl_init($url);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $postFieldsString,
        ];
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
