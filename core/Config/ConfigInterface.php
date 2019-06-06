<?php
/**
 * @author Kemoy Campbell
 * Date: 12/26/18
 * Time: 5:30 PM
 */

namespace Bolzen\Core\Config;

interface ConfigInterface
{
    /**
     * This function returns the current environment of the app such as development,staging or production
     * @return string the current environment of the application
     */
    public function environment():string;

    /**
     * This function returns the current scheme that the app is running on
     * @return string http or https
     */
    public function scheme():string;

    /**
     * This function returns the current host of the application server.
     * @return string localhost or ip address or the domain of the hosting server
     */
    public function serverHost():string;

    /**
     * This function returns the project directory
     * @return string - the directory of the project
     */
    public function projectDirectory():string;

    /**
     * This function returns whether debug is enabled
     * @return bool true if enabled. False otherwise
     */
    public function isDebugEnabled():bool;

    /**
     * This function returns whether the application will be using a database or not
     * @return bool true if the application will be using a database. False otherwise
     */
    public function isDatabaseRequired():bool;

    /**
     * This function returned the name of the database the application is using
     * @return string the database name
     */
    public function databaseName():string;

    /**
     * This function returns the name of the database's Dsn
     * @return string return the database dsn
     */
    public function databaseDsn():string;

    /**
     * This function returns the password of the database
     * @return string the database password
     */
    public function databasePassword():string;

    /**
     * This function returns the username of the database
     * @return string the database user
     */
    public function databaseUser():string;

    /**
     * This return the name of the database's prefix
     * @return string database prefix
     */
    public function databasePrefix():string;

    /**
     * This function return the name of the database's host
     * @return string the database's host
     */
    public function databaseHost():string;

    /**
     * This function returns the base url of the application
     * @return string get base url
     */
    public function getBaseUrl():string;

    /**
     * This function returns the max log files
     * @return int - the maximum size of the log before rotating
     */
    public function getMaxLogFiles():int;

    /**
     * This function take the config xml variable and returns the value
     * @param string $configName - the xml configuration variable name on whose value to get
     * @return string - return the value associated with the xml variable
     * @throws \InvalidArgumentException if we are not able to located the xml variable
     */
    public function getXmlConfigValue(string $configName):string;

    public function isXmlConfigurationRequired():bool;
}
