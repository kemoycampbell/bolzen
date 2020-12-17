<?php
/**
 * @author Kemoy Campbell
 * Date: 12/28/18
 * Time: 5:35 PM
 */

namespace Bolzen\Core\AccessControl;

use Bolzen\Core\Column\ColumnInterface;
use Bolzen\Core\Filter\FilterInterface;

interface AccessControlInterface
{

    /**
     * This function authenticates a user's account
     * @param string $username the username to authenticate
     * @param string $password the user's account password
     * @param bool $createSession - true to create a authenticated session, false otherwise
     * @return bool return true if successful. False otherwise
     */
    public function authenticate(string $username, string $password, bool $createSession = false):bool;

    /**
     * This function create a authorized session for the current logged
     * user
     */
    public function createAccessControlSession():void;

    /**
     * This function create a random token using openssl_random_pseudo_bytes
     * @return string a secure random token
     */
    public function generateRandomToken():string;

    /**
     * This function returns the current CSRF token
     * @return string the current CSRF token
     */
    public function getCSRFToken():string;

    /**
     * This function check if a given token is a valid CSRF token
     * @param string $token the token to validate
     * @return bool true if token is validate. False otherwise
     */
    public function isValidCSRFToken(string $token):bool;

    /**
     * This function take a path and redirect the application to that path.
     * @param $path - the path to redirect to not including the base of the url
     */
    public function redirect($path):void;

    /**
     * This function checks whether a user have a certain role
     * @param string $role the role to check
     * @param FilterInterface|null $filter
     * @return bool true if the user has the role. False otherwise
     */
    public function hasRole(string $role, FilterInterface $filter = null):bool;

    /**
     * This function take an array of roles and check whether the user has
     * those roles
     * @param array $roles the roles to check
     * @param FilterInterface $filter
     * @return bool return true if the user has the roles. False otherwise
     */
    public function hasRoles(array $roles, FilterInterface $filter):bool;

    /**
     * This function checks whether a session is valid.
     * @param string $token the CSRF token
     * @return bool true if the session is valid. False otherwise
     */
    public function isValidSession(string $token):bool;

    /**
     * Redirect the user to a desired path if the session proven to be invalid
     * @param string $path
     * @param string $token
     */
    public function redirectIfInvalidSession(string $path, string $token):void;

    /**
     * This function takes a role and attempt to return the role id
     * @param string $role - the role whose id to fetch
     * @return string the role id in string
     */
    public function getRoleID(string $role):string;

    /**
     * Assign a role to a user
     * @param string $username - the username to assign the role
     * @param string $role - the role to assign the user
     * @param ColumnInterface|null $column
     * @return bool - true if the role was successful added, false otherwise
     */
    public function assignRole(
        string $username,
        string $role,
        ColumnInterface $column = null
    ):bool;

    /**
     * Strip the user of a specific role
     * @param FilterInterface $filter
     * @param string $role - the role to remove
     * @return bool - true if the role was removed. False otherwise
     */
    public function stripRole(FilterInterface $filter, string $role):bool;

    public function getPath():string;
}
