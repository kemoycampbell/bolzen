<?php


namespace Bolzen\Src\Home\Controller;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Home\Model\Users;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $usersModel = new Users();
        return $this->render($request, array("lists"=>$usersModel->getUsers()));
    }
}