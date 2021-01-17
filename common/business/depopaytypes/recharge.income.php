<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */
 
namespace common\business\depopaytypes;

use yii;

use common\models\DepositTradeModel;
use common\models\DepositRecordModel;
use common\models\DepositRechargeModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id recharge.income.php 2018.7.22 $
 * @author mosir
 */
 
class RechargeIncome extends IncomeDepopay
{
	// 针对交易记录的交易类型，值有：购物：SHOPPING； 理财：FINANCE；缴费：PUC_CHARGE； 还款：CCR；转账：TRANSFER ...
	var $_tradeCat	= 'RECHARGE'; 
	
	// 针对财务明细的资金用途，值有：在线支付：PAY；充值：RECHARGE；提现：WITHDRAW; 服务费：SERVICE；转账：TRANSFER
    var $_tradeType = 'RECHARGE';
	
	// 支付类型，值有：即时到帐：INSTANT；担保交易：SHIELD；货到付款：COD
	var $_payType   = 'INSTANT';
	
	public function submit($data = array())
	{
        extract($data);
		
        // 处理交易基本信息
        $base_info = parent::_handle_trade_info($trade_info, $post);
		if (!$base_info) {
            return false;
        }
		
		//$tradeNo = $extra_info['tradeNo'];
		
		// 插入充值记录
		if(!$this->_insert_recharge_info($trade_info, $extra_info, $post)) {
			$this->setErrors('50005');
			return false;
		}
					
		return true;
	}
	
	/* 插入交易记录，充值记录 */
	private function _insert_recharge_info($trade_info, $extra_info, $post)
	{
		// 如果添加有记录，则不用再添加了
		if(!DepositTradeModel::find()->where(['tradeNo' => $extra_info['tradeNo']])->exists())
		{
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
				'payment_code'	=> $post->payment_code,
				'tradeCat'		=> $this->_tradeCat,
				'payType'		=> $this->_payType,
				'flow'     		=> $this->_flow_name,
				'fundchannel'   => Language::get($post->payment_code),
				'title'			=> Language::get('recharge'),
				'buyer_remark'	=> $post->remark ? $post->remark : '',
				'add_time'		=> Timezone::gmtime()
			);
			
			$model = new DepositTradeModel();
			foreach($data_trade as $key => $val) {
				$model->$key = $val;
			}
		
			if($model->save(false) == true)
			{
				$query = new DepositRechargeModel();
				$query->orderId = $bizOrderId;
				$query->userid = $trade_info['userid'];
				$query->is_online = 1;
	
				$result = $query->save();
			}
		}
		return $result;
	}
	
	/* 线上充值（支付充值，资金退回）响应通知 */
	public function respond_notify($orderInfo = array(), $notify_result, $outTradeNo = '')
	{
		$time = Timezone::gmtime();
				
		/** 
		 * 如果是支付订单的预充值通知（有正常的交易记录，需要创建新的充值记录） 
		 * 数据来源有两种，一种是：从正常的交易记录中读取，二种是：从交易日志中读取（支付变更的情况）
		 */
		if(in_array($orderInfo['bizIdentity'], array(Def::TRADE_ORDER, Def::TRADE_BUYAPP)))
		{
			$tradeNo = $orderInfo['payTradeNo'];
				
			$trade_info = array('userid' => $orderInfo['buyer_id'], 'party_id' => 0, 'amount' => $orderInfo['amount']);
			$extra_info = array('tradeNo' => $tradeNo);
			$post		= (object)array('payment_code' => $orderInfo['payment_code']);
				
			if($this->_insert_recharge_info($trade_info, $extra_info, $post)) {
				$orderInfo['tradeList'] = DepositTradeModel::find()->where(['tradeNo' => $tradeNo])->indexBy('trade_id')->asArray()->all();
			}
		}
		// 如果是单纯充值的订单通知（有正常的交易记录，无需创建新的充值记录，只需改变交易状态）
		if(in_array($orderInfo['bizIdentity'], array(Def::TRADE_RECHARGE))) {
			
			// 数据兼容处理，不要加到判断中
			!isset($orderInfo['tradeList']) && $orderInfo['tradeList'] = array();
		}
	
		foreach($orderInfo['tradeList'] as $tradeInfo)
		{
			// 修改交易状态
			DepositTradeModel::updateAll(['status' => 'SUCCESS', 'pay_time' => $time, 'end_time' => $time, 'outTradeNo' => $outTradeNo], ['tradeNo' => $tradeInfo['tradeNo']]);
			
			// 插入充值者收入记录，并更新账户余额表
			$model = new DepositRecordModel();
			$model->tradeNo = $tradeInfo['tradeNo'];
			$model->userid = $tradeInfo['buyer_id'];
			$model->amount =  $tradeInfo['amount'];
			$model->balance = parent::_update_deposit_money($tradeInfo['buyer_id'], $tradeInfo['amount']);
			$model->tradeType = $this->_tradeType;
			$model->tradeTypeName = Language::get(strtoupper($this->_tradeType));
			$model->flow = $this->_flow_name;
			
			if($model->save() === false) {
				return false;
			}
		}
		
		// 如果是从交易日志中取交易数据，删掉交易日志文件
		if(isset($orderInfo['tradelogfile'])) @unlink($orderInfo['tradelogfile']);
		
		return true;
	}
}
