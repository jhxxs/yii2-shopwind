<?php
$params = array_merge(
    require __DIR__ . '/../../../common/config/params.php',
    require __DIR__ . '/../../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-mob',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\mob\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'abcdefg1234567890999',
        ],
        'user' => file_exists(Yii::getAlias('@public') . '/data/install.lock') ? [
            'identityClass' => 'common\models\UserModel',
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true]
        ] : null,
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
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
