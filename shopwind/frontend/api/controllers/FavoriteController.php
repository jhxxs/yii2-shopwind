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

use common\models\CollectModel;
use common\models\GoodsModel;
use common\models\StoreModel;

use common\library\Basewind;
use common\library\Timezone;
use common\library\Page;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id FavoriteController.php 2018.11.15 $
 * @author yxyc
 */

class FavoriteController extends \common\base\BaseApiController
{
	/** 
	 * 获取我收藏的商品列表
	 * @api 接口访问地址: https://www.xxx.com/api/favorite/goods
	 */
    public function actionGoods()
	{
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);

		$query = CollectModel::find()->select('item_id as goods_id,add_time')
			->where(['userid' => Yii::$app->user->id, 'type' => 'goods'])
			->orderBy(['add_time' => SORT_DESC]);

		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach($list as $key => $value) {
			if($array = GoodsModel::find()->select('goods_name,price,default_image as goods_image,store_id,cate_id')->where(['goods_id' => $value['goods_id']])->asArray()->one()) {
				$array['goods_image'] = Formatter::path($array['goods_image'], 'goods');
				$array['store_name'] = StoreModel::find()->select('store_name')->where(['store_id' => $array['store_id']])->scalar();
			}
			$value['add_time'] = Timezone::localDate('Y-m-d H:i:s', $value['add_time']);
			$list[$key] = array_merge($value, $array ? $array : []);
		}
		
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];
		return $respond->output(true, null, $this->params);
	}

	/** 
	 * 获取我收藏的店铺列表
	 * @api 接口访问地址: https://www.xxx.com/api/favorite/store
	 */
    public function actionStore()
	{
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);

		$query = CollectModel::find()->select('item_id as store_id,add_time')
			->where(['userid' => Yii::$app->user->id, 'type' => 'store'])
			->orderBy(['add_time' => SORT_DESC]);
		
		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach($list as $key => $value) {
			if($array = StoreModel::find()->select('store_name,store_logo,credit_value')->where(['store_id' => $value['store_id']])->asArray()->one()) {
				$array['store_logo'] = Formatter::path($array['store_logo'], 'store');
				$array['credit_image'] = StoreModel::computeCredit($array['credit_value']);
			}
			$value['add_time'] = Timezone::localDate('Y-m-d H:i:s', $value['add_time']);
			$list[$key] = array_merge($value, $array ? $array : []);
		}
		
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];
		return $respond->output(true, null, $this->params);
	}
	
	/**
	 * 获取我的收藏的数量（商品或店铺）
	 * @api 接口访问地址: https://www.xxx.com/api/favorite/quantity
	 */
    public function actionQuantity()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		$query = CollectModel::find()->alias('c')->joinWith('goods g', false)->where(['userid' => Yii::$app->user->id]);
		
		// 根据类型读取店铺或商品
		if(isset($post->type) && in_array($post->type, ['goods', 'store'])) {
			$query->andWhere(['c.type' => $post->type]);
		}

		return $respond->output(true, null, ['quantity' => $query->count()]);
    }
}