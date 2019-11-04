<?php
/**
 * @author Kemoy Campbell
 * Date: 12/29/18
 * Time: 5:42 PM
 */

namespace Bolzen\Core\User;

use Bolzen\Core\Database\DatabaseInterface;
use Bolzen\Core\Session\SessionInterface;
use Bolzen\Core\Tables\CoreTables;

class User implements UserInterface
{
    private $accountTable;
    private $database;
    private $session;
    private $anonymousUser;
    private $hashAlgorithm;

    public function __construct(SessionInterface $session, DatabaseInterface $database)
    {
        $this->database = $database;
        $this->session = $session->getSession();
        $this->accountTable = CoreTables::$ACCOUNT;
        $this->anonymousUser = "anonymous";
        $this->hashAlgorithm = "sha256";
    }

    /**
     * This function returns all the roles of the supplied user.
     * If the username is empty then it is assumed that the roles should
     * be that of the current logged user
     * @param string $username the user whose roles to get. The default username
     *                         is set to the current logged user
     * @return array the supplied user roles. Otherwise empty array
     */
    public function getRoles(string $username = ""): array
    {
        //if no username is supply then we assumed the current logged user
        if (empty($username)) {
            $username = $this->getUserName();
        }

        $sql = "SELECT name as role FROM roles INNER JOIN accountRoles ON
                accountRoles.roleId = roles.roleId WHERE accountRoles.username = ?";
        $bindings = array($username);

        return $this->database->genericSqlBuilder($sql, $bindings)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * The username of the current logged user
     * @return string the username of the current logged user. If no
     *                user has logged then anonymous is returned
     */
    public function getUserName(): string
    {
        return $this->session->get('username', $this->anonymousUser);
    }

    /**
     * This function returns an array of request user or users
     * @param string $columns the columns to obtain from the account table
     * @param string $where the where clauses to restrict to specific group of user(s)
     * @param array $bindings the bindings for the where clause
     * @return array return an array of the target user(s)
     */
    public function getUsers(string $columns, string $where = "", array $bindings = array()): array
    {
        return $this->database->select($this->accountTable, $columns, $where, $bindings)->fetchAll();
    }

    /**
     * This function adds a new user to the account table
     * @param string $username the username of the user to add
     * @param string $password the user password. This will be hashed using sha256
     * @param bool $verified a boolean statement on whether the user's account is verified
     * @return bool true if the user was successful added. False otherwise
     */
    public function add(string $username, string $password, bool $verified = false): bool
    {
        $columns = "username,password,verified";
        $password = hash($this->hashAlgorithm, $password);

        //fix the bug that when parameter is set to false nothing is assigned to the $verified variable
        if (!$verified) {
            $verified = 0;
        }

        $bindings = array($username, $password, $verified);

        return $this->database->insert($this->accountTable, $columns, $bindings);
    }

    /**
     * This removes a user from the account table
     * @param string $username the username of the user to remove
     * @return bool true if the user was successful removed. False otherwise
     */
    public function remove(string $username): bool
    {
        $where = "username = ?";
        $bindings = array($username);

        return $this->database->delete($this->accountTable, $where, $bindings);
    }

    /**
     * This checks whether the current logged user is anonymous
     * @return bool true if the user is anonymous. False otherwise
     */
    public function isAnonymous(): bool
    {
        return $this->getUserName() === $this->anonymousUser;
    }

    /**
     * This function checks on whether a user exist in the account table
     * @param string $username the username to check
     * @return bool true if the user exist. False otherwise
     */
    public function hasUser(string $username): bool
    {
        $columns = "username";
        $where = "username = ?";
        $bindings = array($username);

        return count($this->getUsers($columns, $where, $bindings)) > 0;
    }

    /**
     * This function is use to change the user's password. The underlying function uses sha256 for the password hashing
     * @param string $username the username to change
     * @param string $password the new password of the user
     * @return bool true if the password was successful changed. False otherwise
     */
    public function changePassword(string $username, string $password): bool
    {
        $columns = "password";
        $password = hash($this->hashAlgorithm, $password);
        $where = "username = ?";
        $bindings = array($password, $username);
        return $this->database->update($this->accountTable, $columns, $where, $bindings);
    }

    /**
     * Checks whether a user account is verified
     * @param string $username - the username of the account to check if verified
     * @return bool true if the account is verified. False otherwise
     */
    public function isVerified(string $username = ""): bool
    {
        if (empty($username)) {
            $username = $this->getUserName();
        }
        $columns = "username";
        $where = "username = ? AND verified = ?";
        $bindings = array($username, true);
        return $this->database->select($this->accountTable, $columns, $where, $bindings)->rowCount() > 0;
    }

    /**
     * This function set a user account to verified.
     * @param string $username the username of the account to set to verified
     * @return bool true if the account was set as verified. False otherwise
     */
    public function makeAccountVerified(string $username): bool
    {
        $columns = "verified";
        $where = "username = ?";
        $bindings = array(true, $username);
        return $this->database->update($this->accountTable, $columns, $where, $bindings);
    }

    /**
     * This function returns the type of hash algorithm being used
     * @return string the hash algorithm that is used to hash the user's password
     */
    public function hashAlgorithm(): string
    {
        return $this->hashAlgorithm;
    }
}