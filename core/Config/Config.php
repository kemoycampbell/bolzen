<?php
/**
 * @author Kemoy Campbell
 * Date: 12/26/18
 * Time: 5:29 PM
 */

namespace Bolzen\Core\Config;

class Config implements ConfigInterface
{
    private const ENVIRONMENT = "environment";
    private const DEBUG = "debug";
    private const ENABLEDATABASE = "enableDatabase";
    private const HASH = "hash";
    private const DIRECTORY = "directory";
    private const SCHEME = "scheme";
    private const HOST = "host";
    private const DATABASE_NAME = "DB_NAME";
    private const DATABASE_HOST = "DB_HOST";
    private const DATABASE_USER = "DB_USER";
    private const DATABASE_PASSWORD = "DB_PASS";
    private const DATABASE_PREFIX = "DB_PREFIX";

    private $environment;
    private $config;
    private $dotEnv;
    private $server;

    public function __construct()
    {
        $this->loadConfig();
        $this->loadDotEnv();
    }

    private function loadDotEnv()
    {
        if (! $this->dotEnv && $this->isDatabaseRequired()) {
            // $this->dotEnv = new Dotenv();
            //$this->dotEnv->load(__DIR__.'/../../config/.env');

            //ensure we have the database parameters
            $this->databaseNameValidation();
            $this->databaseHostValidation();
            $this->databasePasswordValidation();
            $this->databaseUserValidation();
            $this->databasePrefixValidation();
        }
    }


    /**
     * This function returns the current environment of the app such as development,staging or production
     * @return string the current environment of the application
     */
    public function environment(): string
    {
        return $this->environment;
    }

    private function loadConfig()
    {
        if (!$this->config) {
            $this->config = include __DIR__ . '/../../config/config.php';

            //verifications
            $this->environmentValidation();
            $this->debugParameterValidation();
            $this->enabledDatabaseParameterValidation();
            $this->debugParameterValidation();
            $this->serverInfoValidation();
        }

    }

