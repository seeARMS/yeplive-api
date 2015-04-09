<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'development',
        'certificate' =>'/path/to/certificate.pem',
        'passPhrase'  =>'password',
        'service'     =>'apns'
    ),
    'YepliveAndroid' => array(
        'environment' => env('PUSH_NOTIFICATION_ENV'),
        'apiKey'      =>env('GOOGLE_SERVER_KEY'),
        'service'     =>'gcm'
    )

);