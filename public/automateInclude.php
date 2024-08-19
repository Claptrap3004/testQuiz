<?php


spl_autoload_register(function ($class){
    $part = explode('\\', $class);
    $class = $part[1];
    $dirs = ['class/','Controller/','core/','Model/','View/'];
    foreach ($dirs as $dir){
        $filename = 'App/'.$dir . $class . '.php';
        if (file_exists($filename)){
            require "$filename";
            break;
        }
    }

});
