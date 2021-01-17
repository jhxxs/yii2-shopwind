<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\plugins\payment\wxmppay\lib;

use common\plugins\payment\wxmppay\lib\Common_util_pub;
use common\plugins\payment\wxmppay\lib\WxPayConf_pub;

/**
 * @Id Mp_pub.php 2018.6.3 $
 * @author mosir
 *
 */

class Mp_pub extends Common_util_pub
{
	var $parameters;//app参数，格式为数组
	var $prepay_id;//使用统一支付接口得到的预支付id
	var $partnerid;
	var $curl_timeout;//curl超时时间
	
	function __construct($config) 
	{
		parent::__construct($config);
	
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;
	}

	/**
	 * 	作用：设置prepay_id
	 */
	function setPrepayId($prepayId)
	{
		$this->prepay_id = $prepayId;
	}

	function setPartnerId($partnerid) 
	{
		$this->partnerid = $partnerid;
	}

	/**
	 * 	作用：设置mp的参数
	 */
	public function getParameters()
	{
		$jsApiObj["appId"] = $this->_config['AppID'];
		$timeStamp = time();
	    $jsApiObj["timeStamp"] = "$timeStamp";
	    $jsApiObj["nonceStr"] = $this->createNoncestr();
		$jsApiObj["package"] = "prepay_id=$this->prepay_id";
	    $jsApiObj["signType"] = "MD5";
	    $jsApiObj["paySign"] = $this->getSign($jsApiObj);
	    $this->parameters = $jsApiObj;
		
		return $this->parameters;
	}
}