<?php
$params = array_merge(
    require __DIR__ . '/../../../common/config/params.php',
    require __DIR__ . '/../../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\api\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'abcdefg1234567890999',
            // config this can receive post or json data [Yii::$app->request->post()]
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => file_exists(Yii::getAlias('@public') . '/data/install.lock') ? [
            'identityClass' => 'common\models\UserModel',
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'enableAutoLogin' => false,
            'enableSession' => false
        ] : null,
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        // url route
        'urlManager' => [
            'suffix' => ''
        ],
        'view' => []
    ],
    'params' => $params
];
