<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins;

use yii;
use yii\helpers\Url;

use common\models\OrderModel;
use common\models\CodModel;
use common\models\PluginModel;
use common\models\DepositTradeModel;
use common\models\DepositAccountModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Business;
use common\library\Def;

/**
 * @Id BasePayment.php 2018.6.1 $
 * @author mosir
 */

class BasePayment extends BasePlugin
{
	/**
	 * 支付插件系列
	 * @var string $instance
	 */
	protected $instance = 'payment';

	/**
	 * 提交支付请求
	 */
	public function pay($orderInfo = [])
	{
		return $this->createPayform();
	}

	/**
	 * 退款原路返回
	 */
	public function refund($orderInfo = [])
	{
		return false;
	}

	/**
	 * 获取规范的支付表单数据
	 * @param array $params
	 * @param string $method post|get
	 */
	public function createPayform($params = [], $gateway = '', $method = 'get')
	{
		return [
			'gateway' => $gateway,
			'params' => $params,
			'method' => $method,
			'payment_code' 	=> $this->code
		];
	}

	/**
	 * 获取通知地址 
	 * @param string $payTradeNo 支付交易号
	 */
	public function createNotifyUrl($payTradeNo = '')
	{
		return Url::toRoute(['paynotify/notify', 'payTradeNo' => $payTradeNo], true);
	}

	/**
	 * 获取返回地址 
	 * @param string $payTradeNo 支付交易号
	 */
	public function createReturnUrl($payTradeNo = '')
	{
		if ($this->params->callback) {
			return $this->params->callback . (strripos($this->params->callback, '?') > -1 ? '&' : '?') . 'payTradeNo=' . $payTradeNo;
		}

		// VUE页面
		return Basewind::baseUrl() . '/trade/cashier/result/' . $payTradeNo;
	}

	/**
	 * 获取支付订单号
	 * @param array $orderInfo 订单信息
	 * @param string $length 支付订单号的长度
	 */
	public function getPayTradeNo($orderInfo, $length = 0)
	{
		$payTradeNo = DepositTradeModel::genPayTradeNo($orderInfo, $length);
		DepositTradeModel::updateAll(['payTradeNo' => $payTradeNo], ['in', 'trade_id', array_keys($orderInfo['tradeList'])]);

		return $payTradeNo;
	}

	/**
	 * 获取支付回调返回的数据
	 */
	public function getNotify()
	{
		if (($notify = Yii::$app->request->post())) {
			return $notify;
		}

		$notify = file_get_contents("php://input");
		if (!$notify) {
			$notify = Yii::$app->request->get();
		}

		// 针对支付宝通知，如果发现签名失败，可考虑此转化
		if (isset($notify['fund_bill_list'])) {
			$notify['fund_bill_list'] = stripslashes($notify['fund_bill_list']);
		}

		return $notify;
	}

	/**
	 * 返回给支付网关的回馈信息
	 * @param bool $target
	 */
	public function verifyResult($target = false)
	{
		echo $target ? 'success' : 'fail';
	}

	/**
	 * 获取已启用并满足一定条件的支付方式
	 * @param int $store_id 店铺ID 这个主要是为了获取店铺配置的货到付款方式
	 * @param bool $removeImproper 是否移除不合理的支付方式
	 * @param array $extraParams 其他搜索条件
	 */
	public function getEnabled($store_id = 0, $removeImproper = true, $extraParams = array())
	{
		$query = PluginModel::find()->where(['instance' => $this->instance, 'enabled' => 1])->indexBy('code');
		if ($extraParams) {
			$query->andWhere($extraParams);
		}
		$payments = $query->asArray()->all();

		// 如果卖家没有配置货到付款，则去除该支付方式
		if (isset($payments['cod']) && (!($cod = CodModel::checkAndGetInfo($store_id)) || empty($cod->regions))) {
			unset($payments['cod']);
		}

		// 只取合适的支付方式
		if ($removeImproper === true) {
			$suitable = ['deposit', 'alipay', 'wxpay', 'unionpay', 'cod']; // PC
			$terminal = $this->getTerminal();

			// APP
			if ($terminal == 'APP') {
				$suitable = ['deposit', 'alipay', 'wxapppay'];
			}

			// 小程序
			elseif ($terminal == 'MP') {
				$suitable = Basewind::isWeixin() ? ['deposit', 'wxmppay'] : ['deposit', 'alipay'];
			}

			// 公众号/H5浏览器
			elseif ($terminal == 'WAP') {
				if (Basewind::isWeixin()) {
					$suitable = ['deposit', 'wxpay'];
				} else if (Basewind::isAlipay()) {
					$suitable = ['deposit', 'alipay'];
				} else {
					$suitable = ['deposit', 'wxpay', 'alipay', 'unionpay'];
				}
			}

			foreach ($payments as $key => $payment) {
				if (!in_array($payment['code'], $suitable)) {
					unset($payments[$key]);
				}
			}
		}

		if ($payments) {
			// 排序，使得在收银台界面余额支付排在第一位
			$tmp = array();
			foreach ($payments as $key => $payment) {
				if (in_array($payment['code'], array('deposit'))) {
					$tmp[$key] = $payment;
					unset($payments[$key]);
					break;
				}
			}
			$payments = $tmp + $payments;
		}

		return $payments;
	}

