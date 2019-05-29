<?php
/**
 * @author Kemoy Campbell
 * Date: 2/13/19
 * Time: 8:01 PM
 */

namespace Bolzen\Core\Log;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Log extends Logger
{
    //private
    public function __construct(int $size)
    {
        $path = __DIR__ . '/../../var/log/error.log';
        parent::__construct('logReporter', array(new RotatingFileHandler($path, $size, Logger::ERROR)));
    }

}