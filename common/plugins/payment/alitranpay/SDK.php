<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\plugins\payment\alitranpay;

use yii;

use common\library\Language;
use common\plugins\payment\alipay\lib\AopCertClient;
use common\plugins\payment\alipay\lib\request\AlipayFundTransUniTransferRequest;

/**
 * @Id SDK.php 2018.7.19 $
 * @author mosir
 */

class SDK extends \common\plugins\payment\alipay\SDK
{
	/**
	 * 转账接口只支持证书模式
	 */
	public function transfer($orderInfo = array())
    {
		if(!$this->appId || !$this->rsaPrivateKey || !$this->alipayCertPath || !$this->appCertPath || !$this->rootCertPath) {
			$this->errors = Language::get('params fail');
			return false;
		}

		try {
			
			$aop = new AopCertClient();
			$aop->appId 				= $this->appId;
			$aop->rsaPrivateKey 		= $this->rsaPrivateKey;
			$aop->postCharset 			= Yii::$app->charset;
			$aop->signType 				= $this->signType;
			$aop->apiVersion 			= '1.0';
			
			// 证书模式
			$aop->alipayrsaPublicKey 	= $aop->getPublicKey($this->alipayCertPath);

			//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
			$aop->isCheckAlipayPublicCert = true;
			
			//调用getCertSN获取证书序列号
			$aop->appCertSN = $aop->getCertSN($this->appCertPath);
			
			//调用getRootCertSN获取支付宝根证书序列号
			$aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);
			
			// 提现金额最低0.1
			$biz_content = array(
				'order_title' => $orderInfo['title'],
				'out_biz_no'  => $this->payTradeNo,
				'trans_amount'=> $orderInfo['amount'],
				'product_code'=> 'TRANS_ACCOUNT_NO_PWD',
				'payee_info'  => [
					'identity'  => $orderInfo['payee']['account'],
					'identity_type' => 'ALIPAY_LOGON_ID',
					'name' => $orderInfo['payee']['name'],
				],
				'biz_scene' => 'DIRECT_TRANSFER',
				//'remark' => '',
			);
			
			$request = new AlipayFundTransUniTransferRequest();
			$request->setBizContent(json_encode($biz_content));
			$result = $aop->execute($request); 
			
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$resultCode = $result->$responseNode->code;
			if(empty($resultCode) || $resultCode != 10000) {
				$this->errors = $result->$responseNode->sub_msg ? $result->$responseNode->sub_msg : $result->$responseNode->msg;
				return false;
			} 

			return $result->$responseNode->order_id;

		} catch (\Exception $e) {
			$this->errors = $e->getMessage();
			return false;
		}
	}
}