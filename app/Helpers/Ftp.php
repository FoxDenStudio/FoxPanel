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

class Ftp
{
    private $conn;
    private $basePath;

    public function __construct($host, $user, $pass, $base)
    {
        $this->basePath = $base . '/';
        $this->conn = ftp_connect($host);
        ftp_login($this->conn, $user, $pass);
    }

    public function close()
    {
        ftp_close($this->conn);
    }

    public function makeDirectory($dirToCreate)
    {
        if (!file_exists($this->basePath . $dirToCreate)) {
            ftp_mkdir($this->conn, $this->basePath . $dirToCreate);
        }
    }

    public function deleteDirectory($dir)
    {
        ftp_rmdir($this->conn, $this->basePath . $dir);
    }

    public function folderPermission($folderChmod, $permission)
    {
        if (ftp_chmod($this->conn, $permission, $folderChmod) !== false) {
            return "<p>$folderChmod chmoded successfully to " . $permission . "</p>\n";
        }
    }

    public function uploadFile($remoteFile, $localFile)
    {
        if (ftp_put($this->conn, $this->basePath . $remoteFile, $localFile, FTP_ASCII)) {
            return "<p>successfully uploaded $localFile to $remoteFile</p>\n";
        } else {
            return "<p>There was a problem while uploading $remoteFile</p>\n";
        }
    }

    public function deleteFile($file)
    {
        ftp_delete($this->conn, $this->basePath . $file);
    }
}