	/**
	 * 获取支付方式的键值
	 * @param array $payments
	 */
	public function getKeysOfPayments($payments = [])
	{
		$keys = array();
		foreach ($payments as $key => $value) {
			$keys[] = $value['code'];
		}

		return $keys;
	}

	/**
	 * 根据订单数据，获取可用的支付方式
	 * @param array $orderInfo 订单数据
	 * @param bool $showDepositPay 是否显示余额支付方式
	 * @param bool $showCodPay 是否显示货到付款方式 
	 */
	public function getAvailablePayments($orderInfo, $showDepositPay = true, $showCodPay = false)
	{
		$selected = $errorMsg = false;
		$all_payments = $cod_payments = array();

		$payments = $this->getEnabled(0);
		foreach ($payments as $key => $payment) {

			// 如果支付的金额为零，那么仅显示余额付款和货到付款，不显示网银等支付
			if ($orderInfo['amount'] <= 0) {
				if (!in_array($payment['code'], ['deposit', 'cod'])) continue;
			}

			if (in_array($payment['code'], ['deposit'])) {
				if ($showDepositPay === true) {

					$depositAccount = DepositAccountModel::getAccountInfo(Yii::$app->user->id);
					if (in_array($depositAccount->pay_status, ['ON'])) {
						if ($orderInfo['amount'] > $depositAccount->money) {
							$payment['disabled'] = 1;
							$payment['disabled_desc'] = Language::get('balancepay_not_enough');
						} else {
							$selected = true;
							$payment['selected'] = 1;
						}
					} else {
						$payment['disabled'] = 1;
						$payment['disabled_desc'] = Language::get('balancepay_disabled');
					}
				} else {
					$payment = false;
				}
			}

			if ($payment !== false) {
				if ($selected === false && !$payment['disabled']) {
					$selected = true;
					$payment['selected'] = 1;
				}

				$all_payments[$payment['code']] = $payment;
			}
		}

		// 检查是否支持货到付款(目前只有购物订单允许使用货到付款，其他如购买应用等不允许)
		// 如果订单已选择货到付款，也不再显示货到付款方式（不应该也不存在选择货到付款方式后再去修改支付方式的情形）
		if ($showCodPay === true && $orderInfo['payType'] != 'COD' && in_array($orderInfo['bizIdentity'], array(Def::TRADE_ORDER))) {
			if ($cod_payments = OrderModel::checkMergePayCodPaymentEnable($orderInfo['orderList'])) {
				$cod_payment = current($cod_payments);

				// 如果还没有选择默认的支付方式，则选择
				if ($selected === false) $cod_payment['selected'] = 1;
				$all_payments[$cod_payment['code']] = $cod_payment;
			}
		}

		if (empty($all_payments)) {
			$errorMsg = Language::get('store_no_payment');
		}

		return array($all_payments, $cod_payments, $errorMsg);
	}

	/**
	 * 异步通知后的充值订单处理 
	 * @param array $orderInfo
	 * @param array $notify_result
	 */
	public function handleRechargeAfterNotify($orderInfo, $notify_result)
	{
		if (!in_array($notify_result['target'], array(Def::ORDER_ACCEPTED, Def::ORDER_FINISHED))) {
			$this->errors = Language::get('trade_status_fail');
			return false;
		}

		// 转到对应的业务实例，不同的业务实例用不同的文件处理，如购物，卖出商品，充值，提现等，每个业务实例又继承支出或者收入
		$depopay_type = Business::getInstance('depopay')->build('recharge');
		if (!$depopay_type->notify($orderInfo)) {
			return false;
		}

		// 充值返金额处理
		if (!$depopay_type->rebate($orderInfo)) {
			return false;
		}

		return true;
	}

