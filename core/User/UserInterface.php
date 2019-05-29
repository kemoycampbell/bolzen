<?php
/**
 * @author Kemoy Campbell
 * Date: 12/29/18
 * Time: 5:00 PM
 */

namespace Bolzen\Core\User;

interface UserInterface
{
    /**
     * This function returns all the roles of the supplied user.
     * If the username is empty then it is assumed that the roles should
     * be that of the current logged user
     * @param string $username the user whose roles to get. The default username
     *                         is set to the current logged user
     * @return array the supplied user roles. Otherwise empty array
     */
    public function getRoles(string $username = ""):array;

    /**
     * The username of the current logged user
     * @return string the username of the current logged user. If no
     *                user has logged then anonymous is returned
     */
    public function getUserName():string;

    /**
     * This function returns an array of request user or users
     * @param string $columns the columns to obtain from the account table
     * @param string $where the where clauses to restrict to specific group of user(s)
     * @param array $bindings the bindings for the where clause
     * @return array return an array of the target user(s)
     */
    public function getUsers(string $columns, string $where = "", array $bindings = array()):array;

    /**
     * This function adds a new user to the account table
     * @param string $username the username of the user to add
     * @param string $password the user password. This will be hashed using sha256
     * @param bool $verified a boolean statement on whether the user's account is verified
     * @return bool true if the user was successful added. False otherwise
     */
    public function add(string $username, string $password, bool $verified = false):bool;

    /**
     * This removes a user from the account table
     * @param string $username the username of the user to remove
     * @return bool true if the user was successful removed. False otherwise
     */
    public function remove(string $username):bool;

    /**
     * Checks whether a user account is verified
     * @param string $username - the username of the account to check if verified
     * @return bool true if the account is verified. False otherwise
     */
    public function isVerified(string $username = ""):bool;

    /**
     * This function set a user account to verified.
     * @param string $username the username of the account to set to verified
     * @return bool true if the account was set as verified. False otherwise
     */
    public function makeAccountVerified(string $username):bool;

    /**
     * This checks whether the current logged user is anonymous
     * @return bool true if the user is anonymous. False otherwise
     */
    public function isAnonymous():bool;


    /**
     * This function checks on whether a user exist in the account table
     * @param string $username the username to check
     * @return bool true if the user exist. False otherwise
     */
    public function hasUser(string $username):bool;

    /**
     * This function is use to change the user's password. The underlying function uses sha256 for the password hashing
     * @param string $username the username to change
     * @param string $password the new password of the user
     * @return bool true if the password was successful changed. False otherwise
     */
    public function changePassword(string $username, string $password):bool;

    /**
     * This function returns the type of hash algorithm being used
     * @return string the hash algorithm that is used to hash the user's password
     */
    public function hashAlgorithm():string;

}