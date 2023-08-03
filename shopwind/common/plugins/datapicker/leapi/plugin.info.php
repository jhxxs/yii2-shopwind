<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\datapicker\leapi;

/**
 * @Id plugin.info.php 2022.1.30 $
 * @author mosir
 */

return array(
    'code' => 'leapi',
    'name' => '99API',
    'desc' => '乐榜（99api）商品数据采集，支持淘宝、天猫、京东、1688（阿里巴巴）、拼多多等商品数据，<a target="_blank" href="https://www.99api.com/Login?log=5&referee=19843">进入官网申请秘钥</a>',
    'remark' => '注册账号后开通（或联系99API官方客服）所需平台的商品详情（或商品详情Plus）接口即可',
    'summary' => '支持采集【淘宝、天猫、京东、1688（阿里巴巴）、拼多多】商品标题，主图，规格，库存，描述，价格字段，所有平台支持最多两种规格，多余的规格将被过滤，各字段返回数据由于平台（99API）原因与数据源略有差异。',
    'author' => 'SHOPWIND',
	'website' => 'https://www.shopwind.net',
    'version' => '1.0',
    'config' => array(
        'apikey' => array(
            'type' => 'text',
            'text' => 'apikey'
        )
    )
);