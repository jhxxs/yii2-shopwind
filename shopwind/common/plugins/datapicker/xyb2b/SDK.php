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

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;

/**
 * @Id SDK.php 2018.6.5 $
 * @author mosir
 */

class SDK
{
	/**
	 * 网关地址
	 * @var string $gateway
	 */
	public $gateway = 'http://api.bbc.xyb2b.com/open/api/route';

	/**
	 * 应用ID
	 * @var string $appId
	 */
	public $appId;

	/**
	 * 秘钥
	 * @var string $appKey
	 */
	public $appKey;

	/**
	 * 抓取错误
	 */
	public $errors;

	/**
	 * 构造函数
	 */
	public function __construct(array $config)
	{
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * 获取商品详情
	 */
	public function detail($id)
	{
		$post = [
			'version' => '2',
			'merchant_id' => $this->appId,
			'opcode' => 'get_product_detail_v2',
			'sign_type' => 'MD5',
			'biz_content' => json_encode([
				'sku_id_list' => [$id] // 必须是1个
			]),
			'business_id' => Timezone::gmtime()
		];
		$post['sign'] = $this->getSign($post);
		if (!($response = Basewind::curl($this->gateway, 'post', json_encode($post), true))) {
			$this->errors = Language::get('no_data');
			return false;
		}

		$result = json_decode($response);
		if ($result->ret_code != '200') {
			$this->errors = $result->ret_msg;
			return false;
		}

		return $this->format(json_decode($result->result_content));
	}

	/**
	 * 获取商品列表
	 */
	public function goodslist($categoryId = 0, $page = 1, $page_size = 10)
	{
		$post = [
			'version' => '2',
			'merchant_id' => $this->appId,
			'opcode' => 'get_product_list_v2',
			'sign_type' => 'MD5',
			'biz_content' => json_encode([
				'category_id' => $categoryId,
				'current' => $page,
				'size' => $page_size // 最多10
			]),
			'business_id' => Timezone::gmtime()
		];
		$post['sign'] = $this->getSign($post);

		if (!($response = Basewind::curl($this->gateway, 'post', json_encode($post), true))) {
			$this->errors = Language::get('no_data');
			return false;
		}

		$result = json_decode($response);
		if ($result->ret_code != '200') {
			$this->errors = $result->ret_msg;
			return false;
		}

		return json_decode($result->result_content, true);
	}

	/**
	 * 获取分类列表
	 */
	public function categorylist($categoryId = 0)
	{
		$post = [
			'version' => '2',
			'merchant_id' => $this->appId,
			'opcode' => 'get_product_category_v2',
			'sign_type' => 'MD5',
			'biz_content' => json_encode([
				'parent_cat_id' => $categoryId
			]),
			'business_id' => Timezone::gmtime()
		];
		$post['sign'] = $this->getSign($post);

		if (!($response = Basewind::curl($this->gateway, 'post', json_encode($post), true))) {
			$this->errors = Language::get('no_data');
			return false;
		}

		$result = json_decode($response);
		if ($result->ret_code != '200') {
			$this->errors = $result->ret_msg;
			return false;
		}

		return json_decode($result->result_content, true);
	}

	/**
	 * 由于本系统限制,规格图片必须是放第一种规格下
	 */
	public function translate($result)
	{
		$tmp = $result['spec_name_2'];
		$result['spec_name_2'] = $result['spec_name_1'];
		$result['spec_name_1'] = $tmp;

		foreach ($result['specs'] as $key => $value) {
			$tmp = $value['spec_2'];
			$result['specs'][$key]['spec_2'] = $value['spec_1'];
			$result['specs'][$key]['spec_1'] = $tmp;
		}

		return $result;
	}

	/**
	 * 从链接提取商品ID
	 * https://www.xyb2b.com/buyer/mall/goodDetail?skuId=20000037326
	 */
	public function getItemId($url)
	{
		if (!empty($url) && stripos($url, '?') >= 0) {
			list($host, $query) = explode('?', $url);
			$query = str_replace('&amp;', '&', $query);
			parse_str($query, $params);
			return $params['skuId'];
		}

		return 0;
	}

	public function getDescInfo($item)
	{
		return $item->sku_detail;
	}

	public function getGoodsImages($item)
	{
		$list = [];
		foreach ($item->sku_thumb_image as $value) {
			$list[] = ['image_url' => $value, 'thumbnail' => $value];
		}

		array_unshift($list, ['image_url' => $item->sku_img, 'thumbnail' => $item->sku_img]);
		return $list;
	}

	public function format($data)
	{
		$item = $data->list[0];
		$result = [
			'goods_name' => $item->sku_name,
			'tags'		=> implode(',', [$item->goods_pack_type_name, $item->warehouse_name, $item->origin_name]),
			'goods_images' => $this->getGoodsImages($item),
			'description' => $this->getDescInfo($item),
			'spec_qty'	=> 0,
			'brand' => $item->brand_name
		];

		// 由于本系统限制，最多支持两种规格
		$index = 1;
		foreach ($item->sku_attributes as $key => $value) {

			if ($key > 0) {
				break;
			}

			list($specname, $specvalue) = explode(':', $value);
			$result['spec_name_1'] = $specname;
			$result['spec_name_2'] = '起批量';
			$result['spec_qty']	= 2;

			foreach ($item->price_list as $k => $v) {
				$spec = [
					'sku' => $item->sku_code . '-' . ($key + 1) . '-' . ($k + 1),
					'price' => $v->batch_sell_price,
					'stock' => $item->stock_num,
					'sort_order' => $index++,
					'spec_1' => $specvalue,
					'spec_2' => $v->batch_start_num . $item->measure_spec,
					'image' =>  $item->sku_img //$item->sku_thumb_image[$k]
				];

				$result['specs'][] = $spec;
			}
		}

		//return $this->translate($result);
		return $result;
	}

	public function getSign($params = [])
	{
		unset($params['sign_type']);
		ksort($params);

		$string = '';
		foreach ($params as $key => $value) {
			if ($value) {
				//$value = $this->character($value);
				$string .= $key . "=" . $value . "&";
			}
		}

		if (!$string) {
			return false;
		}

		return md5(substr($string, 0, -1) . $this->appKey);
	}

	/*
	 * 处理特殊字符
	 */
	protected function character($value)
	{
		// 过滤反斜杠
		$value = stripslashes($value);

		return $value;
	}
}