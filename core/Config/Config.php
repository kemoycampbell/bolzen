<?php
/**
 * @author Kemoy Campbell
 * Date: 12/26/18
 * Time: 5:29 PM
 */

namespace Bolzen\Core\Config;

use http\Exception\InvalidArgumentException;
use Symfony\Component\Dotenv\Dotenv;

class Config implements ConfigInterface
{
    private const ENVIRONMENT = "environment";
    private const DEBUG = "debug";
    private const ENABLED_DATABASE = "enableDatabase";
    private const DIRECTORY = "directory";
    private const SCHEME = "scheme";
    private const HOST = "host";
    private const DATABASE_NAME = "DB_NAME";
    private const DATABASE_HOST = "DB_HOST";
    private const DATABASE_USER = "DB_USER";
    private const DATABASE_PASSWORD = "DB_PASS";
    private const DATABASE_PREFIX = "DB_PREFIX";
    private const MAXIMUM_LOG = "max_files";
    private const XML_ENABLED = "useXmlConfigurationVariable";
    private const XML_PATH = "xmlPath";
    private const XML_DATABASE = "xmlDatabase";

    private $environment;
    private $config;
    private $server;
    private $databaseParametersLoaded;
    private $XML_DATABASEParameters;
    private $xml;

    public function __construct()
    {
        $this->loadConfig();
        $this->loadXmlConfigurationIfNeed();
        $this->loadDatabaseParametersIfNeed();
    }

    public function isXmlConfigurationRequired():bool
    {
        if (!isset($this->config[self::XML_ENABLED])) {
            throw new \InvalidArgumentException("The parameter ".self::XML_ENABLED." is missing from config");
        }

        $status = $this->config[self::XML_ENABLED];

        if (!is_bool($status)) {
            throw new \InvalidArgumentException("The parameter ".self::XML_ENABLED." must be a boolean");
        }

        return $status;
    }

