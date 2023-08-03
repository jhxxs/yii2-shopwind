<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'abcdefg1234567890',
        ],
        'user' => file_exists(Yii::getAlias('@public') . '/data/install.lock') ? [
            'identityClass' => 'common\models\UserModel',
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'enableAutoLogin' => true,
            'loginUrl' => ['user/login']
        ] : null,
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'errorHandler' => [
            'errorAction' => 'default/error'
        ],
        'urlManager' => [
            'rules' => [
                'plugin/<instance:\w+>/<code:\w+>/<action:(install|uninstall|config)>' => 'plugin/<action>',
                'plugin/<instance:\w+>/<code:\w+>/<view:\w+>' => 'plugin/reflex',
                'plugin/<instance:\w+>/index' => 'plugin/index',
            ],
            'suffix' => '.html',
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
    'params' => $params,
];
