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
 * @Id BaseEditor.php 2018.9.5 $
 * @author mosir
 */

class BaseEditor extends BasePlugin
{
	/**
	 * 富文本编辑器插件系列
	 * @var string $instance
	 */
	protected $instance = 'editor';

	public function getRoute()
	{
		return [
			'index' => 9, // 排序
			'text'  => Language::get('plugin_editor'),
			'url'   => Url::toRoute(['plugin/index', 'instance' => 'editor']),
			'priv'  => ['key' => 'plugin|editor|all', 'label' => Language::get('plugin_editor')]
		];
	}
}
