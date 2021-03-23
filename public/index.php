<?php

require_once __DIR__ . "/../vendor/autoload.php";
use app\core\Application;
use app\controllers\ScandiwebController;

$app = new Application(dirname(__DIR__));

$app->router->get("/", [ScandiwebController::class, "list_product"]);
$app->router->get("/product/list", [ScandiwebController::class, "list_product"]);
$app->router->get("/product/add", [ScandiwebController::class, "add_product"]);
$app->router->post("/product/add", [ScandiwebController::class, "add_product"]);
$app->router->post("/product/delete", [ScandiwebController::class, "delete_product"]);

$app->run();
