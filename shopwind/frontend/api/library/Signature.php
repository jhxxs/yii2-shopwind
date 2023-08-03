<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\library;

use yii;

/**
 * @Id Signature.php 2018.10.6 $
 * @author yxyc
 */

class Signature
{
	public $code;
	public $message;

	public function __construct()
	{
	}

	/*
	 * 验证签名
	 */
	public function verify($post)
	{
		/**
		 * 郑重声明：
		 * 开源版本不提供数据请求验签策略，您的数据相当于在裸奔，建议正式运营使用商业版本！！！
		 * 开源版本不提供数据请求验签策略，您的数据相当于在裸奔，建议正式运营使用商业版本！！！
		 * 开源版本不提供数据请求验签策略，您的数据相当于在裸奔，建议正式运营使用商业版本！！！
		 */

		return true;
	}
}
