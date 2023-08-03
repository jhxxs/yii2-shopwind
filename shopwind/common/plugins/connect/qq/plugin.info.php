<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\connect\qq;

/**
 * @Id plugin.info.php 2018.6.3 $
 * @author mosir
 */

return array(
    'code' => 'qq',
    'name' => '腾讯QQ登录',
    'desc' => 'QQ互联快捷登录，支持PC端、H5端登录、APP端登录。<a href="https://connect.qq.com" target="_blank">接口申请</a>',
    'remark' => '在QQ互联/应用管理/创建网站应用，网站回调域填写：域名/connect/callback;域名/h5/pages/connect/callback<br/>如果要开启APP端登录，请同时创建【移动应用】秘钥，后将秘钥填写到移动端UNIAPP源码并打包编译',
    'author' => 'SHOPWIND',
	'website' => 'https://www.shopwind.net',
    'version' => '1.0',
    'config' => array(
        'appId' => array(
            'type' => 'text',
            'text' => 'appId'
        ),
        'appKey' => array(
            'type' => 'text',
            'text' => 'appKey'
        ),
    )
);