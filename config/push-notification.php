<?php

return array(

    'ios'     => array(
        'environment' =>'production',
        'certificate' => app_path() . '/certificates/aps_prod.pem',
        'passPhrase'  =>'',
        'service'     =>'apns'
    ),
    'android' => array(
        'environment' =>'production',
        'apiKey'      =>'AIzaSyBb2OkCu2QzQYqXjRhZ2YHlPrGkXPner24',
        'service'     =>'gcm'
    )

);