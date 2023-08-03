<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php'
);

return [
    'id' => 'app-install',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => '\install\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-install',
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'abcdefg1234567890999',
        ],
        'errorHandler' => [
            'errorAction' => 'default/error'
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,
            'rules' => [
                '<action:\w+>' => 'default/<action>'
            ]
        ],
        'view' => [
            'theme' => [
                'basePath' => '@app/views/default',
                'baseUrl' => '@web/views/default',
                'pathMap' => [
                    '@app/views' => [
                        '@app/views/default'
                    ]
                ]
            ]
        ]
    ],
    'params' => $params
];
