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

use common\models\OrderModel;
use common\models\DepositTradeModel;
use common\models\DepositAccountModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Def;
use common\library\Plugin;

use frontend\api\library\Respond;

/**
 * @Id CashierController.php 2018.11.23 $
 * @author yxyc
 */

class CashierController extends \common\base\BaseApiController
{
	/**
	 * 提交收银台交易订单支付请求
	 * @api 接口访问地址: https://www.xxx.com/api/cashier/pay
	 */
	public function actionPay()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		$post->payment_code = $respond->getRealPaymentcode($post);

		if (empty($post->orderId)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('orderId_empty'));
		}
		$post->orderId = implode(',', (array)$post->orderId);

		if (!isset($post->payment_code) && empty($post->payment_code)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('pls_select_paymethod'));
		}

		// 获取交易数据
		list($errorMsg, $orderInfo) = DepositTradeModel::checkAndGetTradeInfo($post->orderId, Yii::$app->user->id);
		if ($errorMsg !== false) {
			return $respond->output(Respond::PARAMS_INVALID, $errorMsg);
		}

		// 如果是余额支付，且支付金额大于零，验证支付密码
		if (in_array(strtolower($post->payment_code), ['deposit']) && $orderInfo['amount'] > 0) {
			if (!DepositAccountModel::checkAccountPassword($post->password, Yii::$app->user->id)) {
				return $respond->output(Respond::PARAMS_INVALID, Language::get('password_error'));
			}
		}

		$payment = Plugin::getInstance('payment')->build($post->payment_code, $post);
		list($all_payments, $cod_payments, $errorMsg) = $payment->getAvailablePayments($orderInfo, true, true);
		if ($errorMsg !== false) {
			return $respond->output(Respond::PARAMS_INVALID, $errorMsg);
		}

		// 检查用户所使用的付款方式是否在允许的范围内
		if (!in_array($post->payment_code, $payment->getKeysOfPayments($all_payments))) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('payment_not_available'));
		}

		// 买家选择的支付方式更新到交易表
		if (DepositTradeModel::updateTradePayment($orderInfo, $post->payment_code) === false) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('payment_save_trade_fail'));
		}

		$payment_info = $all_payments[$post->payment_code];
		if (in_array($orderInfo['bizIdentity'], array(Def::TRADE_ORDER))) {
			$isCod = strtoupper($post->payment_code) == 'COD';
			OrderModel::updateOrderPayment($orderInfo, $isCod ? $cod_payments : $payment_info, $isCod);
		}

		// 生成支付参数
		$payform = $payment->pay($orderInfo);
		if ($payform === false) {
			return $respond->output(Respond::PARAMS_INVALID, $payment->errors);
		}

		return $respond->output(true, null, $payform);
	}

	/**
	 * 微信获取CODE后跳回的地址（适用于微信公众号支付）
	 * @api 接口访问地址: https://www.xxx.com/api/cashier/wxpay
	 */
	public function actionWxpay()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		if (!$post->payTradeNo || !($orderInfo = DepositTradeModel::getTradeInfoForNotify($post->payTradeNo))) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_info_empty'));
		}

		if (!in_array($orderInfo['payment_code'], array('wxpay'))) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('payment_code_invalid'));
		}

		$payment = Plugin::getInstance('payment')->build($orderInfo['payment_code'], $post);
		if ($post->code && ($jsApiParameters = $payment->getParameters($orderInfo, $post->code))) {
			$this->params['jsApiParameters'] = $jsApiParameters;
		}

		$this->params['orderInfo'] = array(
			'title' => $orderInfo['title'],
			'payee' => Yii::$app->params['site_name'],
			'bizIdentity' => $orderInfo['bizIdentity'],
			'payTradeNo' => $post->payTradeNo,
			'amount' => $orderInfo['amount']
		);
		return $respond->output(true, null, $this->params);
	}

	/**
	 * 获取收银台交易订单数据集合
	 * @api 接口访问地址: https://www.xxx.com/api/cashier/build
	 */
	public function actionBuild()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		if (empty($post->bizOrderId)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('bizOrderId_empty'));
		}

		// 订单类型
		if (!in_array($post->bizIdentity, [Def::TRADE_ORDER, Def::TRADE_RECHARGE, Def::TRADE_DRAW, Def::TRADE_BUYAPP])) {
			$post->bizIdentity = Def::TRADE_ORDER;
		}

		// 普通购物订单
		$model = new \frontend\home\models\CashierTradeOrderForm();
		if (in_array($post->bizIdentity, [Def::TRADE_ORDER])) {
			$orderId = $model->getOrderId($post);
		}

		// 购买营销工具订单
		$model = new \frontend\home\models\CashierTradeBuyappForm();
		if (in_array($post->bizIdentity, [Def::TRADE_BUYAPP])) {
			$orderId = $model->getOrderId($post);
		}

		if (!$orderId || $model->errors) {
			return $respond->output(Respond::PARAMS_INVALID, $model->errors);
		}

		// 有可能是多少个交易号
		return $this->paybuild($respond, implode(',', $orderId), $post);
	}

	/**
	 * 查询支付交易状态
	 * @api 接口访问地址: https://www.xxx.com/api/cashier/checkpay
	 */
	public function actionCheckpay()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		if ($post->tradeNo) {
			$query = DepositTradeModel::find()->select('payType,pay_time,status,tradeNo,payTradeNo,bizIdentity')->where(['buyer_id' => Yii::$app->user->id, 'tradeNo' => $post->tradeNo])->one();
		} else if ($post->payTradeNo) {
			if (!($query = DepositTradeModel::find()->select('payType,pay_time,status,tradeNo,payTradeNo,bizIdentity')->where(['buyer_id' => Yii::$app->user->id, 'payTradeNo' => $post->payTradeNo])->orderBy(['trade_id' => SORT_DESC])->one())) {

				// 由于支付变更，通过支付交易号找不到对应的交易记录后，插入的资金退回记录
				$query = DepositTradeModel::find()->select('payType,pay_time,status,tradeNo,payTradeNo,bizIdentity')->where(['buyer_id' => Yii::$app->user->id, 'tradeNo' => $post->payTradeNo])->orderBy(['trade_id' => SORT_DESC])->one();
			}
		}

		if (!$query) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('handle_exception'));
		}
		return $respond->output(true, null, [
			'tradeNo' => $query->tradeNo,
			'payTradeNo' => $query->payTradeNo,
			'status' => $query->status,
			'ispay' => $query->pay_time ? true : false,
			'bizIdentity' => $query->bizIdentity,
			'payType' => $query->payType
		]);
	}

	private function paybuild($respond, $orderId, $post = null)
	{
		list($errorMsg, $orderInfo) = DepositTradeModel::checkAndGetTradeInfo($orderId, Yii::$app->user->id);
		if ($errorMsg !== false) {
			return $respond->output(Respond::PARAMS_INVALID, $errorMsg);
		}

		list($all_payments, $cod_payments, $errorMsg) = Plugin::getInstance('payment')->build(null, $post)->getAvailablePayments($orderInfo, true, true);
		if ($errorMsg !== false) {
			return $respond->output(Respond::PARAMS_INVALID, $errorMsg);
		}
		$this->params['payments'] = $this->removeFieldsOfPayment($all_payments);
		$this->params['orderInfo'] = $this->removeFieldsOfTrade($orderInfo);
		$this->params['orderId'] = explode(',', $orderId);

		return $respond->output(true, null, $this->params);
	}

	/**
	 * 移除接口不需要的字段
	 * 微信公众号支付，APP支付，小程序支付统一返回支付代号：wxpay，后端再通过入参terminal去区别
	 */
	private function removeFieldsOfPayment($payments)
	{
		$result = [];
		foreach ($payments as $key => $value) {
			if (in_array($key, ['wxmppay', 'wxapppay'])) $key = 'wxpay';

			$result[$key] = array_merge(
				['name' => $value['subname'] ? $value['subname'] : $value['name'], 'code' => $key, 'selected' => intval($value['selected'])],
				$value['disabled'] ? ['disabled' => $value['disabled'], 'disabled_desc' => $value['disabled_desc']] : []
			);
		}

		return $result;
	}

	/**
	 * 移除接口不需要的字段
	 */
	private function removeFieldsOfTrade($orderInfo)
	{
		unset($orderInfo['orderList']);
		foreach ($orderInfo['tradeList'] as $key => $value) {
			foreach ($value as $k => $v) {
				if (!in_array($k, ['trade_id', 'tradeNo', 'bizOrderId', 'bizIdentity', 'seller_id', 'amount'])) {
					unset($orderInfo['tradeList'][$key][$k]);
				}
			}
			$orderInfo['tradeList'][$key]['store_name'] = $value['seller'];
			$orderInfo['tradeList'][$key]['goods_name'] = $value['name'];
		}

		return $orderInfo;
	}
}
