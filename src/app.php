<?php
use Symfony\Component\Routing\Route;
use Bolzen\Core\RouteCollection\RouteCollection;

$config = $container->get('config');
$accessControl = $container->get('accessControl');
$routes = new RouteCollection($config, $accessControl);

####################################
# Do not modify the line above
# Your Routes goes here
##################################

$routes->add('Home/index', new Route("index", array(
    '_controller'=>'\Bolzen\Src\Home\HomeController::index'
    )));

###############################
# Do not modify below
##############################

//echo "<pre>".print_r($routes, true)."</pre>";

return $routes->getRouteCollection();
