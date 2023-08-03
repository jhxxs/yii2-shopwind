<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\other\cleanup;

use yii;
use yii\helpers\Url;
use common\library\Language;

/**
 * @Id plugin.info.php 2018.8.3 $
 * @author mosir
 */

return array(
    'code' => 'cleanup',
    'name' => '一键清除',
    'desc' => '清除安装系统时安装的测试（演示）数据，包括图片、用户、商品等',
    'author' => 'SHOPWIND',
    'website' => 'https://www.shopwind.net',
    'version' => '1.0',
    'buttons' => array(
        array(
            'label' => Language::get('manage'),
            'url' => Url::toRoute(['plugin/reflex', 'instance' => $this->instance, 'code' => 'cleanup', 'view' => 'index'])
        )
    ),
    'config' => array()
);
