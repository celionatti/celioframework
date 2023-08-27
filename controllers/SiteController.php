<?php

namespace App\controllers;

use App\Core\Request;
use App\models\Users;
use App\Core\Response;
use App\Core\Controller;
use App\Core\Support\Csrf;

class SiteController extends Controller
{
    public $currentUser;

    public function onConstruct(): void
    {
        $this->view->setLayout('default');
        $this->currentUser = Users::getCurrentUser();
    }

    /**
     * @throws Exception
     */
    public function index(Request $request, Response $response)
    {
        if($request->isPost()) {
            $this->csrf->checkToken();
            dd($request->getBody());
        }

        $view = [
            'errors' => [],
        ];
        $this->view->render('welcome', $view);
    }

    public function users(Request $request, Response $response)
    {
        $id = $request->getParam('id');

        $csrf = new Csrf();

        dd($csrf);

        $view = [
            
        ];
        $this->view->render('users', $view);
    }
}
