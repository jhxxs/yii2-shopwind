<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\connect\weixin;

/**
 * @Id plugin.info.php 2018.6.3 $
 * @author mosir
 */

return array(
    'code' => 'weixin',
    'name' => '微信公众号登录',
    'desc' => '适用于微信公众号内登录，申请微信公众号账户。<a href="https://mp.weixin.qq.com" target="_blank">接口申请</a>',
    'remark' => '申请微信公众平台的服务号类型，进入公众号设置/功能设置，在业务域名、JS接口安全域名、网页授权域名项中填写：你的域名。务必到微信开放平台/管理中心/公众号/绑定该公众号',
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
