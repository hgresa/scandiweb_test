<?php

namespace app\core;


class Database
{
    public  string $host;
    public  string $database;
    public  string $user;
    public  string $password;
    public  string $charset;

    public function __construct($host, $database, $user, $password, $charset)
    {
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
        $this->charset = $charset;
    }
}