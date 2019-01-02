<?php
/**
 * @author Kemoy Campbell
 * Date: 1/1/19
 * Time: 4:55 PM
 */

namespace Bolzen\Src\Home;

use Bolzen\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return $this->render($request);
    }
}
