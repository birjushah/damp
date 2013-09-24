<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
 
 // Initialize Request Microtime
if (!defined('REQUEST_MICROTIME')) {
	define('REQUEST_MICROTIME', microtime(true));
}

// Initialize Base Dir
if (!defined('BASE_DIR')) {
	define('BASE_DIR', realpath(__DIR__ . "/../"));
}
// Initialize Request Microtime
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
