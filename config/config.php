<?php

return array(
    'debug'=>true,
    'environment'=>'dev',
    'enableDatabase'=>true,
    'hashAlgorithm'=>'sha256',

    ###################################
    # APPLICATION HOSTING ENVIRONMENT #
    ###################################
    'dev'=>array(
        'directory'=>'test',
        'scheme'=>'http',
        'host'=>'localhost'

    ),
    'stage'=>array(
        'directory'=>'',
        'scheme'=>'http',
        'host'=>'localhost'

    ),
    'prod'=>array(
        'directory'=>'',
        'scheme'=>'http',
        'host'=>'localhost'

    ),
);
