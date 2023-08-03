<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\datapicker\xyb2b;

/**
 * @Id plugin.info.php 2022.1.30 $
 * @author mosir
 */

return array(
    'code' => 'xyb2b',
    'name' => '行云货仓',
    'desc' => '行云货仓（https://open.xyb2b.com）为跨境电商合作伙伴提供各类业务数据与服务，<a target="_blank" href="https://open.xyb2b.com/control-platform/app-manage">进入官网申请秘钥</a>',
    'remark' => '创建应用时务必选择应用类型为：行云货仓API',
    'summary' => '跨境电商平台（https://www.xyb2b.com）货仓，支持导入商品标题，主图，规格，库存，描述，价格字段，支持最多两种规格。',
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