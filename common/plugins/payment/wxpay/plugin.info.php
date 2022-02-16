<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\payment\wxpay;

use yii;

use common\library\Language;

/**
 * @Id plugin.info.php 2018.6.3 $
 * @author mosir
 */

return array(
    'code'      => 'wxpay',
    'name'      => Language::get('wxpay'),
    'subname'   => Language::get('wxpay'),
    'desc'      => Language::get('wxpay_desc'),
    'author'    => 'SHOPWIND',
    'website'   => 'https://www.shopwind.net',
    'version'   => '3.0',
    'currency'  => Language::get('wxpay_currency'),
    'config'    => array(
        'appId'   => array(        // 应用ID
            'text'  => Language::get('appid'),
            'desc'  => Language::get('appid_desc'),
            'type'  => 'text',
        ),
        'appSecret'   => array(        // 应用ID
            'text'  => Language::get('AppSecret'),
            'type'  => 'text',
        ),
        'mchId'     => array(        //商户号
            'text'  => Language::get('mchid'),
            'type'  => 'text',
        ),
        'mchKey'   => array(        //商户密钥
            'text'  => Language::get('mchkey'),
            'type'  => 'text',
        ),
        'serialNo' => array(         // 证书序列号
            'text'  => Language::get('serialno'),
            'desc'  => Language::get('serialno_desc'),
            'type'      => 'text',
        ),
        'clientKey'     => array(        // 商户证书文件
            'text'  => Language::get('clientkey'),
            'desc'  => Language::get('pemPath_desc'),
            'placeholder' => 'cacert/apiclient_key.pem',
            'type'  => 'text',
        ),
        'wechatKey'     => array(        // 微信平台证书文件
            'text'  => Language::get('wechatkey'),
            'desc'  => Language::get('wxpemPath_desc'),
            'placeholder' => 'cacert/wechatpay.pem',
            'type'  => 'text',
        )
    )
);