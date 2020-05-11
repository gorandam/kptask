<?php

use Kptask\Core\App\WebSkeletor;

define('APP_PATH', __DIR__ . '/..');

error_reporting(E_ALL);
ini_set('display_errors', 1);

///**
// * Fix for GuzzleHttp Request from Globals method. If this function does not exist, it does not collect headers !?
// * @HACK
// *
// */
//if (!function_exists('getallheaders')) {
//    function getallheaders()
//    {
//        $headers = [];
//        foreach ($_SERVER as $name => $value) {
//            if (substr($name, 0, 5) == 'HTTP_') {
//                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
//            }
//        }
//        return $headers;
//    }
//}

include("../vendor/autoload.php");

/* @var \DI\Container $container */
$container = require APP_PATH . '/config/bootstrap.php';
$app = new WebSkeletor($container, $container->get(\Psr\Log\LoggerInterface::class));
$app->respond();
