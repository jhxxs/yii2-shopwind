<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\models;

use Yii;
use yii\base\Model; 

use common\models\OrderModel;
use common\models\RefundModel;
use common\models\DepositTradeModel;
use common\models\OrderGoodsModel;
use common\models\UserModel;

use common\library\Timezone;
use common\library\Page;
use common\library\Def;
use frontend\api\library\Formatter;

/**
 * @Id RefundForm.php 2018.10.17 $
 * @author yxyc
 */
class RefundForm extends Model
{
	public $enter = 'buyer';
	public $errors = null;
	
	/**
	 * 获取退款记录
	 * @desc 兼容读取我的退款 和 我管理的退款
	 */
	public function formData($post = null)
	{
		$query = RefundModel::find()->alias('r')
			->select('r.refund_id,r.refund_sn,r.tradeNo,r.title,r.buyer_id,r.seller_id,r.total_fee,r.refund_total_fee,r.created,r.finished,r.status,r.intervene,dt.bizOrderId,dt.bizIdentity')
			->joinWith('depositTrade dt', false)
			->orderBy(['created' => SORT_DESC]);

		if($this->enter == 'seller') {
			$query->where(['r.seller_id' => Yii::$app->user->id]);
		} else {
			$query->where(['r.buyer_id' => Yii::$app->user->id]);
		}
		if($post->status == 'GOING') {
			$query->andWhere(['and', ['!=', 'r.status', 'SUCCESS'], ['!=', 'r.status', 'CLOSED']]);
		} elseif($post->status) {
			$query->andWhere(['r.status' => $post->status]);
		}
		if($post->refund_sn) {
			$query->andWhere(['refund_sn' => $post->refund_sn]);
		}
		if($post->bizOrderId) {
		    $query->andWhere(['bizOrderId' => $post->bizOrderId]);
		}
		if($post->tradeNo) {
		    $query->andWhere(['r.tradeNo' => $post->tradeNo]);
		}
	
		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach($list as $key => $value) {
			$list[$key]['created'] = Timezone::localDate('Y-m-d H:i:s', $value['created']);
			$list[$key]['finished'] = Timezone::localDate('Y-m-d H:i:s', $value['finished']);
			$list[$key]['buyer_name'] = UserModel::find()->select('username')->where(['userid' => $value['buyer_id']])->scalar();
			$list[$key]['seller_name'] = UserModel::find()->select('username')->where(['userid' => $value['seller_id']])->scalar();

			if(($value['bizIdentity'] == Def::TRADE_ORDER) && !empty($value['bizOrderId'])) {
				if(($order = OrderModel::find()->select('order_id,order_sn,seller_name as store_name')->where(['order_sn' => $value['bizOrderId']])->asArray()->one())) {
					if(isset($post->queryitem) && ($post->queryitem === true)) {
						$items = OrderGoodsModel::find()->select('goods_id,spec_id,goods_name,goods_image,specification,price,quantity')->where(['order_id' => $order['order_id']])->asArray()->all();
						foreach($items as $k => $v) {
							$items[$k]['goods_image'] = Formatter::path($v['goods_image'], 'goods');
						}
						$order['items'] = $items;
					}
					$list[$key] += $order;
				}
			}
		}

		return array($list, $page);
	}
}