	/**
	 * 异步通知后的购物订单处理 
	 * @param array $orderInfo
	 * @param array $notify_result
	 */
	public function handleOrderAfterNotify($orderInfo, $notify_result)
	{
		if (!in_array($notify_result['target'], [Def::ORDER_ACCEPTED, Def::ORDER_FINISHED])) {
			$this->errors = Language::get('trade_status_fail');
			return false;
		}

		if (empty($orderInfo['tradeList'])) {
			$this->errors = Language::get('trade_info_error');
			return false;
		}

		// codepay: 货到付款订单，确认收货后的付款完成处理
		// 转到对应的业务实例，不同的业务实例用不同的文件处理，如购物，卖出商品，充值，提现等，每个业务实例又继承支出或者收入
		$depopay_type = Business::getInstance('depopay')->build($orderInfo['payType'] == 'COD' ? 'codpay' : 'buygoods');

		foreach ($orderInfo['tradeList'] as $tradeInfo) {
			$order_info = $tradeInfo['order_info'];

			$result = $depopay_type->notify([
				'trade_info' => ['userid' => $tradeInfo['buyer_id'], 'party_id' => $tradeInfo['seller_id'], 'amount' => $tradeInfo['amount']],
				'extra_info' => $order_info + ['tradeNo' => $tradeInfo['tradeNo'], 'status' => $tradeInfo['status']],
			]);

			if (!$result) {
				$this->errors = $depopay_type->errors; // 先把错误存起来
				continue;
			}

			// 短信和邮件提醒： 买家已付款通知卖家
			Basewind::sendMailMsgNotify(
				$order_info,
				array(
					'key' => 'toseller_online_pay_success_notify'
				),
				array(
					'key' => 'toseller_online_pay_success_notify',
				)
			);
		}

		return $this->errors ? false : true;
	}

	/**
	 * 异步通知后的购买应用订单处理 
	 * @param array $orderInfo
	 * @param array $notify_result
	 */
	public function handleBuyappAfterNotify($orderInfo, $notify_result)
	{
		if (!in_array($notify_result['target'], [Def::ORDER_ACCEPTED, Def::ORDER_FINISHED])) {
			$this->errors = Language::get('trade_status_fail');
			return false;
		}

		if (empty($orderInfo['tradeList'])) {
			$this->errors = Language::get('trade_info_error');
			return false;
		}

		// 转到对应的业务实例，不同的业务实例用不同的文件处理，如购物，卖出商品，充值，提现等，每个业务实例又继承支出或者收入
		$depopay_type = Business::getInstance('depopay')->build('buyapp');

		// 目前暂不考虑同时支付多个购买APP的交易，所以循环只有一次
		foreach ($orderInfo['tradeList'] as $tradeInfo) {
			$order_info = $tradeInfo['order_info'];
			$result = $depopay_type->notify([
				'trade_info' => ['userid' => $tradeInfo['buyer_id'], 'party_id' => 0, 'amount' => $tradeInfo['amount']],
				'extra_info' => $order_info + ['tradeNo' => $tradeInfo['tradeNo']],
			]);

			if (!$result) {
				$this->errors = $depopay_type->errors; // 先把错误存起来
				continue;
			}
		}
		return $this->errors ? false : true;
	}

	public function getNotifySpecificData()
	{
		return [0, ''];
	}

	/**
	 * 更新交易数据
	 * 主要是处理：将第三方支付平台（如支付宝/微信）的交易参数记录至系统，以便做后续操作（如退款、查询交易等）
	 * @var string $payTradeNo 支付交易号
	 */
	public function updateTradeInfo($payTradeNo)
	{
		list($notifyMoney, $outTradeNo, $openid) = $this->getNotifySpecificData();
		DepositTradeModel::updateAll(['outTradeNo' => $outTradeNo, 'openid' => $openid], ['payTradeNo' => $payTradeNo]);
	}

	/**
	 * 支付终端判断
	 * 包含APP支付，小程序支付，手机WAP支付，电脑PC支付
	 */
	protected function getTerminal()
	{
		if (isset($this->params->terminal)) {
			$terminal = strtoupper($this->params->terminal);
			if (in_array($terminal, ['APP', 'MP', 'WAP', 'PC'])) {
				return $terminal;
			}
		}

		return Basewind::isMobile() ? 'WAP' : 'PC';
	}

	public function getRoute()
	{
		return [
			'index' => 3, // 排序
			'text'  => Language::get('plugin_payment'),
			'url'   => Url::toRoute(['plugin/index', 'instance' => 'payment']),
			'priv'  => ['key' => 'plugin|payment|all', 'label' => Language::get('plugin_payment')]
		];
	}
}
