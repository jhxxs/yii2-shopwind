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

use yii\web\BadRequestHttpException;

/**
 * @Id SDKRuntimeException.php 2018.6.3 $
 * @author mosir
 *
 */

class SDKRuntimeException extends BadRequestHttpException
{
	public function errorMessage()
	{
		return $this->getMessage();
	}

}