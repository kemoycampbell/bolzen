<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/15/18
 * Time: 1:14 PM
 */

namespace Bolzen\Core\Controller;

use Bolzen\Core\Log\Log;
use Bolzen\Core\Model\Model;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function exception(FlattenException $exception)
    {

        if ($this->config->isEnvironmentDevelopment()) {
            $msg = 'Something went wrong! ('.$exception->getMessage().')';
        } else {
            if ($exception->getStatusCode()===Response::HTTP_NOT_FOUND) {
                $page = $exception->getPrevious()->getTrace()[1]['args'][0][1];
                $msg = "The page $page doesnt exist";
            } else {
                $msg = "We ran into an error while performing your request";
            }
            $logger = new Log($this->config->getMaxLogFiles());
            $logger->error($msg);

        }
        return new Response($msg, $exception->getStatusCode());
    }
}
