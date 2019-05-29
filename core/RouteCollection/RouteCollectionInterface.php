<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/18
 * Time: 2:49 PM
 */

namespace Bolzen\Core\RouteCollection;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

interface RouteCollectionInterface
{
    /**
     * This method returns the symfony route collection instance
     * @return RouteCollection - symfony route collection instance
     */
    public function getRouteCollection():RouteCollection;

    /**
     * This method takes a given name and route and add it to the symfony's
     * route collection
     * @param string $name The route name
     * @param Route $route A Route instance
     */
    public function add(string $name, Route $route):void;

    /**
     * This method allows to add controller only using the add function.
     * However, a random route name is generated
     * @param Route $route A Route instance
     */
    public function addControllerOnly(Route $route):void;

    /**
     * This method allows to add controller only using the add function.
     * However, a random route name is generated
     * @param Route $route A Route instance
     */
    public function addAjax(Route $route):void;
}