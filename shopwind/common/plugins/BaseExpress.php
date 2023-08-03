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
 * @Id BaseExpress.php 2018.9.5 $
 * @author mosir
 */

class BaseExpress extends BasePlugin
{
	/**
	 * 物流跟踪插件系列
	 * @var string $instance
	 */
	protected $instance = 'express';

	/**
	 * 获取插件支持的物流公司 
	 */
	public function getCompanys()
	{
		if (!$this->isInstall($this->code)) {
			return false;
		}

		$list = ['noneed' => '无需物流'];
		$file = Yii::getAlias('@common') . '/plugins/' . $this->instance . '/' . $this->code . '/company.inc.php';
		if (is_file($file) && ($result = include($file))) {
			return array_merge($list, $result);
		}
		return $list;
	}

	/**
	 * 根据物流代号获取物流公司名称
	 */
	public function getCompanyName($key)
	{
		if (!$key) return '';

		$list = $this->getCompanys();
		if ($list && isset($list[$key])) {
			return $list[$key];
		}
		return '';
	}

	public function getRoute()
	{
		return [
			'index' => 6, // 排序
			'text'  => Language::get('plugin_express'),
			'url'   => Url::toRoute(['plugin/index', 'instance' => 'express']),
			'priv'  => ['key' => 'plugin|express|all', 'label' => Language::get('plugin_express')]
		];
	}
}
