<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/21/18
 * Time: 12:05 PM
 */

namespace Bolzen\Core\Model;

use Bolzen\Core\AccessControl\AccessControlInterface;
use Bolzen\Core\Database\DatabaseInterface;
use Bolzen\Core\Session\SessionInterface;
use Bolzen\Core\User\UserInterface;

class ModelLoader
{
    private $session;
    private $database;
    private $accessControl;
    private $user;

    public static $modelInstance;

    public function setModelDependencies(
        SessionInterface $session,
        DatabaseInterface $database,
        AccessControlInterface $accessControl,
        UserInterface $user
    ) {
        if (self::$modelInstance===null) {
            self::$modelInstance = new self();
            self::$modelInstance->setModelDependencies($session, $database, $accessControl, $user);
        }

        $this->session = $session;
        $this->database = $database;
        $this->accessControl = $accessControl;
        $this->user = $user;
    }

    public function getModelDependencies():\stdClass
    {
        $std = new \stdClass();
        $std->database = $this->database;
        $std->accessControl = $this->accessControl;
        $std->session = $this->session;
        $std->user = $this->user;

        return $std;
    }
}
