<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/15/18
 * Time: 2:01 PM
 */

namespace Bolzen\Core\Controller;

use Bolzen\Core\AccessControl\AccessControlInterface;
use Bolzen\Core\Config\ConfigInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class Controller
{
    protected $twig;
    protected $contentType;

    public function __construct()
    {
        $controllerLoader = ControllerLoader::$twigInstance;
        $this->twig = $this->setTwig($controllerLoader->getTwig());
        $this->contentType = "text/html";
    }

    /**
     * @param Twig_Environment $twig set the environment in twig
     * @return Twig_Environment
     */
    private function setTwig(Twig_Environment $twig)
    {
        return $twig;
    }

    /**
     * @param string $baseUrl store the url
     * @return string url
     */
    public function setBaseUrl(string $baseUrl)
    {
        return $baseUrl;
    }

    /**
     * @param Request $request an object for file and context
     * @param array $context a list of array for context
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(Request $request, array $context = array())
    {
        //according to symfony document, extra will load the route path to $_route
        extract($request->attributes->all(), EXTR_SKIP);
        ob_start();

        $file = $_route.".php";
        echo $this->twig->render($file, $context);
        $response = new Response(ob_get_clean());
        $response->headers->set('Content-Type', $this->contentType);

        return $response;
    }


}
