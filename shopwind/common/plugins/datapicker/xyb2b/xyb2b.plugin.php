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

use yii;

use common\library\Page;
use common\plugins\BaseDatapicker;
use common\plugins\datapicker\xyb2b\SDK;

/**
 * @Id xyb2b.plugin.php 2018.9.5 $
 * @author mosir
 */

class Xyb2b extends BaseDatapicker
{
	/**
	 * 插件实例
	 * @var string $code
	 */
	public $code = 'xyb2b';

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
	 * 获取商品列表
	 */
	public function goodslist($categoryId = 0, $page = 1, $page_size = 10, $cached = true)
	{
		$cache = Yii::$app->cache;
		$cachekey = md5((__METHOD__) . var_export(func_get_args(), true));
		$data = $cache->get($cachekey);
		if ($data === false || !$cached) {

			$client = $this->getClient();
			if (!($result = $client->goodslist(intval($categoryId), intval($page), intval($page_size)))) {
				$this->errors = $client->errors;
				return false;
			}

			$result = $result['sku_detail'];

			// 返回给前端统一字段和格式
			$data = ['list' => [], 'pagination' => []];
			foreach ($result['records'] as $value) {
				$data['list'][] = [
					'goods_id' => $value['sku_id'],
					'goods_name' => $value['sku_name'],
					'brand' => $value['brand_name'],
					'default_image' => $value['sku_img'],
					'stocks' => $value['stock_num'],
					'price' => $value['price_list'][0]['batch_sell_price'],
					'category' => [$value['category_name1'], $value['category_name2'], $value['category_name3']]
				];
			}
			$page = Page::getPage($result['total'], 10, false, intval($page));
			$data['pagination'] = Page::formatPage($page, false);

			$cache->set($cachekey, $data, 3600);
		}

		return $data;
	}

	/**
	 * 获取分类列表
	 */
	public function categorylist($categoryId = 0)
	{
		$client = $this->getClient();
		if (!($result = $client->categorylist(intval($categoryId)))) {
			$this->errors = $client->errors;
			return false;
		}

		$list = [];
		foreach ($result['category_list'] as $value) {
			$list[] = ['name' => $value['category_name'], 'id' => $value['category_id']];
		}

		return $list;
	}

	/**
	 * 从链接提取商品ID
	 * https://www.xyb2b.com/buyer/mall/goodDetail?skuId=20000037326
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
			$this->client = new SDK($this->config);
		}
		return $this->client;
	}
}
