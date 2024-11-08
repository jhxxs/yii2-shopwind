<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\controllers;

use Yii;

use common\library\Basewind;
use common\library\Language;
use common\library\Plugin;
use common\library\Page;

use frontend\api\library\Respond;

/**
 * @Id PickerController.php 2019.6.15 $
 * @author yxyc
 */

class PickerController extends \common\base\BaseApiController
{
	/**
	 * 采集商品到本地
	 * @api 接口访问地址: https://www.xxx.com/api/picker/add
	 */
	public function actionAdd()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['cate_id']);

		if (!($client = Plugin::getInstance('datapicker')->build($post->code, $post))) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('no_plugin'));
		}

		if (empty($post->url) || !($itemid = $client->getItemId($post->url))) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('no_data'));
		}

		// 开始采集数据并导入本地
		$model = new \frontend\home\models\GoodsForm(['store_id' => Yii::$app->user->id]);
		if (!$model->import($post, $itemid)) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('import_fail'));
		}

		return $respond->output(true, null, ['goods_id' => $model->goods_id]);
	}

	/**
	 * 获取采集平台商品列表（目前只有行云货仓有）
	 * @api 接口访问地址: https://www.xxx.com/api/picker/goods
	 */
	public function actionGoods()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size', 'categoryId']);
		if (!isset($post->code)) $post->code = '';

		$result = [];
		if ($post->code && ($client = Plugin::getInstance('datapicker')->build($post->code))) {
			if (!($result = $client->goodslist($post->categoryId, $post->page, $post->page_size))) {
				return $respond->output(Respond::PARAMS_INVALID, $client->errors);
			}
		}

		return $respond->output(true, null, $result);
	}

	/**
	 * 获取采集平台商品分类列表（目前只有行云货仓有）
	 * @api 接口访问地址: https://www.xxx.com/api/picker/goods
	 */
	public function actionCategory()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['categoryId']);
		if (!isset($post->code)) $post->code = '';

		$result = [];
		if ($post->code && ($client = Plugin::getInstance('datapicker')->build($post->code))) {
			if (!($result = $client->categorylist($post->categoryId))) {
				return $respond->output(Respond::PARAMS_INVALID, $client->errors);
			}
		}

		return $respond->output(true, null, $result);
	}

	/**
	 * 获取已启用的采集平台列表
	 * @api 接口访问地址: https://www.xxx.com/api/picker/list
	 */
	public function actionList()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		$list = [];
		if ($client = Plugin::getInstance('datapicker')->build()) {
			if ($result = $client->getList(true)) {
				foreach ($result as $value) {
					if ($value['enabled']) {
						$list[] = ['code' => $value['code'], 'name' => $value['name'], 'summary' => $value['summary']];
					}
				}
			}
		}

		return $respond->output(true, null, $list);
	}
}
