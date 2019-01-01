<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/20/18
 * Time: 7:30 PM
 */

namespace Bolzen\Core\Controller;

use Bolzen\Core\Twig\Twig;

class ControllerLoader
{
    public static $twigInstance = null;
    public $twig;

    public function setTwig(Twig $twig)
    {
        if (self::$twigInstance===null) {
            self::$twigInstance = new ControllerLoader();
            self::$twigInstance->setTwig($twig);
        }

        $this->twig = $twig->getTwig();
    }

    public function getTwig()
    {
        return $this->twig;
    }

}