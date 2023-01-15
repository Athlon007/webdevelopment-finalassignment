<?php
class HomeController
{
    public function index(): void
    {
        $sortby = "popular";
        if (isset($_GET) && isset($_GET["sortby"]) && $_GET["sortby"] == "new") {
            $sortby = "new";
        }

        require("../views/home/index.php");
    }
}
