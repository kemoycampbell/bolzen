<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/15/18
 * Time: 1:14 PM
 */

namespace Bolzen\Core\Controller;

use Bolzen\Core\Config\ConfigInterface;
use Bolzen\Core\Log\Log;
use Bolzen\Core\Model\Model;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ErrorController extends Model
{
    private $logger;

    public function __construct()
    {
        parent::__construct();
    }

    public function exception(FlattenException $exception)
    {

        if ($this->config->environment()==="dev") {
            $msg = 'Something went wrong! ('.$exception->getMessage().')';
        } else {
            $this->logger = new Log($this->config->getMaxLogFiles());
            if ($exception->getStatusCode()===Response::HTTP_NOT_FOUND) {
                $msg = "Page not found ".$exception->getMessage();
            } else {
                $msg = "We ran into an error while performing your request";
            }
            $this->logger->error($exception->getMessage());
        }
        return new Response($msg, $exception->getStatusCode());
    }
}
