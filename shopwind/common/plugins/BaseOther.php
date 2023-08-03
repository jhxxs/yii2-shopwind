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
 * @Id BaseOther.php 2023.3.31 $
 * @author mosir
 */

class BaseOther extends BasePlugin
{
	/**
	 * 其他插件系列
	 * @var string $instance
	 */
	protected $instance = 'other';

	public function getRoute()
	{
		return [
			'index' => 255, // 排序
			'text'  => Language::get('plugin_other'),
			'url'   => Url::toRoute(['plugin/index', 'instance' => 'other']),
			'priv'  => ['key' => 'plugin|other|all', 'label' => Language::get('plugin_other')]
		];
	}
}
