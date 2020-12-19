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
    use ErrorTrait;

    protected SessionInterface $session;
    protected DatabaseInterface $database;
    protected AccessControlInterface $accessControl;
    protected UserInterface $user;
    protected ConfigInterface $config;

    public function __construct()
    {

        $this->session = ModelLoader::$session;
        $this->user = ModelLoader::$user;
        $this->database = ModelLoader::$database;
        $this->accessControl = ModelLoader::$accessControl;
        $this->config = ModelLoader::$config;
    }

}