<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\connect\weixinapp;

/**
 * @Id plugin.info.php 2018.6.3 $
 * @author mosir
 */

return array(
    'code' => 'weixinapp',
    'name' => '微信登录',
    'desc' => '适用于PC端微信扫码登录/APP客户端(Android & iOS)登录，申请微信开放平台账户。<a href="https://open.weixin.qq.com" target="_blank">接口申请</a>',
    'remark' => '此处填微信开放平台创建【网站应用】类型秘钥，授权回调域填写：你的域名。如果要开启APP登录，请同时创建【移动应用】秘钥，后将秘钥填写到移动端manifest.json->App模块配置->OAuth->微信登录->appid（其中appsecret通过源码视图新增节点填写）并打包编译。',
    'author' => 'SHOPWIND',
    'website' => 'https://www.shopwind.net',
    'version' => '1.0',
    'config' => array(
        'appId' => array(
            'type' => 'text',
            'text' => 'AppId'
        ),
        'appKey' => array(
            'type' => 'text',
            'text' => 'AppSecret'
        )
    )
);
