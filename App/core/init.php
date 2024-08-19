<?php
//require 'App.php';
//require 'config.php';
//require 'Controller.php';
//require 'DataBase.php';
//require 'Model.php';
require '../vendor/autoload.php';
require '../App/core/config.php';
//
//spl_autoload_register(function ($class) {
//    require $filename = dirname(__FILE__) . '\..\Model\\' . $class . '.php';
//});
spl_autoload_register(function ($class){
    $part = explode('\\', $class);
    $class = $part[1];
    $dirs = ['class/','Controller/','core/','Model/','View/'];
    foreach ($dirs as $dir){
        $filename = '../App/'.$dir . $class . '.php';
        if (file_exists($filename)){
            require "$filename";
            break;
        }
    }

});
