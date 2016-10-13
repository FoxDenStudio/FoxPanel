<?php
/**
 * SimpleMVC specifed directory default is '.'
 * If app folder is not in the same directory update it's path.
 */
$relDir = '.';

/* Set the full path to the docroot */
define('ROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

/* Make the application relative to the docroot, for symlink'd index.php */
if (!is_dir($relDir) and is_dir(ROOT . $relDir)) {
    $relDir = ROOT . $relDir;
}

/* Define the absolute paths for configured directories */
define('ROOT_DIR', realpath($relDir) . DIRECTORY_SEPARATOR);

/* Unset non used variables */
unset($relDir);

/* load composer autoloader */
require ROOT_DIR . 'vendor/autoload.php';

if (!is_readable(ROOT_DIR . 'app/Core/Config.php')) {
    die('No Config.php found, configure and rename Config.example.php to Config.php in app/Core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
define('ENVIRONMENT', 'development');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(E_ALL);
            break;
        case 'production':
            error_reporting(0);
            break;
        default:
            exit('The application environment is not set correctly.');
    }
}

/* initiate config */
new Core\Config();

/** load routes */
require ROOT_DIR . 'routes.php';
