<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\promote\coupon;

use yii;
use yii\helpers\Url;

use common\library\Basewind;
use common\library\Language;

/**
 * @Id plugin.info.php 2018.8.3 $
 * @author mosir
 */
return array(
    'code' => 'coupon',
    'name' => '优惠券',
    'desc' => '商户发放优惠券，用户下单直接抵扣',
    'author' => 'SHOPWIND',
	'website' => 'https://www.shopwind.net',
    'version' => '1.0',
    'category' => 'user', // user/store
    'icon' => 'icon-youhuiquan',
    'buttons' => array(
        array(
            'label' => Language::get('setting'),
            'url' => Basewind::baseUrl() . '/seller/coupon/list',
            'dialog' => true
        )
    )
);