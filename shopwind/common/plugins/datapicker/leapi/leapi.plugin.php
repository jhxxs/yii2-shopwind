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

use yii;

use common\plugins\BaseDatapicker;

/**
 * @Id leapi.plugin.php 2018.9.5 $
 * @author mosir
 */

class Leapi extends BaseDatapicker
{
	/**
	 * 插件实例
	 * @var string $code
	 */
	public $code = 'leapi';

	/**
	 * SDK实例
	 * @var object $client
	 */
	private $client = null;

	/**
	 * 获取商品详情
	 */
	public function detail($id)
	{
		$client = $this->getClient();
		if (!($data = $client->detail($id))) {
			$this->errors = $client->errors;
			return false;
		}

		return $data;
	}

	/**
	 * 从链接提取商品ID
	 * 京东：https://item.jd.com/2349418.html
	 * 阿里巴巴：https://detail.1688.com/offer/1251853611.html
	 * 淘宝天猫：https://detail.tmall.com/item.htm?id=563422855497
	 * 拼多多：http://mobile.yangkeduo.com/goods.html?goods_id=5914165983
	 */
	public function getItemId($url)
	{
		return $this->getClient()->getItemId($url);
	}

	/**
	 * 获取SDK实例
	 */
	public function getClient()
	{
		if ($this->client === null) {
			$base_file = Yii::getAlias('@common') . '/plugins/' . $this->instance . '/' . $this->code . '/SDK.php';
			include_once($base_file);

			$webid = $this->getWebId($this->params->url);
			$class_name = sprintf('common\plugins\%s\%s\%s', $this->instance, $this->code, $webid);
			$this->client = new $class_name($this->config);
		}

		return $this->client;
	}
}
