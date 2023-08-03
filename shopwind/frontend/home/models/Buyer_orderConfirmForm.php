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
use common\models\OrderGoodsModel;
use common\models\DepositTradeModel;
use common\models\RefundModel;
use common\models\GoodsStatisticsModel;
use common\models\OrderLogModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id Buyer_orderConfirmForm.php 2018.9.19 $
 * @author mosir
 */
class Buyer_orderConfirmForm extends Model
{
	public $errors = null;

	public function formData($post = null)
	{
		// 订单信息 
		if (!$post->order_id || !($orderInfo = OrderModel::find()->where(['order_id' => $post->order_id, 'buyer_id' => Yii::$app->user->id])->andWhere(['in', 'status', [Def::ORDER_SHIPPED]])->indexBy('order_id')->asArray()->one())) {
			$this->errors = Language::get('no_such_order');
			return false;
		}

		// 交易信息 
		if (!($tradeInfo = DepositTradeModel::find()->where(['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $orderInfo['order_sn'], 'buyer_id' => Yii::$app->user->id])->asArray()->one())) {
			$this->errors = Language::get('no_such_order');
			return false;
		}
		return array($orderInfo, $tradeInfo);
	}

	/**
	 * 买家确认收货(货到付款订单不进入该逻辑)
	 * @param object $post
	 */
	public function submit($post = null, $orderInfo = [], $tradeInfo = [], $sendNotify = true)
	{
		// 有退款功能： 如果该订单有退款商品（退款关闭的除外），则不允许确认收货
		$refund = RefundModel::find()->select('refund_id,status')->where(['tradeNo' => $tradeInfo['tradeNo']])->asArray()->one();
		if ($refund && !in_array($refund['status'], array('CLOSED', 'SUCCESS'))) {
			$this->errors = Language::get('order_not_confirm_for_refund');
			return false;
		}

		// 如果订单中的商品为空，则认为订单信息不完整，不执行
		$ordergoods = OrderGoodsModel::find()->where(['order_id' => $orderInfo['order_id']])->asArray()->all();
		if (empty($ordergoods)) {
			$this->errors = Language::get('order_not_confirm_for_refund');
			return false;
		}

		// 更新订单状态 
		$model = OrderModel::findOne($orderInfo['order_id']);
		$model->status = Def::ORDER_FINISHED;
		$model->receive_time = Timezone::gmtime();
		$model->finished_time = Timezone::gmtime();
		if (!$model->save()) {
			$this->errors = $model->errors;
			return false;
		}

		// 记录订单操作日志 
		$query = new OrderLogModel();
		$query->order_id = $orderInfo['order_id'];
		$query->operator = addslashes(Yii::$app->user->identity->username);
		$query->order_status = Def::getOrderStatus($orderInfo['status']);
		$query->changed_status = Def::getOrderStatus(Def::ORDER_FINISHED);
		$query->remark = Language::get('buyer_confirm');
		$query->log_time = Timezone::gmtime();
		$query->save();

		// 转到对应的业务实例，不同的业务实例用不同的文件处理，如购物，卖出商品，充值，提现等，每个业务实例又继承支出或者收入 
		$depopay_type = \common\library\Business::getInstance('depopay')->build('sellgoods', $post);

		$result = $depopay_type->submit(array(
			'trade_info' => array('userid' => $orderInfo['seller_id'], 'party_id' => $orderInfo['buyer_id'], 'amount' => $orderInfo['order_amount']),
			'extra_info' => $orderInfo + array('tradeNo' => $tradeInfo['tradeNo'])
		));

		if (!$result) {
			$this->errors = $depopay_type->errors;
			return false;
		}

		// 更新累计销售件数
		foreach ($ordergoods as $key => $goods) {
			GoodsStatisticsModel::updateAllCounters(['sales' => $goods['quantity']], ['goods_id' => $goods['goods_id']]);
		}

		// 短信和邮件提醒： 买家已确认通知卖家
		if ($sendNotify === true) {
			Basewind::sendMailMsgNotify(
				$orderInfo,
				array(
					'key' => 'toseller_finish_notify'
				),
				array(
					'key' => 'toseller_finish_notify'
				)
			);
		}
		return true;
	}
}
