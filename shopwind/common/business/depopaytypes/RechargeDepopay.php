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

use common\models\DepositTradeModel;
use common\models\DepositSettingModel;
use common\models\DepositRecordModel;
use common\models\DepositRechargeModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id RechargeDepopay.php 2018.7.22 $
 * @author mosir
 */

class RechargeDepopay extends IncomeDepopay
{
	/**
	 * 针对财务明细的资金用途，值有：在线支付：PAY；充值：RECHARGE；提现：WITHDRAW；服务费：SERVICE；转账：TRANSFER；返现：REGIVE；扣费：CHARGE
	 */
	public $_tradeType = 'RECHARGE';

	public function submit($data = array())
	{
		extract($data);

		// 处理交易基本信息
		$base_info = parent::_handle_trade_info($trade_info, $extra_info);
		if (!$base_info) {
			return false;
		}

		//$tradeNo = $extra_info['tradeNo'];

		// 插入充值记录
		if (!$this->_insert_recharge_info($trade_info, $extra_info)) {
			$this->setErrors('50005');
			return false;
		}

		return true;
	}

	/* 插入交易记录，充值记录 */
	private function _insert_recharge_info($trade_info, $extra_info)
	{
		// 如果添加有记录，则不用再添加了
		if (!DepositTradeModel::find()->where(['tradeNo' => $extra_info['tradeNo']])->exists()) {
			$bizOrderId	= DepositTradeModel::genTradeNo(12, 'bizOrderId');

			// 增加交易记录
			$data_trade = array(
				'tradeNo'		=> $extra_info['tradeNo'],
				'payTradeNo'	=> DepositTradeModel::genPayTradeNo(),
				'bizOrderId'	=> $bizOrderId,
				'bizIdentity'	=> Def::TRADE_RECHARGE,
				'buyer_id'		=> $trade_info['userid'],
				'seller_id'		=> $trade_info['party_id'],
				'amount'		=> $trade_info['amount'],
				'status'		=> 'PENDING',
				'payment_code'	=> $this->post->payment_code,
				'payType'		=> $this->_payType,
				'flow'     		=> $this->_flow,
				'title'			=> Language::get('recharge'),
				'buyer_remark'	=> $this->post->remark ? $this->post->remark : '',
				'add_time'		=> Timezone::gmtime()
			);

			$model = new DepositTradeModel();
			foreach ($data_trade as $key => $val) {
				$model->$key = $val;
			}

			if ($model->save(false) == true) {
				$query = new DepositRechargeModel();
				$query->orderId = $bizOrderId;
				$query->userid = $trade_info['userid'];
				$query->is_online = 1;

				$result = $query->save();
			}
		}
		return $result;
	}

	/**
	 * 线上充值响应通知 
	 */
	public function notify($orderInfo = array())
	{
		$time = Timezone::gmtime();

		foreach ($orderInfo['tradeList'] as $tradeInfo) {
			// 修改交易状态
			DepositTradeModel::updateAll(['status' => 'SUCCESS', 'pay_time' => $time, 'end_time' => $time], ['tradeNo' => $tradeInfo['tradeNo']]);

			// 插入充值者收入记录，并更新账户余额表
			$model = new DepositRecordModel();
			$model->tradeNo = $tradeInfo['tradeNo'];
			$model->userid = $tradeInfo['buyer_id'];
			$model->amount =  $tradeInfo['amount'];
			$model->balance = parent::_update_deposit_money($tradeInfo['buyer_id'], $tradeInfo['amount']);
			$model->tradeType = $this->_tradeType;
			$model->flow = $this->_flow;
			$model->name = Language::get($this->_tradeType);

			if (!$model->save()) {
				$this->errors = Language::get('trade_fail');
				return false;
			}
		}

		return true;
	}

	/**
	 * 充值返利（返钱）
	 */
	public function rebate($orderInfo = array())
	{
		// 如果充值返金额比例为零，则不处理
		$rate = floatval(DepositSettingModel::getDepositSetting($orderInfo['buyer_id'], 'regive_rate'));
		if (!$rate || (round($orderInfo['amount'] * $rate, 2) <= 0)) {
			return true;
		}

		// 实际上，只存在一次循环
		foreach ($orderInfo['tradeList'] as $tradeInfo) {
			// 如果已返过，则不处理
			if (DepositTradeModel::find()->where(['bizOrderId' => $tradeInfo['tradeNo'], 'bizIdentity' => Def::TRADE_REGIVE])->exists()) {
				return true;
			}

			// 增加交易记录
			$trade_info = array(
				'tradeNo'		=> DepositTradeModel::genTradeNo(),
				'payTradeNo'	=> DepositTradeModel::genPayTradeNo(),
				'bizOrderId'	=> $tradeInfo['tradeNo'],
				'bizIdentity'	=> Def::TRADE_REGIVE,
				'buyer_id'		=> $tradeInfo['buyer_id'],
				'seller_id'		=> 0,
				'amount'		=> round($tradeInfo['amount'] * $rate, 2),
				'status'		=> 'SUCCESS',
				'payment_code'	=> 'deposit',
				'payType'		=> $this->_payType,
				'flow'     		=> $this->_flow,
				'title'			=> Language::get('recharge_give'),
				'buyer_remark'	=> '',
				'add_time'		=> Timezone::gmtime(),
				'pay_time'		=> Timezone::gmtime(),
				'end_time'		=> Timezone::gmtime()
			);

			$model = new DepositTradeModel();
			foreach ($trade_info as $key => $val) {
				$model->$key = $val;
			}

			if ($model->save(false) == true) {
				$trade_info['userid'] = $trade_info['buyer_id'];
				$trade_info['tradeType'] = 'REGIVE';
				$trade_info['name'] = $trade_info['title'];
				$extra_info['tradeNo'] = $trade_info['tradeNo'];
				return parent::_insert_record_info($trade_info, $extra_info);
			}
		}

		return false;
	}
}
