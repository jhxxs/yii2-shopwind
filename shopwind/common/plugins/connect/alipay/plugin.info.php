<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\connect\alipay;

/**
 * @Id plugin.info.php 2018.6.3 $
 * @author mosir
 */

return array(
    'code' => 'alipay',
    'name' => '支付宝登录',
    'desc' => '支付宝快捷登录, 支持PC端、手机端H5登录。<a href="https://open.alipay.com/develop/manage" target="_blank">接口申请</a>',
    'remark'=> '申请开放平台秘钥，创建应用时选择网页应用类型，在开发设置/接口加签方式/设置中选择 密钥模式，下载<a href="https://opendocs.alipay.com/common/02kipk" target="_blank">支付宝开放平台密钥工具</a>自行生成秘钥对。',
    'author' => 'SHOPWIND',
    'website' => 'https://www.shopwind.net',
    'version' => '1.0',
    'config' => array(
        'appId' => array(
            'type' => 'text',
            'text' => 'APPID'
        ),
        'rsaPublicKey' => array(
            'type' => 'text',
            'text' => '应用公钥'
        ),
        'rsaPrivateKey' => array(
            'type' => 'text',
            'text' => '应用私钥'
        ),
        'alipayrsaPublicKey'   => array(
            'type'  => 'text',
            'text'  => '支付宝公钥',

        ),
        'signType'  => array(
            'type'      => 'select',
            'text'      => '签名类型',
            'items'     => array(
                'RSA2'   => 'RSA2',
                //'RSA'   => 'RSA',
            ),
        ),
        /*
		'appId_wap' => array(
            'type' => 'text',
            'text' => 'APPID（H5端）'
        ),
        'rsaPublicKey_wap' => array(
            'type' => 'text',
            'text' => '应用公钥（H5端）'
        ),
		'rsaPrivateKey_wap' => array(
			'type' => 'text',
            'text' => '应用私钥（H5端）'
		),
		'alipayrsaPublicKey_wap'   => array(
			'type'  => 'text',
            'text'  => '支付宝公钥（H5端）',
            
        ),
		'signType_wap'  => array(
			'type'      => 'select',
            'text'      => '签名类型（H5端）',
            'items'     => array(
                'RSA2'   => 'RSA2',
				//'RSA'   => 'RSA',
            ),
        ),*/
    )
);
