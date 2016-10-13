<?php
use Helpers\Assets;
use Helpers\Hooks;
use Helpers\Url;

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Site meta -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo WEBSITE_TITLE . ' - ' . $this->data['title']; ?></title>

    <!-- CSS -->
    <?php
    Assets::css([
        Url::templatePath() . 'css/foundation.css',
        Url::templatePath() . 'css/app.css',
        Url::templatePath() . 'css/foundation-icons.css',
    ]);

    ?>

    <link rel="prefetch"
          href="https://originalmockups.com/thumbs/infinity-mockup-bundle-cover@2x-e835c6971edfad6286233db95569e4b0.jpg">
</head>
<body>
<div class="row">
    <div class="large-12 columns">
        <div class="top-bar">
            <div class="top-bar-title">
                <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
                    <button class="menu-icon" type="button" data-toggle style="width: 25px;height: inherit;"></button>
                </span>
                <span class="title">FoxPanel</span>
            </div>
            <div id="responsive-menu">
                <div class="top-bar-right">
                    <ul class="dropdown menu" data-dropdown-menu>
                        <li><a href="#">One</a></li>
                        <li><a href="#">Two</a></li>
                        <li><a href="#">Three</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<hr>-->
<div class="content">
