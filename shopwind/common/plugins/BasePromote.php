<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins;

use yii;
use yii\helpers\Url;

use common\library\Language;

/**
 * @Id BasePromote.php 2018.6.4 $
 * @author mosir
 */

class BasePromote extends BasePlugin
{
	/**
	 * 营销插件系列
	 * @var string $instance
	 */
	protected $instance = 'promote';

	public function getRoute()
	{
		return [
			'index' => 1, // 排序
			'text'	=> Language::get('plugin_promote'),
			'url'	=> Url::toRoute('promote/index'),
			'priv'  => ['key' => 'promote|all', 'depends' => 'appmarket|all', 'label' => Language::get('plugin_promote')]
		];
	}
}
