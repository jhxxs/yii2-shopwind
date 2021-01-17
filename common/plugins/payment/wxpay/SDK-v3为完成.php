<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\plugins\payment\wxpay;

use common\library\Basewind;
use yii;

use common\library\Language;
use common\library\Def;

use common\plugins\payment\wxpay\lib\JsApi_pub;
use common\plugins\payment\wxpay\lib\UnifiedOrder_pub;
use common\plugins\payment\wxpay\lib\Notify_pub;

/**
 * @Id SDK.php 2018.7.19 $
 * @author mosir
 */

class SDK
{
	/**
	 * 网关地址
	 * @var string $gateway
	 */
	public $gateway = null;

	/**
	 * 支付插件实例
	 * @var string $code
	 */
	public $code = null;

	/**
	 * 支付交易号
	 * @var string $payTradeNo
	 */
	public $payTradeNo;

	/**
	 * 通知地址
	 * @var string $notifyUrl
	 */
	public $notifyUrl;

	/**
	 * 返回地址
	 * @var string $returnUrl
	 */
	public $returnUrl;

	/**
	 * 插件配置信息
	 * @var array $config
	 */
	private $config;

	/**
	 * 抓取错误
	 */
	public $errors;

	/**
	 * 构造函数
	 */
	public function __construct(array $config)
	{
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
		$this->config = $config;
	}

	public function getPayform($orderInfo = array())
	{
		//$jsApi = new JsApi_pub($this->config);
		return $this->createOauthUrlForCode($this->returnUrl);
	}

	/**
	 * 统一下单接口
	 */
	private function unifiedOrder($orderInfo, $payTradeNo = '', $code = '')
	{
		$response = Basewind::curl($this->createOauthUrlForOpenid($code));
		$response = json_decode($response);
		$openid = $response->openid;

		$parameters = array(
			'appid' => $this->config['AppID'],
			'mchid' => $this->config['MchID'],
			'description' => $this->getTitle($orderInfo['title']),
			'out_trade_no' => $payTradeNo,
			'notify_url' => $this->notifyUrl,
			'amount' => array(
				'total' => $orderInfo['amount'] * 100,
				'currency' => 'CNY'
			),
			'payer' => array(
				'openid' => $openid
			)
		);
	}


	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj)
	{
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$this->_config['KEY'];
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}


	/**
	 * 	作用：生成可以获得code的url
	 */
	function createOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = $this->config['AppID'];
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE#wechat_redirect";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}

	/**
	 * 	作用：生成可以获得openid的url
	 */
	function createOauthUrlForOpenid($code)
	{
		$urlObj["appid"] = $this->config['AppID'];
		$urlObj["secret"] = $this->config['AppSecret'];
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}

	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar = '';
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}


	private function getTitle($title)
	{
		// body max length <= 128
		if (strlen($title) > 128) {
			return mb_substr($title, 0, 40, Yii::$app->charset); // 代表40个字 120个字符
		}
		return $title;
	}



	public function getParameters($wxcode, $orderInfo, $payTradeNo = '')
	{
		$jsApi = new JsApi_pub($this->config);

		$jsApi->setCode($wxcode);
		$openid = $jsApi->getOpenId();

		if (!$openid) {
			$this->errors = Language::get('openid_empty');
			return false;
		}

		// 使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub($this->config);

		// body max length <= 128
		if (strlen($orderInfo['title']) > 128) {
			$body = mb_substr($orderInfo['title'], 0, 40, Yii::$app->charset); // 代表40个字 120个字符
		} else $body = $orderInfo['title'];

		// 设置统一支付接口参数
		$unifiedOrder->setParameter("openid", $openid);
		$unifiedOrder->setParameter("body", $body); //商品描述

		$unifiedOrder->setParameter("out_trade_no", $payTradeNo); //商户订单号 
		$unifiedOrder->setParameter("total_fee", $orderInfo['amount'] * 100); //总金额
		$unifiedOrder->setParameter("notify_url", $this->notifyUrl); //通知地址 
		$unifiedOrder->setParameter("trade_type", "JSAPI"); //交易类型

		$prepay_id = $unifiedOrder->getPrepayId();
		$jsApi->setPrepayId($prepay_id);
		$jsApiParameters = $jsApi->getParameters();

		return $jsApiParameters;
	}
	public function verifyNotify($orderInfo, $notify)
	{
		// 验证与本地信息是否匹配。这里不只是付款通知，有可能是发货通知，确认收货通知
		if ($orderInfo['payTradeNo'] != $notify['out_trade_no']) {
			// 通知中的订单与欲改变的订单不一致
			$this->errors = Language::get('order_inconsistent');
			return false;
		}
		if ($orderInfo['amount'] != round($notify['total_fee'] / 100, 2)) {
			// 支付的金额与实际金额不一致
			$this->errors = Language::get('price_inconsistent');
			return false;
		}

		//至此，说明通知是可信的，订单也是对应的，可信的
		if (($notify['return_code'] == 'SUCCESS') && ($notify['result_code'] == 'SUCCESS')) {
			$order_status = Def::ORDER_ACCEPTED;
		} else {
			$this->errors = Language::get('undefined_status');
			return false;
		}

		return array('target' => $order_status);
	}

	public function verifySign($notify)
	{
		$notify_pub = new Notify_pub($this->config);

		unset($notify['payTradeNo']);
		$notify_pub->data = $notify;

		return $notify_pub->checkSign();
	}

	public function verifyResult($result)
	{
		$notify = new Notify_pub($this->config);

		if ($result) {
			$notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码	
		} else {
			$notify->setReturnParameter("return_code", "FAIL"); //返回状态码
			$notify->setReturnParameter("return_msg", "SIGNATURE FAIL"); //返回信息
		}

		//回应微信
		$returnXml = $notify->returnXml();
		echo $returnXml;
	}
}
