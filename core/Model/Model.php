<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/20/18
 * Time: 2:38 PM
 */

namespace Bolzen\Core\Model;

use Bolzen\Core\AccessControl\AccessControlInterface;
use Bolzen\Core\Config\ConfigInterface;
use Bolzen\Core\Database\DatabaseInterface;
use Bolzen\Core\Session\SessionInterface;
use Bolzen\Core\User\UserInterface;

class Model
{
    use ModelTrait;

    protected $session;
    protected $database;
    protected $accessControl;
    protected $user;
    protected $config;

    public function __construct()
    {
        $modelLoader = ModelLoader::$modelInstance->getModelDependencies();

        $this->session = $this->setSession($modelLoader->session)->getSession();
        $this->user = $this->setUser($modelLoader->user);
        $this->database = $this->setDatabase($modelLoader->database);
        $this->accessControl = $this->setAccessControl($modelLoader->accessControl);
        $this->config = $this->setConfig($modelLoader->config);
    }

    private function setConfig(ConfigInterface $config)
    {
        return $config;
    }

    private function setSession(SessionInterface $session)
    {
        return $session;
    }

    private function setUser(UserInterface $user)
    {
        return $user;
    }

    private function setDatabase(DatabaseInterface $database)
    {
        return $database;
    }

    private function setAccessControl(AccessControlInterface $accessControl)
    {
        return $accessControl;
    }

}