    /**
     * This function parses out the environment info from the config as well as validate it.
     */
    private function environmentValidation()
    {
        //only do this if the environment is not already set
        if (!$this->environment) {

            if (empty($this->config)) {
                throw new \InvalidArgumentException("config cannot be empty");
            }

            //is the environment key available in config.php?
            if (!isset($this->config[self::ENVIRONMENT])) {
                throw new \InvalidArgumentException("The variable environment is missing!");
            }

            //ensure we have a valid environment
            $allowedEnvironment = array("stage","prod","dev");
            $this->environment = $this->config[self::ENVIRONMENT];
            if (!in_array($this->environment, $allowedEnvironment)) {
                throw new \InvalidArgumentException("Invalid environment supplied. The allowed types are 
                stage, prod, and dev");
            }
        }
    }

    /**
     * This method parses the debug parameter as well as check to ensure that the parameter is acceptable
     */
    private function debugParameterValidation()
    {
        //does the parameter debug exist?
        if (!isset($this->config[self::DEBUG])) {
            throw new \InvalidArgumentException("The parameter debug is missing from the config");
        }

        //is it a boolean ?
        if (!is_bool($this->config[self::DEBUG])) {
            throw new \InvalidArgumentException("The parameter debug must be a boolean");
        }
    }

    /**
     * This function parse the enabled database parameter
     */
    private function enabledDatabaseParameterValidation()
    {
        if (!isset($this->config[self::ENABLEDATABASE])) {
            throw new \InvalidArgumentException("The parameter ".self::ENABLEDATABASE. " is missing from 
            config");
        }

        //is it a boolean ?
        if (!is_bool($this->config[self::ENABLEDATABASE])) {
            throw new \InvalidArgumentException("The parameter ".self::ENABLEDATABASE." must be a boolean");
        }
    }

    /**
     * This function parses out the server info from the config as well as validate them
     */
    private function serverInfoValidation()
    {
        if (!$this->server) {
            //can we locate the server info based on the environment
            if (!isset($this->config[$this->environment])) {
                throw new \InvalidArgumentException("The array $this->environment is missing!");
            }

            $this->server = $this->config[$this->environment];
            //are we missing any keys(directory, scheme and host)?

            if (!array_key_exists(self::DIRECTORY, $this->server)) {
                throw new \InvalidArgumentException(self::DIRECTORY. " is missing from the config");
            }elseif (!array_key_exists(self::SCHEME, $this->server)) {
                throw new \InvalidArgumentException(self::SCHEME." is missing from the config");
            }elseif (!array_key_exists(self::HOST, $this->server)) {
                throw new \InvalidArgumentException(self::HOST. " is missing from the config");
            }

            //scheme, host cannot be empty
            if (empty($this->server[self::SCHEME]) || empty($this->server[self::HOST])) {
                throw new \InvalidArgumentException("The parameter scheme and host cannot be empty");
            }
        }
    }

    /**
     * This function validate the database prefix
     */
    private function databasePrefixValidation()
    {
        if (empty(getenv(self::DATABASE_PREFIX))) {
            throw new \InvalidArgumentException(self::DATABASE_PREFIX. " cannot be empty");
        }
    }

    /**
     * This function validate the database name
     */
    private function databaseNameValidation()
    {
        if (empty(getenv(self::DATABASE_NAME))) {
            throw new \InvalidArgumentException(self::DATABASE_NAME. " cannot be empty");
        }
    }

    /**
     * This function validate the database user
     */
    private function databaseUserValidation()
    {
        if (empty(getenv(self::DATABASE_USER))) {
            throw new \InvalidArgumentException(self::DATABASE_USER." cannot be empty");
        }
    }

    /**
     * This function validates the database password
     */
    private function databasePasswordValidation()
    {
        if (empty(getenv(self::DATABASE_PASSWORD))) {
            throw new \InvalidArgumentException(self::DATABASE_PASSWORD. " cannot be empty");
        }
    }

    /**
     * This function validate the database host
     */
    private function databaseHostValidation()
    {
        if (empty(getenv(self::DATABASE_HOST))) {
            throw new \InvalidArgumentException(self::DATABASE_HOST. " cannot be empty");
        }
    }

    /**
     * This function returns the current scheme that the app is running on
     * @return string http or https
     */
    public function scheme(): string
    {
        return $this->server[self::SCHEME];
    }

    /**
     * This function returns the current host of the application server.
     * @return string localhost or ip address or the domain of the hosting server
     */
    public function serverHost(): string
    {
        return $this->server[self::HOST];
    }

    /**
     * This function returns the project directory
     * @return string - the directory of the project
     */
    public function projectDirectory(): string
    {
        return $this->server[self::DIRECTORY];
    }

    /**
     * This function returns whether debug is enabled
     * @return bool true if enabled. False otherwise
     */
    public function isDebugEnabled(): bool
    {
        return $this->config[self::DEBUG];
    }

    /**
     * This function returns whether the application will be using a database or not
     * @return bool true if the application will be using a database. False otherwise
     */
    public function isDatabaseRequired(): bool
    {
        return $this->config[self::ENABLEDATABASE];
    }

    /**
     * This function returned the name of the database the application is using
     * @return string the database name
     */
    public function databaseName(): string
    {
        return getenv(self::DATABASE_NAME);
    }

    /**
     * This function returns the name of the database's Dsn
     * @return string return the database dsn
     */
    public function databaseDsn(): string
    {
        //$dsn = 'mysql:host=localhost;dbname=testdb';
        return $this->databasePrefix().":host=".$this->databaseHost().";dbname=".$this->databaseName().";charset=utf8";
    }

    /**
     * This function returns the password of the database
     * @return string the database password
     */
    public function databasePassword(): string
    {
        return getenv(self::DATABASE_PASSWORD);
    }

    /**
     * This function returns the username of the database
     * @return string
     */
    public function databaseUser():string
    {
        return getenv(self::DATABASE_USER);
    }

    /**
     * This return the name of the database's prefix
     * @return string database prefix
     */
    public function databasePrefix(): string
    {
        return getenv(self::DATABASE_PREFIX);
    }

    /**
     * This function return the name of the database's host
     * @return string the database's host
     */
    public function databaseHost(): string
    {
        return getenv(self::DATABASE_HOST);
    }

    /**
     * This function returns the base url of the application
     * @return string get base url
     */
    public function getBaseUrl(): string
    {
        //https://localhost/projectDirectory
        return $this->scheme()."://".$this->serverHost()."/".$this->projectDirectory()."/";
    }
}