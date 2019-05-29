<?php
/**
 * @author Kemoy Campbell
 * Date: 12/28/18
 * Time: 10:04 PM
 */

namespace Bolzen\Core\Session;

use Symfony\Component\HttpFoundation\Session\Session;

interface SessionInterface
{
    /**
     * Return the current session
     * @return Session the current active session
     */
    public function getSession():Session;
}