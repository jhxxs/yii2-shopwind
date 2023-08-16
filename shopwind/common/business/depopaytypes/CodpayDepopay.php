<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\business\depopaytypes;

use yii;

use common\models\DepositSettingModel;
use common\models\DistributeModel;
use common\models\GuideshopModel;
use common\models\OrderGoodsModel;
use common\models\OrderLogModel;
use common\models\IntegralModel;
use common\models\GoodsStatisticsModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id CodpayDepopay.php 2018.4.24 $
 * @author mosir
 */

class CodpayDepopay extends OutlayDepopay
{
	/**
	 * 针对交易记录的交易分类，值有：购物：SHOPPING； 理财：FINANCE；缴费：CHARGE； 还款：CCR；转账：TRANSFER ...
	 */
	public $_tradeCat	= 'SHOPPING';

	/**
	 * 针对财务明细的交易类型，值有：在线支付：PAY；充值：RECHARGE；提现：WITHDRAW; 服务费：SERVICE；转账：TRANSFER
	 */
	public $_tradeType 	= 'PAY';

	/**
	 * 支付类型，值有：即时到帐：INSTANT；担保交易：SHIELD；货到付款：COD
	 */
	public $_payType   	= 'COD';

	public function notify($data = [])
	{
		extract($data);

		// 处理交易基本信息
		$base_info = $this->_handle_trade_info($trade_info, $extra_info);
		if (!$base_info) {
			return false;
		}

		$tradeNo = $extra_info['tradeNo'];
		$time = Timezone::gmtime();

		// 修改交易状态为交易完成
		if (!$this->_update_trade_status($tradeNo, ['status' => 'SUCCESS', 'pay_time' => $time, 'end_time' => $time])) {
			$this->setErrors("50022");
			return false;
		}

		// 货到付款订单收货后的在线支付： 如果是余额支付，则处理买家支出记录，并变更账户余额
		if ($extra_info['payment_code'] == 'deposit') {
			if (!$this->_insert_record_info($trade_info, $extra_info)) {
				$this->setErrors('50020');
				return false;
			}
		}

		// 修改订单状态为交易完成
		if (!parent::_update_order_status($extra_info['order_id'], ['status' => Def::ORDER_FINISHED, 'pay_time' => $time, 'finished_time' => $time])) {
			$this->setErrors('50021');
			return false;
		}

		// 插入卖家收入记录，并变更账户余额
		if (!$this->insertSellerRecord($trade_info, $extra_info)) {
			$this->setErrors("50008");
			return false;
		}

		// 如果有交易服务费，则扣除卖家手续费
		if ($trade_rate = DepositSettingModel::getDepositSetting($trade_info['party_id'], 'trade_rate')) {
			if (!parent::_sys_chargeback($tradeNo, array_merge($trade_info, ['userid' => $trade_info['party_id']]), $trade_rate, 'trade_fee')) {
				$this->setErrors("50009");
				return false;
			}
		}

		// 如果买家使用的是余额支付，则重置不可提现额度金额
		if ($trade_info['amount'] > 0 && $extra_info['payment_code'] == 'deposit') {
			parent::relieveUserNodrawal($tradeNo, $trade_info['userid'], $trade_info['amount']);
		}

		// 买家确认收货后，即交易完成，处理订单商品三级返佣 
		DistributeModel::distributeInvite($extra_info);

		// 如果是社区团购订单，给团长分成
		GuideshopModel::distributeProfit($extra_info);

		// 买家确认收货后，即交易完成，将订单积分表中的积分进行派发 
		IntegralModel::distributeIntegral($extra_info);

		// 将确认的商品状态设置为 交易完成
		OrderGoodsModel::updateAll(['status' => 'SUCCESS'], ['order_id' => $extra_info['order_id']]);

		// 订单操作日志
		OrderLogModel::change($extra_info['order_id'], Language::get('order_ispayed'));
		OrderLogModel::create($extra_info['order_id'], Def::ORDER_FINISHED);

		// 更新累计销售件数
		foreach (OrderGoodsModel::find()->select('goods_id,quantity')->where(['order_id' => $extra_info['order_id']])->all() as $query) {
			GoodsStatisticsModel::updateStatistics($query->goods_id, 'sales', $query->quantity);
		}

		return true;
	}

	/**
	 * 插入收入记录，并变更账户余额
	 */
	public function insertSellerRecord($trade_info, $extra_info)
	{
		// 加此判断，目的为允许提交订单金额为零的处理
		if ($trade_info['amount'] == 0) {
			return true;
		}

		$data_record = array(
			'tradeNo'		=>	$extra_info['tradeNo'],
			'userid'		=>	$trade_info['party_id'], //卖家
			'amount'		=> 	$trade_info['amount'],
			'balance'		=>	parent::_update_deposit_money($trade_info['party_id'],  $trade_info['amount'], 'add'), // 同时更新余额
			'tradeType'		=>  $this->_tradeType,
			'flow'			=>	'income',
			'name'			=>  $trade_info['name'] ? $trade_info['name'] : Language::get($this->_tradeType),
		);

		// 插入收入记录
		return parent::_insert_deposit_record($data_record, false);
	}
}
