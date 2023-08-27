<?php

namespace App\Core;

use App\Core\Support\Csrf;
use JetBrains\PhpStorm\NoReturn;

class Controller
{
    public View $view;
    public Session $session;
    public Csrf $csrf;
    public string $action = "";

    public function __construct()
    {
        $this->view = new View();
        $this->session = new Session();
        $this->csrf = new Csrf();
        $this->view->setLayout(Config::get('default_layout'));
        $this->onConstruct();
    }

    #[NoReturn] public function json_response($resp): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(Response::OK);
        echo json_encode($resp);
        exit;
    }

    public function previous_pagination($currentPage)
    {
        return $currentPage > 1 ? $currentPage - 1 : false;
    }

    public function next_pagination($currentPage, $numberOfPages)
    {
        return $currentPage + 1 <= $numberOfPages ? $currentPage + 1 : 1;
    }

    public function onConstruct(): void
    {
    }
}