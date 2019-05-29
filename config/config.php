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
    'useXmlConfigurationVariable'=>true,
    'xmlPath'=>'/home/w-ntidtutor/configvars.xml',

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
        'DB_PASS'=>"dbPass_w_ntidtutor",
        'DB_HOST'=>"dbHost_w_ntidtutor",
        'DB_NAME'=>"",
    ),

    ###################################
    # APPLICATION HOSTING ENVIRONMENT #
    ###################################
    'dev'=>array(
        'directory'=>'ntid/tutoring',
        'scheme'=>'https',
        'host'=>'www-staging.rit.edu'

    ),
    'stage'=>array(
        'directory'=>'huston',
        'scheme'=>'https',
        'host'=>'localhost'

    ),
    'prod'=>array(
        'directory'=>'ntid/tutoring',
        'scheme'=>'https',
        'host'=>'www.rit.edu'

    ),
);
