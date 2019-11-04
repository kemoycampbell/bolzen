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

#feel free to delete the examples below
////home index path
$routes->add('Home/index', new Route("index",array(
    '_controller'=>'\Bolzen\Src\Home\Controller\HomeController::index'
)));

###############################
# Do not modify below
##############################
return $routes->getRouteCollection();
