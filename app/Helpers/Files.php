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

class Files
{
    public static function getFileType($extension)
    {
        $images = ['jpg', 'gif', 'png', 'bmp'];
        $docs = ['txt', 'rtf', 'doc', 'docx', 'pdf'];
        $apps = ['zip', 'rar', 'exe', 'html'];
        $video = ['mpg', 'wmv', 'avi', 'mp4'];
        $audio = ['wav', 'mp3'];
        $db = ['sql', 'csv', 'xls', 'xlsx'];
        $programming = ['java', 'class', 'jar', 'cpp', 'h', 'cxx', 'c'];

        if (in_array($extension, $images)) {
            return 'Image';
        }
        if (in_array($extension, $docs)) {
            return 'Document';
        }
        if (in_array($extension, $apps)) {
            return 'Application';
        }
        if (in_array($extension, $video)) {
            return 'Video';
        }
        if (in_array($extension, $audio)) {
            return 'Audio';
        }
        if (in_array($extension, $db)) {
            return 'Database/Spreadsheet';
        }
        if (in_array($extension, $programming)) {
            return 'Programming';
        }

        return 'Other';
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function getBytesSize($value)
    {
        return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
            switch (strtolower($m[2])) {
                case 't':
                    $m[1] *= 1024;
                    break;
                case 'g':
                    $m[1] *= 1024;
                    break;
                case 'm':
                    $m[1] *= 1024;
                    break;
                case 'k':
                    $m[1] *= 1024;
                    break;
            }

            return $m[1];
        }, $value);
    }

    public static function getFolderSize($path)
    {
        $io = popen('/usr/bin/du -sb ' . $path, 'r');
        $size = intval(fgets($io, 80));
        pclose($io);

        return $size;
    }

    public static function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    public static function removeExtension($file)
    {
        if (strpos($file, '.')) {
            $file = pathinfo($file, PATHINFO_FILENAME);
        }

        return $file;
    }
}
