<?php



function my_autoloader($class_name)
{
    $pathInfo = pathinfo(__FILE__);
    $dir = $pathInfo['dirname'] . '/';
    if (file_exists($dir . $class_name . '.php')) {
        require_once($dir . $class_name . '.php');
    }
}

spl_autoload_register('my_autoloader');