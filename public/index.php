<?php
namespace quiz;
require '../App/core/init.php';

session_start();
$router = new App();
$router->loadController();