<?php
/**
 * @author Kemoy Campbell
 * Date: 12/28/18
 * Time: 10:06 PM
 */

namespace Bolzen\Core\Session;

class Session implements SessionInterface
{
    private $session;

    /**
     * Session constructor - initialize the session and start it if it is
     * not already started
     */
    public function __construct()
    {
        $this->session = new \Symfony\Component\HttpFoundation\Session\Session();
        if (!$this->session->isStarted()) {
            $this->session->start();
        }
    }


    /**
     * Return the current session
     * @return \Symfony\Component\HttpFoundation\Session\Session the current active session
     */
    public function getSession(): \Symfony\Component\HttpFoundation\Session\Session
    {
        return $this->session;
    }
}