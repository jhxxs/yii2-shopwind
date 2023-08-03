<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\controllers;

use Yii;

use common\models\DepositTradeModel;

use common\library\Plugin;
use common\library\Def;

/**
 * @Id PaynotifyController.php 2018.12.20 $
 * @author mosir
 */

class PaynotifyController extends \common\base\BaseApiController
{
	public function actionAlipay()
	{
		return $this->notify(Yii::$app->request->post('out_trade_no', 0));
	}
	
	public function actionWxpay()
	{
		$notify = Plugin::getInstance('payment')->build('wxpay')->getNotify();
		return $this->notify($notify['out_trade_no'], json_encode($notify));
	}

	public function actionWxmppay()
	{
		$notify = Plugin::getInstance('payment')->build('wxmppay')->getNotify();
		return $this->notify($notify['out_trade_no'], json_encode($notify));
	}

	public function actionWxapppay()
	{
		$notify = Plugin::getInstance('payment')->build('wxapppay')->getNotify();
		return $this->notify($notify['out_trade_no'], json_encode($notify));
	}
	
	public function actionUnionpay()
	{
		return $this->notify(Yii::$app->request->post('out_trade_no', 0));
	}
	
	/**
	 * 当异步通知Url可以带参数时可使用此
	 */
	public function actionNotify()
	{
		return $this->notify(Yii::$app->request->post('payTradeNo', 0));
	}

	private function notify($payTradeNo, $params = null)
	{
		if(empty($payTradeNo)) {
			return false;
		}
		if(!($orderInfo = DepositTradeModel::getTradeInfoForNotify($payTradeNo))) {
			return false;
		}
		
		$payment_code = $orderInfo['payment_code'];
		
		// 货到付款的订单不许进入此通知页面
        if(in_array(strtoupper($payment_code), array('COD'))) {
			return false;
		}
		
		$payment = Plugin::getInstance('payment')->build($payment_code, $params);
		if(!($payment_info = $payment->getInfo()) || !$payment_info['enabled']) {
			return false;
        }
		
		if(($notify_result = $payment->verifyNotify($orderInfo, true)) === false) {
			return $payment->verifyResult(false);
		}

		// 到此支付通知数据已经验证完毕，记录下第三方支付平台的部分交易参数
		$payment->updateTradeInfo($payTradeNo);
		
		// 充值订单（处理充值逻辑）
		if(in_array($orderInfo['bizIdentity'], array(Def::TRADE_RECHARGE))) 
		{
			if(!$payment->handleRechargeAfterNotify($orderInfo, $notify_result)) {
				//return Message::warning($payment->errors);
				return $payment->verifyResult(false);
			}
		}	
			
		// 购物订单（处理购物逻辑）
		if(in_array($orderInfo['bizIdentity'], array(Def::TRADE_ORDER)))
		{
			if($payment->handleOrderAfterNotify($orderInfo, $notify_result) === false) {
				//return Message::warning($payment->errors);
				return $payment->verifyResult(false);
			}
		}	
		
		// 购买应用订单（处理购买应用逻辑）
		if(in_array($orderInfo['bizIdentity'], array(Def::TRADE_BUYAPP)))
		{
			if($payment->handleBuyappAfterNotify($orderInfo, $notify_result) === false) {
				//return Message::warning($payment->errors);
				return $payment->verifyResult(false);
			}
		}
		
		return $payment->verifyResult(true);
	}
}