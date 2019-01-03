<?php
/**
 * @author Kemoy Campbell
 * Date: 1/2/19
 * Time: 6:16 PM
 */

namespace Bolzen\Src\Home\Controller;

use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Home\Model\HomeModel;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        //here we create a instance of our home model
        $homeModel = new HomeModel();

        //we will then pass the list of users in a user array to the twig context
        //so we can use it on the view
        return $this->render($request, array("users"=>$homeModel->listUsers()));
    }
}