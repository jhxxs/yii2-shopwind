<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\controllers\seller;

use Yii;

use common\models\CouponModel;

use common\library\Basewind;
use common\library\Timezone;
use common\library\Page;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id CouponController.php 2019.1.15 $
 * @author yxyc
 */

class CouponController extends \common\base\BaseApiController
{
	/**
 	 * 获取优惠券列表
	 * @api 接口访问地址: http://api.xxx.com/seller/coupon/list
	 */
    public function actionList()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);
		
		$query = CouponModel::find()->alias('c')->select('c.id,c.name,c.money,c.amount,c.received,c.total,c.surplus,c.start_time,c.end_time,c.available,s.store_id,s.store_name,s.store_logo')
			->joinWith('store s', false)
			->where(['c.store_id' => Yii::$app->user->id])
			->orderBy(['id' => SORT_DESC]);
		
		if(isset($post->available) && $post->available != '' && $post->available != null) {
			$query->andWhere(['available' => intval($post->available)]);
		}
		if($post->keyword) {
			$query->andWhere(['like', 'name', $post->keyword]);
		}
		
		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach($list as $key => $value) {
			$list[$key]['start_time'] = Timezone::localDate('Y-m-d H:i:s', $value['start_time']);
			$list[$key]['end_time'] = Timezone::localDate('Y-m-d H:i:s', $value['end_time']);
			$list[$key]['store_logo'] = Formatter::path($value['store_logo'], 'store');
			$list[$key]['amount'] = floatval($value['amount']);
			$list[$key]['money'] = floatval($value['money']);

			if($value['end_time'] > 0 && $value['end_time'] < Timezone::gmtime()) {
				CouponModel::updateAll(['available' => 0], ['id' => $value['id']]);
				$list[$key]['available'] = 0;
			}
		}
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];
		return $respond->output(true, null, $this->params);
    }
}