<?php

// 站点设置参数
$params = [];
$abcdefile = Yii::getAlias('@public') . '/data/setting.php';
if (file_exists($abcdefile) && ($setting = require($abcdefile))) {
    $params = is_array($setting) ? $setting : [];
}

return array_merge($params, [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
]);
