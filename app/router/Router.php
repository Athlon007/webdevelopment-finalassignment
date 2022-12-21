<?php

class Router
{
    public function route($request): void
    {
        $request = explode('?', $request)[0];
        if ($request == "/" || str_starts_with($request, "/home")) {
            $this->routeHome($request);
        } elseif (str_starts_with($request, "/admin")) {
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
            case "/home":
            case "/home/":
            case "/home/index":
            case "/home/index/":
                $controller->index();
                break;
            default:
                $this->route404();
                break;
        }
    }

    private function routeAdmin($request): void
    {
        require("../controllers/AdminController.php");
        $controller = new AdminController();

        require_once("../services/LoginService.php");
        $loginService = new LoginService();
        if (!$loginService->isSetup()) {
            // Page not setup? Go into first-time-setup page.
            $controller->setup();
            return;
        }
        if (!$loginService->isLoggedIn()) {
            // Not logged-in? Require to login first.
            $controller->login();
            return;
        }

        switch ($request) {
            case "/admin":
            case "/admin/":
                $controller->opinionsPanel();
                break;
            case "/admin/topics":
            case "/admin/topics/":
                $controller->topicsPanel();
                break;
            case "/admin/setup-ready":
            case "/admin/setup-ready/":
                $controller->setupComplete();
                break;
            default:
                $this->route404();
                break;
        }
    }

    private function route404(): void
    {
        http_response_code(404);
        require("../views/404.php");
    }
}
