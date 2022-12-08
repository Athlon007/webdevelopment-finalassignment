<?php

class Router
{
    public function route($request) : void
    {
        if ($request == "/" || str_starts_with($request, "/home/")) {
            $this->routeHome($request);
        } elseif (str_starts_with($request, "/admin/")) {
            $this->routeAdmin($request);
        } else {
            $this->route404();
        }
    }

    private function routeHome($request): void
    {
        require("../controllers/HomeController.php");
        $controller = new HomeController();
        switch ($request) {
            case "/":
            case "/home/index":
                $controller->index();
                break;
            default:
                $this->route404();
                break;
        }
    }

    private function routeAdmin($request) : void
    {
        # code...
    }

    private function route404() : void
    {
        // TODO: add 404 redirect.
        echo "404";
    }
}