<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identificadores RENIEC SIIC01
    |--------------------------------------------------------------------------
    |
    | Estos son la clave pubica y clave privada proporcionados por la RENIEC
    | para la utilizacon del Refirma Invoker y asi llamar al RefirmaPDF
    | y poder firmar los documentos PDF. La firma se realiza de forma local
    | descargando el documento pra posteriormente subir el documento firmado
    | al servidor de la UGEL01.
    |
    */

    'keys' => [
        'public' => 'mz03iSUkaB_PJUaj9TfS4PxJwO4',
        'secrect' => 'Dmj1dyTOepU7L3bCP8NN',
    ],

    'firma' => [
        'masiva' => [
            'pdf'=>'lot-p',

        ],
        'simple' => [
            'pdf'=>'simple-p'
        ]
    ],

    'app'=>[
        'tipo'=>'pcx'
    ],
    'type'=>'W',

    'protocolo'=>[
        'http'=>'T',
        'https'=>'S'
    ],
    'maxFileSize'=>'90242880',
    'fontSize'=>'7',

];
