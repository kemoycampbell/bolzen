<?php
/**
 * @author Kemoy Campbell
 * Date: 12/26/18
 * Time: 5:29 PM
 */

namespace Bolzen\Core\Config;


use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class Config implements ConfigInterface
{
    private const SERVER_ENVIRONMENT = 'server_environment';
    private const ENVIRONMENT = "environment";
    private const DEBUG = "debug";
    private const ENABLED_DATABASE = "enable_database";
    private const DIRECTORY = "directory";
    private const SCHEME = "scheme";
    private const HOST = "host";
    private const DATABASE_NAME = "DB_NAME";
    private const DATABASE_HOST = "DB_HOST";
    private const DATABASE_USER = "DB_USER";
    private const DATABASE_PASSWORD = "DB_PASS";
    private const DATABASE_PREFIX = "DB_PREFIX";
    private const MAXIMUM_LOG = "max_files";
    private const ENV_PATH = __DIR__.'/../../config/.env';
    private const DEV_ENVIRONMENT = 'dev';
    private const STAGING_ENVIRONMENT = 'stage';
    private const PRODUCTION_ENVIRONMENT = 'prod';
    private const YAML_CONFIG_FILE = __DIR__ . '/../../config/config.yaml';

    private $environment;
    private $config;
    private $server;
    private $databaseParametersLoaded;

    public function __construct()
    {
        $this->loadEnv();
        $this->loadConfig();
        $this->loadDatabaseParametersIfNeed();
    }

    /**
     * This method loads the database parameters from the .env if needs
     */
    private function loadDatabaseParametersIfNeed():void
    {
        if (!$this->databaseParametersLoaded && $this->isDatabaseRequired()) {
            //ensure we have the database parameters
            $this->databaseNameValidation();
            $this->databaseHostValidation();
            $this->databasePasswordValidation();
            $this->databaseUserValidation();
            $this->databasePrefixValidation();
            $this->databaseParametersLoaded = true;
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

    /**
     * This function check if an .env exist and load it
     */
    private function loadEnv():void
    {
        if (file_exists(self::ENV_PATH)) {
            $dotEnv = new Dotenv();
            $dotEnv->load(self::ENV_PATH);
        }
    }

    private function loadConfig():void
    {
        if (!$this->config) {
            $this->config = Yaml::parseFile(self::YAML_CONFIG_FILE);

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
    private function environmentValidation():void
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
     * This function returns the max log files
     * @return int - the maximum size of the log before rotating
     */
    public function getMaxLogFiles(): int
    {
        if (!isset($this->config[self::MAXIMUM_LOG])) {
            throw new \InvalidArgumentException("The parameter max_files is missing from config");
        }

        $size = $this->config[self::MAXIMUM_LOG];

        if (!is_int($size)) {
            throw new \InvalidArgumentException("The parameter max_files must be an integer in config");
        }

        return $size;
    }


    /**
     * This method parses the debug parameter as well as check to ensure that the parameter is acceptable
     */
    private function debugParameterValidation():void
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
    private function enabledDatabaseParameterValidation():void
    {
        if (!isset($this->config[self::ENABLED_DATABASE])) {
            throw new \InvalidArgumentException("The parameter ".self::ENABLED_DATABASE. " is missing from 
            config");
        }

        //is it a boolean ?
        if (!is_bool($this->config[self::ENABLED_DATABASE])) {
            throw new \InvalidArgumentException("The parameter ".self::ENABLED_DATABASE." must be a boolean");
        }
    }

    /**
     * This function parses out the server info from the config as well as validate them
     */
    private function serverInfoValidation():void
    {
        if (!$this->server) {
            //can we locate the server info based on the environment
            if (!isset($this->config[self::SERVER_ENVIRONMENT][$this->environment])) {
                throw new \InvalidArgumentException("The array $this->environment is missing!");
            }

            $this->server = $this->config[self::SERVER_ENVIRONMENT][$this->environment];
            //are we missing any keys(directory, scheme and host)?

            if (!array_key_exists(self::DIRECTORY, $this->server)) {
                throw new \InvalidArgumentException(self::DIRECTORY. " is missing from the config");
            } elseif (!array_key_exists(self::SCHEME, $this->server)) {
                throw new \InvalidArgumentException(self::SCHEME." is missing from the config");
            } elseif (!array_key_exists(self::HOST, $this->server)) {
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
    private function databasePrefixValidation():void
    {
        if (empty($_ENV[self::DATABASE_PREFIX])) {
            throw new \InvalidArgumentException(self::DATABASE_PREFIX. " cannot be empty");
        }
    }

    /**
     * This function validate the database name
     */
    private function databaseNameValidation():void
    {
        if (empty($_ENV[self::DATABASE_NAME])) {
            throw new \InvalidArgumentException(self::DATABASE_NAME. " cannot be empty");
        }
    }

    /**
     * This function validate the database user
     */
    private function databaseUserValidation():void
    {
        if (empty($_ENV[self::DATABASE_USER])) {
            throw new \InvalidArgumentException(self::DATABASE_USER." cannot be empty");
        }
    }

    /**
     * This function validates the database password
     */
    private function databasePasswordValidation():void
    {
        if ($this->environment !=="dev" && !$this->environment===self::STAGING_ENVIRONMENT) {
            if (empty($_ENV[self::DATABASE_PASSWORD])) {
                throw new \InvalidArgumentException(self::DATABASE_PASSWORD. " cannot be empty");
            }
        }
    }

    /**
     * This function validate the database host
     */
    private function databaseHostValidation():void
    {
        if (empty($_ENV[self::DATABASE_HOST])) {
            throw new \InvalidArgumentException(self::DATABASE_HOST." cannot be empty");
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
        return $this->config[self::ENABLED_DATABASE];
    }

    /**
     * This function returned the name of the database the application is using
     * @return string the database name
     */
    public function databaseName(): string
    {
        return $_ENV[self::DATABASE_NAME];
    }

    /**
     * This function returns the name of the database's Dsn
     * @return string return the database dsn
     */
    public function databaseDsn(): string
    {
        return $this->databasePrefix().":host=".$this->databaseHost().";dbname=".$this->databaseName().
            ";charset=utf8mb4";
    }

    /**
     * This function returns the password of the database
     * @return string the database password
     */
    public function databasePassword(): string
    {
        return $_ENV[self::DATABASE_PASSWORD];
    }

    /**
     * This function returns the username of the database
     * @return string
     */
    public function databaseUser():string
    {
        return $_ENV[self::DATABASE_USER];
    }

    /**
     * This return the name of the database's prefix
     * @return string database prefix
     */
    public function databasePrefix(): string
    {
        return $_ENV[self::DATABASE_PREFIX];
    }

    /**
     * This function return the name of the database's host
     * @return string the database's host
     */
    public function databaseHost(): string
    {
        return$_ENV[self::DATABASE_HOST];
    }

    /**
     * This function returns the base url of the application
     * @return string get base url
     */
    public function getBaseUrl(): string
    {
        $path = $this->scheme()."://".$this->serverHost();
        return empty($this->projectDirectory()) ? $path : $path."/".$this->projectDirectory()."/";
    }

    /**
     * @inheritDoc
     */
    public function isEnvironmentDevelopment(): bool
    {
        return $this->environment() == self::DEV_ENVIRONMENT;
    }

    /**
     * @inheritDoc
     */
    public function isEnvironmentStaging(): bool
    {
        return $this->environment() === self::STAGING_ENVIRONMENT;
    }

    /**
     * @inheritDoc
     */
    public function isEnvironmentProduction(): bool
    {
        return $this->environment() === self::PRODUCTION_ENVIRONMENT;
    }

}
