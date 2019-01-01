<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/15/18
 * Time: 1:14 PM
 */

namespace Bolzen\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;

class ErrorController
{
    public function exception(FlattenException $exception)
    {
        $msg = 'Something went wrong! ('.$exception->getMessage().')';

        return new Response($msg, $exception->getStatusCode());
    }
}
