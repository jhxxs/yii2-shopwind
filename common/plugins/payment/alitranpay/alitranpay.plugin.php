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
use yii\helpers\Url;

use common\models\DepositTradeModel;

use common\library\Basewind;
use common\library\Language;

use common\plugins\BasePayment;
use common\plugins\payment\alitranpay\SDK;

/**
 * @Id alitranpay.plugin.php 2018.6.3 $
 * @author mosir
 * 
 * 转账到支付宝接口
 * 注意：该接口并非前台下单的支付接口，而是作为提现使用的后台自动转账接口
 */

class Alitranpay extends BasePayment
{
	/**
     * 支付插件实例
	 * @var string $code
	 */
	protected $code = 'alitranpay';

	/**
     * SDK实例
	 * @var object $client
     */
	private $client = null;

	/**
	 * 提交支付请求
	 * 请确保服务器环境局配置了SSL(SSL certificate problem: unable to get local issuer certificate)
	 */
	public function transfer($orderInfo = array())
    {
		if($orderInfo['amount'] < 0.1) {
			$this->errors = Language::get('money shall not be less than 0.1');
			return false;
		}

		$sdk = $this->getClient();
		$sdk->payTradeNo = $orderInfo['payTradeNo'];
	
		$result = $sdk->transfer($orderInfo);
        if($result === false) {
			$this->errors = $sdk->errors;
			return false;
		}

		return $result;
    }

	/**
     * 获取SDK实例
     */
    public function getClient()
    {
        if($this->client === null) {
            $this->client = new SDK($this->config);
        }
        return $this->client;
    }
}