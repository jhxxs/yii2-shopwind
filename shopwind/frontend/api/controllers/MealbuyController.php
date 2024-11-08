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

use common\models\MealModel;
use common\models\MealGoodsModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Page;

use frontend\api\library\Respond;

/**
 * @Id MealbuyController.php 2018.12.28 $
 * @author yxyc
 */

class MealbuyController extends \common\base\BaseApiController
{
	/**
	 * 获取搭配购列表
	 * @api 接口访问地址: https://www.xxx.com/api/mealbuy/list
	 */
    public function actionList()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(false)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['store_id', 'goods_id', 'page', 'page_size']);

		$orderBy = [];
		if($post->orderby) {
			$orderBy = Basewind::trimAll(explode('|', $post->orderby));
			if(in_array($orderBy[0], array_keys($this->getOrders())) && in_array(strtolower($orderBy[1]), ['desc', 'asc'])) {
				$orderBy = [$orderBy[0] => strtolower($orderBy[1]) == 'asc' ? SORT_ASC : SORT_DESC];
			} 
		}

		$model = new \frontend\home\models\MealForm();
		list($list, $page) = $model->formData($post, isset($post->queryitem) ? (bool)$post->queryitem : true, $orderBy, true, $post->page_size, false, $post->page);
		return $respond->output(true, null, ['list' => $list, 'pagination' => Page::formatPage($page, false)]);
	}
	
	/**
	 * 获取搭配购单条信息
	 * @api 接口访问地址: https://www.xxx.com/api/mealbuy/read
	 */
    public function actionRead()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(false)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['meal_id']);

		if(!$post->meal_id || !MealModel::find()->where(['meal_id' => $post->meal_id])->exists()) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('on_sush_item'));
		}

		$model = new \frontend\home\models\MealForm(['id' => $post->meal_id]);
		list($list) = $model->formData($post, isset($post->queryitem) ? (bool)$post->queryitem : true);
		return $respond->output(true, null, $list ? current($list) : null);
	}
	
	/**
	 * 插入搭配购信息
	 * @api 接口访问地址: https://www.xxx.com/api/mealbuy/add
	 */
    public function actionAdd()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		$model = new \frontend\home\models\Seller_mealForm(['store_id' => Yii::$app->user->id]);
		if(!$model->save($post, true)) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true, ['meal_id' => $model->meal_id]);
	}
	
	/**
	 * 更新搭配购信息
	 * @api 接口访问地址: https://www.xxx.com/api/mealbuy/update
	 */
    public function actionUpdate()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['meal_id']);

		$model = new \frontend\home\models\Seller_mealForm(['store_id' => Yii::$app->user->id, 'meal_id' => $post->meal_id]);
		if(!$model->save($post, true)) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true);
	}
	
	/**
	 * 删除搭配购信息
	 * @api 接口访问地址: https://www.xxx.com/api/mealbuy/delete
	 */
    public function actionDelete()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['meal_id']);

		if(!$post->meal_id || !($model = MealModel::find()->where(['meal_id' => $post->meal_id])->one())) {
			return $respond->output(Respond::RECORD_NOTEXIST, $model->errors);
		}

		if($model->delete()) {
			MealGoodsModel::deleteAll(['meal_id' => $post->meal_id]);
		}
		
		return $respond->output(true);
	}

	/**
	 * 支持的排序规则
	 */
	private function getOrders()
    {
        return array(
            'price'    		=> Language::get('price'),
            'created'      	=> Language::get('add_time'),
        );
    }
}