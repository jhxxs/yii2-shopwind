<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\library;

use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use common\models\OrderModel;
use common\models\OrderGoodsModel;
use common\models\OrderLogModel;
use common\models\RefundModel;
use common\models\DepositTradeModel;
use common\models\DistributeModel;
use common\models\IntegralModel;
use common\models\GoodsStatisticsModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id Taskqueue.php 2018.3.2 $
 * @author mosir
 */
 
class Taskqueue
{
	public static function run()
	{
		self::autoconfirm();
		self::autoclosed();
	}
	
	/* 到期未付款，自动关闭订单 */
	public static function autoclosed()
	{
		$today = Timezone::gmtime();
		
        // 默认2天
        $interval = 2 * 24 * 3600;
		
		// 每次仅处理2条记录，注意：处理太多会影响性能
		$list = OrderModel::find()->where("add_time + {$interval} < {$today}")->andWhere(['in', 'status', [Def::ORDER_SUBMITTED, Def::ORDER_PENDING]])->indexBy('order_id')->orderBy(['order_id' => SORT_ASC])->limit(2)->asArray()->all();
		
		foreach($list as $orderInfo)
		{
			// 修改订单状态
			OrderModel::updateAll(['status' => Def::ORDER_CANCELED], ['order_id' => $orderInfo['order_id']]);
				
			// 修改交易状态
			DepositTradeModel::updateAll(['status' => 'CLOSED', 'end_time' => Timezone::gmtime()], ['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $orderInfo['order_sn'], 'buyer_id' => $orderInfo['buyer_id']]);
				
			// 订单取消后，归还买家之前被预扣积分 
			IntegralModel::returnIntegral($orderInfo);
				
  			// 加回商品库存
			OrderModel::changeStock('+', $orderInfo['order_id']);
				
			// 记录订单操作日志
			$model = new OrderLogModel();
			$model->order_id = $orderInfo['order_id'];
			$model->operator = Language::get('system');
			$model->order_status = Def::getOrderStatus($orderInfo['status']);
			$model->changed_status = Def::getOrderStatus(Def::ORDER_CANCELED);
			$model->remark = '';
			$model->log_time = Timezone::gmtime();
			$model->save();
		}
	}
	
	/* 自动确认收货 */
	public static function autoconfirm()
	{
		$today = Timezone::gmtime();
		
        // 默认15天
        $interval = 15 * 24 * 3600;
		
		// 每次仅处理2条记录，注意：处理太多会影响性能
		$list = OrderModel::find()->where("ship_time + {$interval} < {$today}")->andWhere(['status' => Def::ORDER_SHIPPED])->indexBy('order_id')->orderBy(['order_id' => SORT_ASC])->limit(2)->asArray()->all();
		
		foreach($list as $orderInfo)
		{
			// 交易信息 
			if(!($tradeInfo = DepositTradeModel::find()->where(['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $orderInfo['order_sn']])->asArray()->one())) {
				continue;
			}
			
			// 有退款功能： 如果该订单有退款商品（退款关闭的除外），则不允许确认收货
			$refund = RefundModel::find()->select('refund_id,status')->where(['tradeNo' => $tradeInfo['tradeNo']])->asArray()->one();
			if($refund && !in_array($refund['status'], array('CLOSED', 'SUCCESS'))) {
				continue;
			}
				
			// 如果订单中的商品为空，则认为订单信息不完整，不执行 
			$ordergoods = OrderGoodsModel::find()->where(['order_id' => $orderInfo['order_id']])->asArray()->all();
			if(empty($ordergoods)) {
				continue;
			}
	
			// 更新订单状态 
			$model = OrderModel::findOne($orderInfo['order_id']);
			$model->status = Def::ORDER_FINISHED;
			$model->finished_time = Timezone::gmtime();
			if(!$model->save()) {
				continue;
			}
				
			// 转到对应的业务实例，不同的业务实例用不同的文件处理，如购物，卖出商品，充值，提现等，每个业务实例又继承支出或者收入 
			$depopay_type    = \common\library\Business::getInstance('depopay')->build(['flow' => 'income', 'type' => 'sellgoods']);
				
			$result = $depopay_type->submit(array(
				'trade_info' => array('userid' => $orderInfo['seller_id'], 'party_id' => $orderInfo['buyer_id'], 'amount' => $orderInfo['order_amount']),
				'extra_info' => $orderInfo + array('tradeNo' => $tradeInfo['tradeNo']),
				'post'		 => array()
			));
				
			if($result !== true) {
				continue;
			}
			
			// 买家确认收货后，即交易完成，处理订单商品三级返佣 
			DistributeModel::distributeInvite($orderInfo);
				
			// 买家确认收货后，即交易完成，将订单积分表中的积分进行派发 
			IntegralModel::distributeIntegral($orderInfo);
			
			// 更新累计销售件数 
			foreach ($ordergoods as $key => $goods) {
				GoodsStatisticsModel::updateAllCounters(['sales' => $goods['quantity']], ['goods_id' => $goods['goods_id']]);
			}
				
			// 将确认的商品状态设置为 交易完成 
			OrderGoodsModel::updateAll(['status' => 'SUCCESS'], ['order_id' => $orderInfo['order_id']]);
				
			// 记录订单操作日志 
			$model = new OrderLogModel();
			$model->order_id = $orderInfo['order_id'];
			$model->operator = Language::get('system');
			$model->order_status = Def::getOrderStatus($orderInfo['status']);
			$model->changed_status = Def::getOrderStatus(Def::ORDER_FINISHED);
			$model->remark = '';
			$model->log_time = Timezone::gmtime();
			$model->save();
		}
	}
}