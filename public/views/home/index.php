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
 * Time: 8:48 PM
 */
$this->render('includes/header');
?>
    <div class="row">
        <div class="large-2 columns">
            <ul class="menu vertical callout secondary">
                <li><a href="#">Details</a></li>
                <li><a href="#">Chat</a></li>
                <li><a href="#">Console</a></li>
                <li><a href="#">Players</a></li>
                <li><a href="#">Files</a></li>
                <li><a href="#">Advanced</a></li>
            </ul>
        </div>
        <div class="large-10 columns">
            <div class="callout secondary">
                <div class="row">
                    <div class="large-3 columns left-tag">Power</div>
                    <div class="large-9 columns">
                        <div class="expanded button-group">
                            <a class="success button disabled">Start</a>
                            <a class="alert button">Stop</a>
                            <a class="warning button">Restart</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        Name
                    </div>
                    <div class="large-9 columns">
                        <input type="text" value="Minecraft Server #1" title="Server Name"/>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        Player Slots
                    </div>
                    <div class="large-9 columns">
                        <input type="number" value="16" title="Player Slots"/>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        Status
                    </div>
                    <div class="large-9 columns">
                        <div class="right-tag-content">Online | 5/16 Players</div>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        Status Banner
                    </div>
                    <div class="large-9 columns">
                        <a class="right-tag-content">Show</a>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        Public IP
                    </div>
                    <div class="large-9 columns">
                        <div class="right-tag-content">71.88.102.33</div>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        Port
                    </div>
                    <div class="large-9 columns">
                        <div class="right-tag-content">25565</div>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        World
                    </div>
                    <div class="large-9 columns">
                        <input type="text" value="world" title="Server Name"/>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        Minecraft EULA
                    </div>
                    <div class="large-9 columns">
                        <div class="expanded button-group">
                            <div class="button">Accept EULA</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="large-3 columns left-tag">
                        <if class="fi-wrench"></if>
                    </div>
                    <div class="large-9 columns">
                        <div class="expanded button-group">
                            <a href="#">Advanced Settings</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="callout secondary">
                <div class="row">
                    <div class="large-6 columns">
                        CPU (23%)
                        <div class="progress" role="progressbar" tabindex="0" aria-valuenow="50" aria-valuemin="0" aria-valuetext="50 percent" aria-valuemax="100">
                            <div class="progress-meter" style="width: 23%"></div>
                        </div>
                    </div>
                    <div class="large-6 columns">
                        Memory (93%)
                        <div class="progress alert" role="progressbar" tabindex="0" aria-valuenow="50" aria-valuemin="0" aria-valuetext="50 percent" aria-valuemax="100">
                            <div class="progress-meter" style="width: 93%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php
$this->render('includes/footer');