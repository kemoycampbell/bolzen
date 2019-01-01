<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/18/18
 * Time: 1:45 AM
 */

use Bolzen\Core\Container\Container;
use Symfony\Component\DependencyInjection\Reference;

$container = new \Bolzen\Core\Container\Container();
$container = $container->getContainer();
#########################################
# DO NOT MODIFIED THE LINES ABOVE
#########################################
#Define your container dependencies or configuration here if need... Otherwise leave as default



###############################
# DO NOT MODIFY THE LINE BELOW
##############################
return $container;
