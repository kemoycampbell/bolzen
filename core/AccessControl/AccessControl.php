<?php
/**
 * @author Kemoy Campbell
 * Date: 12/28/18
 * Time: 9:57 PM
 */

namespace Bolzen\Core\AccessControl;

use Bolzen\Core\Column\ColumnInterface;
use Bolzen\Core\Config\ConfigInterface;
use Bolzen\Core\Database\DatabaseInterface;
use Bolzen\Core\Filter\Filter;
use Bolzen\Core\Filter\FilterInterface;
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
    private $maxOpenSSlRandomPseudoCodeByteLength;
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
        $this->maxOpenSSlRandomPseudoCodeByteLength = 10;
    }

    /**
     * This function authenticates a user's account
     * @param string $username the username to authenticate
     * @param string $password the user's account password
     * @param bool $createSession - true to create a authenticated session, false otherwise
     * @return bool return true if successful. False otherwise
     */
    public function authenticate(string $username, string $password, bool $createSession = false): bool
    {
        $username = trim($username);
        $password = trim($password);

        if (empty($username) || empty($password)) {
            return false;
        }

        $password = hash($this->user->hashAlgorithm(), $password);
        $columns = "username";
        $where = "username = ? AND password = ?";
        $bindings = array($username, $password);

        //locating the user's account
        if (empty($this->user->getUsers($columns, $where, $bindings))) {
            return false;
        }

        if ($createSession) {
            $this->session->set("username", $username);
            $this->createAccessControlSession();
        }

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
        return bin2hex(openssl_random_pseudo_bytes($this->maxOpenSSlRandomPseudoCodeByteLength));
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
        $path = implode("/", array_filter(explode("/", $path)));

        $location = $this->config->getBaseUrl().$path;
        header("Location:$location");
        exit;
    }

    /**
     * This function checks whether a user have a certain role
     * @param string $role the role to check
     * @param FilterInterface|null $filter
     * @return bool true if the user has the role. False otherwise
     */
    public function hasRole(string $role, FilterInterface $filter = null): bool
    {
        $role = trim($role);

        //make an exception so we can support whether a role has anonymous
        if (is_null($filter)&& $role==="anonymous") {
            return $this->user->isAnonymous();
        }

        //otherwise we check to see if the user has the targeted role
        $roles = $this->user->getRoles($filter);
        if (empty($role) || empty($roles)) {
            return false;
        }

        $roles = array_column($roles, 'role');

        return in_array(strtolower($role), array_map('strtolower', $roles));
    }


    public function hasRoles(array $roles, FilterInterface $filter = null): bool
    {
        $currentUserRoles = $this->user->getRoles($filter);

        if (empty($currentUserRoles) || empty($roles)) {
            return false;
        }

        $currentUserRoles = array_column($currentUserRoles, "roles");

        return !empty(array_diff($roles, $currentUserRoles));
    }

    /**
     * This function checks whether a session is valid.
     * @param string $token the CSRF token
     * @return bool true if the session is valid. False otherwise
     */
    public function isValidSession(string $token): bool
    {
        $token = trim($token);

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
        $role = trim($role);

        //save the database a trip
        if (empty($role)) {
            return "";
        }

        $where = "name = ?";
        $bindings = array($role);
        $columns = "roleId";
        $data = $this->database->select($this->roleTable, $columns, $where, $bindings);

        return $data->rowCount() > 0 ? $data->fetch()[$columns] : "";
    }

    /**
     * Assign a role to a user
     * @param string $username - the username to assign the role
     * @param string $role - the role to assign the user
     * @param ColumnInterface|null $column - extra parameters
     * @return bool - true if the role was successful added, false otherwise
     */
    public function assignRole(
        string $username,
        string $role,
        ColumnInterface $column = null
    ): bool {
        $username = trim($username);
        $role = trim($role);

        if (empty($username) || empty($role)) {
            return false;
        }


        //ensure that the role is valid
        $roleID = $this->getRoleID($role);
        if (empty($roleID)) {
            return false;
        }

        //default columns
        $columns = "username,accountRoles.roleId";
        $bindings = array($username, $roleID);
        $filter = null;

        //do the user want to enter columns and filtering?
        if ($column!=null) {
            //append column to the columns
            $columns.=",".$column->columns();
            $bindings = array_merge($bindings, $column->bindings());


            //setting up the filter to check for role duplication
            //append the user parameters to the where field
            $fields = explode(",", $columns);
            $where= implode(" =? AND ", $fields)."=?";

            //pass the full clauses to the filter
            $filter = new Filter($where, $bindings);
        }

        //prevent role duplicating
        if ($this->hasRole($role, $filter)) {
            return false;
        }

        return $this->database->insert($this->userRolesTable, $columns, $bindings);
    }


    /**@inheritDoc */
    public function stripRole(FilterInterface $filter, string $role): bool
    {

        if (empty($role)) {
            return false;
        }
        $roleID = $this->getRoleID($role);

        if (empty($roleID)) {
            return false;
        }

        $where = $filter->where();
        $bindings = $filter->bindings();
        return $this->database->delete($this->userRolesTable, $where, $bindings);
    }

    public function getPath():string
    {
        return $this->config->getBaseUrl();
    }
}
