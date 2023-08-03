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

use common\library\Basewind;
use common\library\Language;

/**
 * @Id BaseDatapicker.php 2022.1.31 $
 * @author mosir
 */

class BaseDatapicker extends BasePlugin
{
	/**
	 * 数据采集插件系列
	 * @var string $instance
	 */
	protected $instance = 'datapicker';

	public function getRoute()
	{
		return [
			'index' => 7, // 排序
			'text' => Language::get('plugin_datapicker'),
			'url'  => Url::toRoute(['plugin/index', 'instance' => 'datapicker']),
			'priv' => ['key' => 'plugin|datapicker|all', 'label' => Language::get('plugin_datapicker')]
		];
	}

	/**
	 * 从链接获取采集的网站
	 */
	public function getWebId($url)
	{
		list($host) = explode('?', $url);
		if (stripos($host, 'tmall.com') !== false || stripos($host, 'taobao.com') !== false) {
			return 'Taobao';
		}
		if (stripos($host, '1688.com') !== false) {
			return 'Alibaba';
		}
		if (stripos($host, 'jd.com') !== false) {
			return 'Jd';
		}
		if (stripos($host, 'yangkeduo.com') !== false) {
			return 'Pdd';
		}
		if (stripos($host, Basewind::baseUrl()) !== false) {
			return 'Local';
		}

		return 'SDK';
	}

	public function goodslist($categoryId = 0, $page = 1, $page_size = 10, $cached = true)
	{
		return [];
	}

	public function categorylist($categoryId = 0)
	{
		return [];
	}
}
