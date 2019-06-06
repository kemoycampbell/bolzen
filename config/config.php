<?php

return array(
    'debug'=>true,
    'environment'=>'dev',
    'enableDatabase'=>true,
    'hashAlgorithm'=>'sha256',
    'max_files'=>2,

    ######################################
    # support xml configuration variables
    ######################################
    'useXmlConfigurationVariable'=>false,
    'xmlPath'=>'',

    ##############################################
    # If the developer decided to enable
    # use xml configuration variable,
    # can decided which of the configuration
    # variables they which to override from
    # .env by supplying the value in the array
    # keys
    # format:
    #   'KEY_FROM_ENV_TO_OVERRIDE' => 'CONFIG KEY'
    ###############################################
    "xmlDatabase"=>array(
        'DB_PREFIX'=>"",
        'DB_USER'=>"",
        'DB_PASS'=>"",
        'DB_HOST'=>"",
        'DB_NAME'=>"",
    ),

    ###################################
    # APPLICATION HOSTING ENVIRONMENT #
    ###################################
    'dev'=>array(
        'directory'=>'bolzen',
        'scheme'=>'http',
        'host'=>'localhost'

    ),
    'stage'=>array(
        'directory'=>'huston',
        'scheme'=>'https',
        'host'=>'localhost'

    ),
    'prod'=>array(
        'directory'=>'',
        'scheme'=>'',
        'host'=>''

    ),
);
