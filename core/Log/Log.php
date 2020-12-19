<?php
/**
 * @author Kemoy Campbell
 * Date: 2/13/19
 * Time: 8:01 PM
 */

namespace Bolzen\Core\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log extends Logger
{
    public function __construct(int $size)
    {
        $path = __DIR__ . '/../../var/log/error.log';
        parent::__construct('BolzenLogReporter');
        $this->pushHandler(new StreamHandler($path, Logger::ERROR));
    }
}