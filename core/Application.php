<?php

namespace app\core;

class Application
{
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Database $database;
    public static Application $app;

    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->database = new Database("eu-cdbr-west-03.cleardb.net", "heroku_31c07b3e6a84647", "bce7d80227bcfb", "6beb4b29", "utf8mb4");
    }

    public function run()
    {
        echo $this->router->resolve();
    }
}