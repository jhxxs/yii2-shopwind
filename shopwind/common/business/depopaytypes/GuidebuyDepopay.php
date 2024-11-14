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
use common\models\DepositRecordModel;
use common\models\DepositSettingModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Def;

/**
 * @Id GuidebuyDepopay.php 2019.10.15 $
 * @author mosir
 */

class GuidebuyDepopay extends IncomeDepopay
{
	/**
	 * 针对财务明细的资金用途，值有：在线支付：PAY；充值：RECHARGE；提现：WITHDRAW；服务费：SERVICE；转账：TRANSFER；返现：REGIVE；扣费：CHARGE
	 */
    public $_tradeType = 'TRANSFER';
	
	public function submit($data = array())
	{
		// NOT TO...
	}

	/**
	 * 订单完成后给团长分佣
	 */
	public function distribute($order = array())
	{
		// 佣金
		$money = round($order['order_amount'] * DepositSettingModel::getDepositSetting($order['guider_id'], 'guider_rate'), 2);

		if ($money <= 0 || ($order['guider_id'] <= 0)) {
			return false;
		}
		
		$model = new DepositTradeModel();
		$model->tradeNo = $model->genTradeNo();
		$model->bizOrderId = $model->genTradeNo(12, 'bizOrderId');
		$model->bizIdentity = Def::TRADE_GUIDE;
		$model->buyer_id = $order['guider_id'];
		$model->seller_id = 0;
		$model->amount = $money;
		$model->status = 'SUCCESS';
		$model->payment_code = 'deposit';
		$model->payType = $this->_payType;
		$model->flow = $this->_flow;
		$model->title = Language::get('guiderprofit');
		$model->add_time = Timezone::gmtime();
		$model->pay_time = Timezone::gmtime();
		$model->end_time = Timezone::gmtime();
		
		if($model->save())
		{
			$query = new DepositRecordModel();
			$query->tradeNo = $model->tradeNo;
			$query->userid = $model->buyer_id;
			$query->amount = $model->amount;
			$query->balance = DepositAccountModel::updateDepositMoney($model->buyer_id, $model->amount);
			$query->tradeType = 'REGIVE'; // 返现
			$query->flow = $model->flow;
			$query->name = $model->title;
			$query->remark = $order['order_sn'];
			if($query->save()) {
				return true;
			}
		}
		
		return false;
	}
}