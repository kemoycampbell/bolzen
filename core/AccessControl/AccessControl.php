<?php
/**
 * @author Kemoy Campbell
 * Date: 12/28/18
 * Time: 9:57 PM
 */

namespace Bolzen\Core\AccessControl;

use Bolzen\Core\Config\ConfigInterface;
use Bolzen\Core\Database\DatabaseInterface;
use Bolzen\Core\Session\SessionInterface;
use Bolzen\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class AccessControl implements AccessControlInterface
{
    private $user;
    private $session;
    private $database;
    private $userRolesTable;
    private $roleTable;
    private $maxOpenSSlRandomPseudocodeByteLength;
    private $config;

    private $agent;
    private $ip;

    public function __construct(
        UserInterface $user,
        SessionInterface $session,
        DatabaseInterface $database,
        ConfigInterface $config
    ) {
        $this->user = $user;
        $this->session = $session->getSession();
        $this->database = $database;
        $this->config = $config;
        $this->userRolesTable = "accountRoles";
        $this->roleTable = "roles";
        $this->maxOpenSSlRandomPseudocodeByteLength = 10;
        $this->ip = null;
        $this->agent = null;
    }

    /**
     * This function authenticate a user's account
     * @param string $username the username to authenticate
     * @param string $password the password to authenticate
     * @return bool return true if successful. False otherwise
     */
    public function authenticate(string $username, string $password): bool
    {
        $password = hash($this->user->hashAlgorithm(), $password);
        $columns = "username";
        $where = "username = ? AND password = ?";
        $bindings = array($username, $password);
        if (count($this->user->getUsers($columns, $where, $bindings))<=0) {
            return false;
        }

        $this->createAccessControlSession();
        return true;
    }

    /**
     * This function create a authorized session for the current logged
     * user
     */
    public function createAccessControlSession(): void
    {
        if (!$this->user->isAnonymous()) {
            $request = Request::createFromGlobals();
            $this->ip = $request->getClientIp();
            $this->agent = $request->headers->get('User-Agent', "");
            $username = $this->user->getUserName();


            $this->session->set('username', $username);
            $this->session->set('ip', $this->ip);
            $this->session->set('agent', $this->agent);
            $this->session->set('token', $this->generateRandomToken());
        }
    }

    /**
     * This function create a random token using openssl_random_pseudo_bytes
     * @return string a secure random token
     */
    public function generateRandomToken(): string
    {
        return bin2hex(openssl_random_pseudo_bytes($this->maxOpenSSlRandomPseudocodeByteLength));
    }

    /**
     * This function returns the current CSRF token
     * @return string the current CSRF token
     */
    public function getCSRFToken(): string
    {
        if ($this->session->get('token')===null) {
            $this->session->set('token', $this->generateRandomToken());
        }
        return $this->session->get('token');
    }

    /**
     * This function check if a given token is a valid CSRF token
     * @param string $token the token to validate
     * @return bool true if token is validate. False otherwise
     */
    public function isValidCSRFToken(string $token): bool
    {
        return $this->getCSRFToken() === $token;
    }

    /**
     * This function take a path and redirect the application to that path.
     * @param $path - the path to redirect to not including the base of the url
     */
    public function redirect($path): void
    {
        //stripping the / from the front
        if (substr_count($path, "/") > 1) {
            $path = ltrim($path, "/");
        }
        if (substr_count($path, "/")<=0) {
            $path = "/".$path;
        }

        $location = $this->config->getBaseUrl().$path;
        header("Location:".$location);
        exit;
    }

    /**
     * This function checks whether a user have a certain role
     * @param string $role the role to check
     * @param string $username the username. By default this is the current logged user
     * @return bool true if the user has the role. False otherwise
     */
    public function hasRole(string $role, string $username = ""): bool
    {
        $roles = $this->user->getRoles($username);

        if (empty($role) || empty($roles)) {
            return false;
        }

        $roles = array_column($roles, 'roles');

        return in_array(strtolower($role), array_map('strtolower', $roles));
    }

    /**
     * This function take an array of roles and check whether the user has
     * those roles
     * @param array $roles the roles to check
     * @param string $username username.
     * @return bool return true if the user has the roles. False otherwise
     */
    public function hasRoles(array $roles, string $username = ""): bool
    {
        $currentUserRoles = $this->user->getRoles($username);

        if (empty($currentUserRoles) || empty($roles)) {
            return false;
        }

        $currentUserRoles = array_column($currentUserRoles, "roles");

        return count(array_diff($roles, $currentUserRoles)) > 0;
    }

    /**
     * This function checks whether a session is valid.
     * @param string $token the CSRF token
     * @return bool true if the session is valid. False otherwise
     */
    public function isValidSession(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        if (!$this->isValidCSRFToken($token)) {
            return false;
        }

        //username doesnt match
        if ($this->user->isAnonymous() ||
            $this->session->get("username", "")!==$this->user->getUserName()) {
            return false;
        }

        //session doesnt match
        if ($this->agent===null || $this->session->get("agent")!==$this->agent) {
            return false;
        }

        //ip changes
        if ($this->ip===null || $this->session->get("ip")!==$this->ip) {
            return false;
        }

        return true;
    }

    /**
     * Redirect the user to a desired path if the session proven to be invalid
     * @param string $path
     * @param string $token
     */
    public function redirectIfInvalidSession(string $path, string $token): void
    {
        if (!$this->isValidSession($token)) {
            $this->redirect($path);
        }
    }

    /**
     * This function takes a role and attempt to return the role id
     * @param string $role - the role whose id to fetch
     * @return string the role id in string
     */
    public function getRoleID(string $role): string
    {
        //save the database a trip
        if (empty($role)) {
            return "";
        }

        $where = "name = ?";
        $bindings = array($role);
        $columns = "name";
        $data = $this->database->select($this->roleTable, $columns, $where, $bindings);

        if ($data->rowCount() > 0) {
            return $data->fetch()["roleId"];
        }

        return "";
    }

    /**
     * Assign a role to a user
     * @param string $username - the username to assign the role
     * @param string $role - the role to assign the user
     * @return bool - true if the role was successful added, false otherwise
     */
    public function assignRole(string $username, string $role): bool
    {
        if (empty($username) || empty($role)) {
            return false;
        }

        //prevent duplication
        if ($this->hasRole($role, $username)) {
            return false;
        }

        //can we get the role id?
        $roleID = $this->getRoleID($role);
        if (empty($roleID)) {
            return false;
        }

        $columns = "username,roleId";
        $bindings = array($username, $roleID);
        return $this->database->insert($this->userRolesTable, $columns, $bindings);
    }

    /**
     * Strip the user of a specific role
     * @param string $username - the username to remove the role from
     * @param string $role - the role to remove
     * @return bool - true if the role was removed. False otherwise
     */
    public function stripRole(string $username, string $role): bool
    {
        if (empty($username) || empty($role)) {
            return false;
        }

        $roleID = $this->getRoleID($role);

        if (empty($roleID)) {
            return false;
        }

        $where = "username = ? and roleId = ?";
        $bindings = array($username, $roleID);
        return $this->database->delete($this->userRolesTable, $where, $bindings);
    }
}