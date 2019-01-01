<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$container = include_once __DIR__.'/../src/container.php';
$container->setParameter('routes', include_once __DIR__.'/../src/app.php');
$container->setParameter('charset', 'UTF-8');
$request = Request::createFromGlobals();

//rewriting & formatting the request url
$rewrite = new \Bolzen\Core\Request\Request($request, $container, $config, $accessControl);
$request = $rewrite->getFormattedRequest();

$response = $container->get('framework')->handle($request);
$response->prepare($request);
$response->send();
