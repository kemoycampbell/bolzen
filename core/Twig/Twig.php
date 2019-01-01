<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/20/18
 * Time: 1:45 PM
 */

namespace Bolzen\Core\Twig;

use Bolzen\Core\AccessControl\AccessControlInterface;
use Bolzen\Core\Config\ConfigInterface;
use Bolzen\Core\Session\SessionInterface;

/**
 * Class Twig - provides many built-in features and methods for the php
 * @package Bolzen\Core\Twig
 */
class Twig
{
    private $twig;
    private $loader;
    private $config;
    private $session;
    private $accessControl;
    private $templatePath;

    public function __construct(
        ConfigInterface $config,
        SessionInterface $session,
        AccessControlInterface $accessControl
    ) {
        $this->config = $config;
        $this->accessControl = $accessControl;
        $this->session = $session;

        $this->templatePath = __DIR__.'/../../template/';
        $this->loader = new \Twig_Loader_Filesystem($this->templatePath);
        $this->twig = new \Twig_Environment($this->loader);

        //add the functions that we want twig to have access to so we can
        //access them from the twig templates
        $this->addAssetsFunction();
        $this->addUploadFunction();
        $this->addHasRoleFunction();
        $this->addUrlFunction();
        $this->addGetTokenFunction();
        $this->addHtmlDecodeFunction();
    }

    /**
     * Provide the function to include the needed file which contains all folders and files
     * that which would be used for an assets.
     */
    private function addAssetsFunction()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {

            $file = sprintf('assets/%s', ltrim($asset, '/'));

            $absolute = $this->config->getBaseUrl().$file;

            return $absolute;
        }));
    }

    /**
     * Provide the function to set up and or obtain files which contains
     * upload configuration to set the base url with it
     */
    private function addUploadFunction()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('upload', function ($path) {

            $file = sprintf('uploads/%s', ltrim($path, '/'));
            $absolute = $this->config->getBaseUrl().$file;
            return $absolute;
        }));
    }

    /**
     * The function to add a function which control whether who has a role and not
     */
    private function addHasRoleFunction()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('hasRole', function ($role, $user = "") {
            return $this->accessControl->hasRole($role, $user);
        }));
    }

    /**
     * Add the base url to the function
     */
    private function addUrlFunction()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('url', function ($path) {
            return $this->config->getBaseUrl().ltrim($path, "/");
        }));
    }

    /**
     * Add the current token to the function which would be later use for later
     */
    private function addGetTokenFunction()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('getToken', function () {
            return $this->accessControl->getCSRFToken();
        }));
    }

    /**
     * Provide the function which add the HTML decode feature to the function
     * which lets the php the ability to understand the HTML
     */
    public function addHtmlDecodeFunction()
    {
        $this->twig->addFunction(new \Twig_SimpleFunction('html_decode', function ($code) {
            echo html_entity_decode($code);
        }));
    }

    /**
     * Return the twig environment instance
     * @return \Twig_Environment twig environment instance
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * Return twig loader file system
     * @return \Twig_Loader_Filesystem twig loader file system instance
     */
    public function getLoader()
    {
        return $this->loader;
    }
}
