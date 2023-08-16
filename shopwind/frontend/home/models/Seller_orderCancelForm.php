<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\models;

use Yii;
use yii\base\Model; 

use common\models\OrderModel;
use common\models\DepositTradeModel;
use common\models\UserModel;
use common\models\IntegralModel;
use common\models\OrderLogModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id Seller_orderCancelForm.php 2018.9.19 $
 * @author mosir
 */
class Seller_orderCancelForm extends Model
{
	public $store_id = 0;
	public $errors = null;
	
	public function formData($post = null)
	{
		if(!$post->order_id || !($orders = OrderModel::find()
				->where(['in', 'order_id', explode(',', $post->order_id)])
				->andWhere(['seller_id' => $this->store_id])
				->andWhere(['pay_time' => 0, 'ship_time' => 0]) // 未付款，未发货（货到付款）
				->indexBy('order_id')->asArray()->all())) {

			$this->errors = Language::get('no_such_order');
			return false;
		}
		return $orders;
	}
	
	/**
	 * 卖家取消订单
	 */
	public function submit($post = null, $orders = [], $sendNotify = true)
	{
		foreach ($orders as $order_id => $orderInfo)
 		{
			// 修改订单状态
			OrderModel::updateAll(['status' => Def::ORDER_CANCELED, 'finished_time' => Timezone::gmtime()], ['order_id' => $order_id]);
				
			// 修改交易状态
			DepositTradeModel::updateAll(['status' => 'CLOSED', 'end_time' => Timezone::gmtime()], ['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $orderInfo['order_sn'], 'seller_id' => $orderInfo['seller_id']]);
				
			// 订单取消后，归还买家之前被预扣积分 
			IntegralModel::returnIntegral($orderInfo);
				
      		// 加回商品库存
			OrderModel::changeStock('+', $order_id);
				
			// 记录订单操作日志
			OrderLogModel::create($order_id, Def::ORDER_CANCELED, addslashes(Yii::$app->user->identity->username), $post->remark ? $post->remark : $post->reason);
			
			// 发送给买家订单取消通知
			if($sendNotify === true) {
				$mailer = Basewind::getMailer('tobuyer_cancel_order_notify', ['order' => $orderInfo, 'reason' => $post->reason]);
				if($mailer && ($toEmail = UserModel::find()->select('email')->where(['userid' => $orderInfo['seller_id']])->scalar())) {
					$mailer->setTo($toEmail)->send();
				}
			}
		}
		return true;
	}
}
