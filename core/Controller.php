<?php


namespace app\core;


class Controller
{
    protected static \PDO $database;
    protected object $db;

    public function __construct()
    {
        $db_info = Application::$app->database;

        $dsn = "mysql:host=$db_info->host;dbname=$db_info->database;charset=$db_info->charset";
        Controller::$database = new \PDO($dsn, $db_info->user, $db_info->password);

        $this->db = Controller::$database;
    }

    public function getPostData(): array
    {
        return Application::$app->request->getBody();
    }

    public function requestMethod(): string
    {
        return Application::$app->request->getMethod();
    }

    public function render($view, $params = []): string
    {
        return Application::$app->router->renderView($view, $params);
    }
}

