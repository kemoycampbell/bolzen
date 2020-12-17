<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/21/18
 * Time: 12:05 PM
 */

namespace Bolzen\Core\Model;

use Bolzen\Core\AccessControl\AccessControlInterface;
use Bolzen\Core\Config\ConfigInterface;
use Bolzen\Core\Database\DatabaseInterface;
use Bolzen\Core\Session\SessionInterface;
use Bolzen\Core\User\UserInterface;
use stdClass;

class ModelLoader
{
    public static SessionInterface $session;
    public static DatabaseInterface $database;
    public static AccessControlInterface $accessControl;
    public static UserInterface $user;
    public static ConfigInterface $config;

    public static $modelInstance;

    public function setModelDependencies(
        SessionInterface $session,
        DatabaseInterface $database,
        AccessControlInterface $accessControl,
        UserInterface $user,
        ConfigInterface $config
    ) {
        if (self::$modelInstance===null) {
            self::$modelInstance = new self();
            self::$modelInstance->setModelDependencies($session, $database, $accessControl, $user, $config);
        }

        self::$session = $session;
        self::$database = $database;
        self::$accessControl = $accessControl;
        self::$user = $user;
        self::$config = $config;
    }
}
