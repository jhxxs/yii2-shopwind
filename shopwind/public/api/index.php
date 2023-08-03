<?php
/**
 * 解决H5端跨域问题
 * 如果需要配置H5端，请把下面注释去掉，然后把域名改为自己的，请务必填写域名，请不要使用通配符（*）以免引发安全问题
 */
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers:Content-Type");
header("Access-Control-Allow-Methods:POST");
header("Access-Control-Allow-Credentials: false");

error_reporting(E_ALL ^ E_NOTICE);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../../frontend/api/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../../frontend/api/config/main.php',
    require __DIR__ . '/../../frontend/api/config/main-local.php'
);

(new yii\web\Application($config))->run();
