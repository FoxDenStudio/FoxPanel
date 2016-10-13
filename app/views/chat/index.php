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
?>
<div class="row">
    <div class="large-2 columns">
        <ul class="menu vertical callout secondary">
            <li><a href="/details/<?= $this->data['serverUUID'] ?>">Details</a></li>
            <li class="active"><a href="/chat/<?= $this->data['serverUUID'] ?>">Chat</a></li>
            <li><a href="/console/<?= $this->data['serverUUID'] ?>">Console</a></li>
            <li><a href="/players/<?= $this->data['serverUUID'] ?>">Players</a></li>
            <li><a href="/files/<?= $this->data['serverUUID'] ?>">Files</a></li>
            <li><a href="/advanced/<?= $this->data['serverUUID'] ?>">Advanced</a></li>
        </ul>
    </div>
    <div class="large-10 columns">
        <div class="callout secondary">
            <div class="row">
                <div class="large-3 columns">
                    <table class="hover">
                        <tr>
                            <td>d4rkfly3r_</td>
                        </tr>
                        <tr>
                            <td>joshsf</td>
                        </tr>
                        <tr>
                            <td>gravityfox</td>
                        </tr>
                        <tr>
                            <td>ferusgrim</td>
                        </tr>
                    </table>
                </div>
                <div class="large-9 columns">
                    <div class="input-group">
                        <input class="input-group-field" type="text" title="Input Message!"
                               placeholder="Input Message!">
                        <div class="input-group-button">
                            <input type="button" class="button" value="Send">
                        </div>
                    </div>
                    <div class="callout secondary chat-message-area">
                        <div class="row chat-message">
                            <div class="large-4 columns">01:00:02 &lt;d4rkfly3r_&gt;</div>
                            <div class="large-8 columns">This is a message xD</div>
                        </div>
                        <div class="row chat-message">
                            <div class="large-4 columns">01:00:03 &lt;gravityfox&gt;</div>
                            <div class="large-8 columns">Hey Josh!</div>
                        </div>
                        <div class="row chat-message">
                            <div class="large-4 columns">01:00:04 &lt;d4rkfly3r_&gt;</div>
                            <div class="large-8 columns">This is a message xD</div>
                        </div>
                        <div class="row chat-message">
                            <div class="large-4 columns">01:00:04 &lt;d4rkfly3r_&gt;</div>
                            <div class="large-8 columns">Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                Beatae consequatur cupiditate, excepturi explicabo fugiat molestias nihil nisi sequi!
                                Debitis incidunt mollitia temporibus ut velit, voluptatem? Commodi dignissimos excepturi
                                fuga quia?
                            </div>
                        </div>
                        <div class="row chat-message">
                            <div class="large-4 columns">01:00:04 &lt;d4rkfly3r_&gt;</div>
                            <div class="large-8 columns">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet,
                                consequuntur culpa, dolorem enim facilis ipsa itaque iure iusto laboriosam laborum
                                magnam, nostrum repellendus voluptate. Excepturi iste laudantium modi porro quisquam.
                            </div>
                        </div>
                        <div class="row chat-message">
                            <div class="large-4 columns">01:00:04 &lt;d4rkfly3r_&gt;</div>
                            <div class="large-8 columns">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet,
                                consequuntur culpa, dolorem enim facilis ipsa itaque iure iusto laboriosam laborum
                                magnam, nostrum repellendus voluptate. Excepturi iste laudantium modi porro quisquam.
                            </div>
                        </div>
                        <div class="row chat-message">
                            <div class="large-4 columns">01:00:04 &lt;d4rkfly3r_&gt;</div>
                            <div class="large-8 columns">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet,
                                consequuntur culpa, dolorem enim facilis ipsa itaque iure iusto laboriosam laborum
                                magnam, nostrum repellendus voluptate. Excepturi iste laudantium modi porro quisquam.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>