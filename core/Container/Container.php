<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/15/18
 * Time: 1:45 PM
 */

namespace Bolzen\Core\Container;

use Bolzen\Core\AccessControl\AccessControl;
use Bolzen\Core\Config\Config;
use Bolzen\Core\Controller\Controller;
use Bolzen\Core\Controller\ControllerLoader;
use Bolzen\Core\Database\Database;
use Bolzen\Core\Model\Model;
use Bolzen\Core\Model\ModelLoader;
use Bolzen\Core\Session\Session;
use Bolzen\Core\Twig\Twig;
use Bolzen\Core\User\User;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\EventDispatcher;
use Bolzen\Core\Framework\Bolzen;

class Container
{
    private $container;

    public function __construct()
    {
        $this->container = new DependencyInjection\ContainerBuilder();
        $this->loadDefaultContainers();
    }

    private function loadDefaultContainers()
    {
        $this->container->register('context', Routing\RequestContext::class);
        $this->container->register('matcher', Routing\Matcher\UrlMatcher::class)
            ->setArguments(array('%routes%', new Reference('context')))
        ;
        $this->container->register('request_stack', HttpFoundation\RequestStack::class);
        $this->container->register('controller_resolver', HttpKernel\Controller\ControllerResolver::class);
        $this->container->register('argument_resolver', HttpKernel\Controller\ArgumentResolver::class);

        $this->container->register('listener.router', HttpKernel\EventListener\RouterListener::class)
            ->setArguments(array(new Reference('matcher'), new Reference('request_stack')))
        ;
        $this->container->register('listener.response', HttpKernel\EventListener\ResponseListener::class)
            ->setArguments(array('%charset%'))
        ;
        $this->container->register('listener.exception', HttpKernel\EventListener\ExceptionListener::class)
            ->setArguments(array('Bolzen\Core\Controller\ErrorController::exception'))
        ;

        $this->container->register('config', Config::class);
        $this->container->register('session', Session::class);
        $this->container->register('database', Database::class)->setArguments(array(new Reference('config')));

        //$this->container->register('controller', Controller::class);
        $this->container->register('controllerLoader', ControllerLoader::class);
        $this->container->register('modelLoader', ModelLoader::class);

        $this->container->register('twig', Twig::class)
            ->setArguments(array(
                new Reference('config'),
                new Reference('session'),
                new Reference('accessControl')
            ))
        ;



        $this->container->register('user', User::class)
            ->setArguments(array(
                new Reference('session'),
                new Reference('database')
            ))
        ;

        $this->container->register('accessControl', AccessControl::class)
            ->setArguments(array(
                new Reference('user'),
                new Reference('session'),
                new Reference('database'),
                new Reference('config')

                ))
        ;

        $this->container->register('dispatcher', EventDispatcher\EventDispatcher::class)
            ->addMethodCall('addSubscriber', array(new Reference('listener.router')))
            ->addMethodCall('addSubscriber', array(new Reference('listener.response')))
            ->addMethodCall('addSubscriber', array(new Reference('listener.exception')))
        ;

        $this->container->register('framework', Bolzen::class)
            ->setArguments(array(
                new Reference('dispatcher'),
                new Reference('controller_resolver'),
                new Reference('request_stack'),
                new Reference('argument_resolver'),
            ))
        ;

        //passing the method parameters for specific classes
        $this->container->get('controllerLoader')->setTwig($this->container->get('twig'));
        $this->container->get('modelLoader')->setModelDependencies(
            $this->container->get('session'),
            $this->container->get('database'),
            $this->container->get('accessControl'),
            $this->container->get('user')
        );
    }
    public function getContainer()
    {
        return $this->container;
    }
}
