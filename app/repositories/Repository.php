<?php
class Repository
{
    protected PDO $connection;

    public function __construct()
    {
        require("../config.php");
        try {
            $this->connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }
}