    private function loadXmlConfigurationIfNeed():void
    {
        if (!$this->XML_DATABASEParameters && $this->isXmlConfigurationRequired()) {
            //ensure that the XML_DATABASE parameter exist
            if (!isset($this->config[self::XML_DATABASE])) {
                throw new \InvalidArgumentException("The parameter ".self::XML_DATABASE." is missing from config");
            }

            //ensure that we have the xml file path
            if (!isset($this->config[self::XML_PATH])) {
                throw new \InvalidArgumentException("The parameter ".self::XML_PATH." is missing from config");
            }

            //ensure file path exist
            if (!file_exists($this->config[self::XML_PATH])) {
                throw new \InvalidArgumentException("We were not able to located the file 
                ".$this->config[self::XML_PATH]);
            }

            //ensure that we have all database parameters(key)
            $this->XML_DATABASEParameters = $this->config[self::XML_DATABASE];

            if (!isset($this->XML_DATABASEParameters[self::DATABASE_PREFIX])) {
                throw new \InvalidArgumentException("The parameter ". self::DATABASE_PREFIX. " is missing from
                ".self::XML_DATABASE." in config");
            }

            if (!isset($this->XML_DATABASEParameters[self::DATABASE_USER])) {
                throw new \InvalidArgumentException("The parameter ". self::DATABASE_USER. " is missing from
                ".self::XML_DATABASE." in config");
            }

            if (!isset($this->XML_DATABASEParameters[self::DATABASE_PASSWORD])) {
                throw new \InvalidArgumentException("The parameter ". self::DATABASE_PASSWORD. " is missing from
                ".self::XML_DATABASE." in config");
            }

            if (!isset($this->XML_DATABASEParameters[self::DATABASE_HOST])) {
                throw new \InvalidArgumentException("The parameter ". self::DATABASE_HOST. " is missing from
                ".self::XML_DATABASE." in config");
            }

            if (!isset($this->XML_DATABASEParameters[self::DATABASE_NAME])) {
                throw new \InvalidArgumentException("The parameter ". self::DATABASE_NAME. " is missing from
                ".self::XML_DATABASE." in config");
            }

            libxml_disable_entity_loader(false); //an attempt to prevent php bug
            //https://bugs.php.net/bug.php?id=62577
            $this->xml = simplexml_load_file($this->config[self::XML_PATH]);
        }
    }

    /**
     * This function take the config xml variable and returns the value
     * @param string $configName - the xml configuration variable name on whose value to get
     * @return string - return the value associated with the xml variable
     * @throws \InvalidArgumentException if we are not able to located the xml variable
     */
    public function getXmlConfigValue(string $configName):string
    {
        $configVal = $this->xml->xpath("//configvar[name=\"" . $configName . "\"]/./value");
        if (!isset($configVal[0])) {
            throw new \InvalidArgumentException("We were not able to located $configName in the xml config file");
        }

        return $configVal[0];
    }

    private function validateXML_DATABASEParameter(string $key)
    {
        if ($this->isXmlConfigurationRequired()) {
            $key = $this->XML_DATABASEParameters[$key];
            if (!empty($key) && empty($this->getXmlConfigValue($key))) {
                throw new \InvalidArgumentException("The parameter for $key in the xml config cannot be empty");
            }
        }
    }

    private function getXML_DATABASEParameterValue(string $key):?string
    {
        if ($this->isXmlConfigurationRequired()) {
            $key = $this->XML_DATABASEParameters[$key];
            if (!empty($key)) {
                return $this->getXmlConfigValue($key);
            }
        }

        return null;
    }


    /**
     * This method loads the database parameters from the .env if needs
     */
    private function loadDatabaseParametersIfNeed():void
    {
        if (!$this->databaseParametersLoaded && $this->isDatabaseRequired()) {

            $dotEnv = new Dotenv();
            $dotEnv->load(__DIR__.'/../../config/.env');

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

    private function loadConfig():void
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
            if (!isset($this->config[$this->environment])) {
                throw new \InvalidArgumentException("The array $this->environment is missing!");
            }

            $this->server = $this->config[$this->environment];
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
        if ($this->isXmlConfigurationRequired()) {
            $this->validateXML_DATABASEParameter(self::DATABASE_PREFIX);
        } elseif (empty(getenv(self::DATABASE_PREFIX))) {
            throw new \InvalidArgumentException(self::DATABASE_PREFIX. " cannot be empty");
        }
    }

    /**
     * This function validate the database name
     */
    private function databaseNameValidation():void
    {
        if ($this->isXmlConfigurationRequired()) {
            $this->validateXML_DATABASEParameter(self::DATABASE_NAME);
        } elseif (empty(getenv(self::DATABASE_NAME))) {
            throw new \InvalidArgumentException(self::DATABASE_NAME. " cannot be empty");
        }
    }

    /**
     * This function validate the database user
     */
    private function databaseUserValidation():void
    {
        if ($this->isXmlConfigurationRequired()) {
            $this->validateXML_DATABASEParameter(self::DATABASE_USER);
        } elseif (empty(getenv(self::DATABASE_USER))) {
            throw new \InvalidArgumentException(self::DATABASE_USER." cannot be empty");
        }
    }

    /**
     * This function validates the database password
     */
    private function databasePasswordValidation():void
    {
        if ($this->environment !=="dev") {
            if ($this->isXmlConfigurationRequired()) {
                $this->validateXML_DATABASEParameter(self::DATABASE_PASSWORD);
            } elseif (empty(getenv(self::DATABASE_PASSWORD))) {
                throw new \InvalidArgumentException(self::DATABASE_PASSWORD. " cannot be empty");
            }
        }
    }

    /**
     * This function validate the database host
     */
    private function databaseHostValidation():void
    {

        if ($this->isXmlConfigurationRequired()) {
            $this->validateXML_DATABASEParameter(self::DATABASE_HOST);
        } elseif (empty(getenv(self::DATABASE_HOST))) {
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
        $key = self::DATABASE_NAME;
        $value = $this->getXML_DATABASEParameterValue($key);

        return $value!==null ? $value : getenv($key);
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
        $key = self::DATABASE_PASSWORD;
        $value = $this->getXML_DATABASEParameterValue($key);

        return $value!==null ? $value : getenv($key);
    }

    /**
     * This function returns the username of the database
     * @return string
     */
    public function databaseUser():string
    {
        $key = self::DATABASE_USER;
        $value = $this->getXML_DATABASEParameterValue($key);

        return $value!==null ? $value : getenv($key);
    }

    /**
     * This return the name of the database's prefix
     * @return string database prefix
     */
    public function databasePrefix(): string
    {
        $key = self::DATABASE_PREFIX;
        $value = $this->getXML_DATABASEParameterValue($key);

        return $value!==null ? $value : getenv($key);
    }

    /**
     * This function return the name of the database's host
     * @return string the database's host
     */
    public function databaseHost(): string
    {
        $key = self::DATABASE_HOST;
        $value = $this->getXML_DATABASEParameterValue($key);

        return $value!==null ? $value : getenv($key);
    }

    /**
     * This function returns the base url of the application
     * @return string get base url
     */
    public function getBaseUrl(): string
    {
        //https://localhost/projectDirectory
        if (empty($this->projectDirectory())) {
            return $this->scheme()."://".$this->serverHost()."/";
        }

        return $this->scheme()."://".$this->serverHost()."/".$this->projectDirectory()."/";
    }
}
