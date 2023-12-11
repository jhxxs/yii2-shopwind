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
use common\models\IntegralModel;

use common\library\Timezone;

/**
 * @Id SellgoodsDepopay.php 2018.4.24 $
 * @author mosir
 */

class SellgoodsDepopay extends IncomeDepopay
{
	/**
	 * 针对财务明细的资金用途，值有：在线支付：PAY；充值：RECHARGE；提现：WITHDRAW；服务费：SERVICE；转账：TRANSFER；返现：REGIVE；扣费：CHARGE
	 */
	public $_tradeType 	= 'PAY';

	/**
	 * 支付类型，值有：即时到帐：INSTANT；担保交易：SHIELD；货到付款：COD
	 */
	public $_payType   	= 'SHIELD';

	public function submit($data = array())
	{
		extract($data);

		// 处理交易基本信息
		$base_info = $this->_handle_trade_info($trade_info, $extra_info);
		if (!$base_info || !$this->_handle_order_info($extra_info)) {
			return false;
		}

		$tradeNo = $extra_info['tradeNo'];

		// 修改交易状态为交易完成
		if (!$this->_update_trade_status($tradeNo, array('status' => 'SUCCESS', 'end_time' => Timezone::gmtime()))) {
			$this->setErrors("50022");
			return false;
		}

		// 插入卖家收入记录，并变更账户余额
		if (!$this->_insert_record_info($trade_info, $extra_info)) {
			$this->setErrors("50008");
			return false;
		}

		// 如果有交易服务费，则扣除卖家手续费
		if ($trade_rate = DepositSettingModel::getDepositSetting($trade_info['userid'], 'trade_rate')) {
			if (!parent::_sys_chargeback($tradeNo, $trade_info, $trade_rate, 'trade_fee')) {
				$this->setErrors("50009");
				return false;
			}
		}

		// 如果买家使用的是余额支付，则重置不可提现额度金额
		if ($trade_info['amount'] > 0 && $extra_info['payment_code'] == 'deposit') {
			parent::relieveUserNodrawal($tradeNo, $trade_info['party_id'], $trade_info['amount']);
		}

		// 买家确认收货后，即交易完成，处理订单商品三级返佣 
		DistributeModel::distributeInvite($extra_info);

		// 如果是社区团购订单，给团长分成
		GuideshopModel::distributeProfit($extra_info);

		// 买家确认收货后，即交易完成，将订单积分表中的积分进行派发 
		IntegralModel::distributeIntegral($extra_info);

		// 将确认的商品状态设置为 交易完成 
		OrderGoodsModel::updateAll(['status' => 'SUCCESS'], ['order_id' => $extra_info['order_id']]);

		return true;
	}
}
