<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\controllers\my;

use Yii;

use common\models\CouponModel;
use common\models\CouponsnModel;
use common\models\StoreModel;

use common\library\Basewind;
use common\library\Timezone;
use common\library\Page;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id CouponController.php 2019.11.20 $
 * @author yxyc
 */

class CouponController extends \common\base\BaseApiController
{
	/** 
	 * 获取我的优惠券列表
	 * @api 接口访问地址: https://www.xxx.com/api/my/coupon/list
	 */
	public function actionList()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);

		$query = CouponsnModel::find()->alias('cs')->select('cs.coupon_sn,cs.remain_times,c.id,c.name,c.money,c.start_time,c.end_time,c.amount,c.store_id,c.available')
			->joinWith('coupon c', false)
			->where(['and', ['>', 'c.id', 0], ['userid' => Yii::$app->user->id]])
			->orderBy(['available' => SORT_DESC, 'money' => SORT_DESC, 'id' => SORT_DESC]);

		if ($post->coupon_sn) {
			$query->andWhere(['coupon_sn' => $post->coupon_sn]);
		}
		if (isset($post->available) && $post->available != '' && $post->available != null) {
			$query->andWhere(['available' => intval($post->available)]);
		}
		if ($post->keyword) {
			$query->andWhere(['like', 'name', $post->keyword]);
		}
		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach ($list as $key => $value) {
			if ($value['store_id']) {
				if ($store = StoreModel::find()->select('store_name,store_logo')->where(['store_id' => $value['store_id']])->asArray()->one()) {
					$store['store_logo'] = Formatter::path($store['store_logo'], 'store');
					$list[$key] += $store;
				}
			}

			if ($value['available'] && (($value['remain_times'] < 1) || ($value['end_time'] > 0 && $value['end_time'] < Timezone::gmtime()))) {
				$list[$key]['available'] = 0;
				CouponModel::updateAll(['available' => 0], ['id' => $value['id']]);
			}
			unset($list[$key]['remain_times']);
			$list[$key]['start_time'] = Timezone::localDate('Y-m-d H:i:s', $value['start_time']);
			$list[$key]['end_time'] = Timezone::localDate('Y-m-d H:i:s', $value['end_time']);
		}

		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];
		return $respond->output(true, null, $this->params);
	}

	/**
	 * 获取我的优惠券的数量
	 * @api 接口访问地址: https://www.xxx.com/api/my/coupon/quantity
	 */
	public function actionQuantity()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		$query = CouponsnModel::find()->where(['userid' => Yii::$app->user->id]);
		$this->params = ['quantity' => $query->count()];

		return $respond->output(true, null, $this->params);
	}
}
