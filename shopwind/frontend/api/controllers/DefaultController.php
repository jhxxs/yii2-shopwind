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

use common\library\Basewind;

/**
 * @Id DefaultController.php 2018.10.13 $
 * @author yxyc
 */

class DefaultController extends \common\base\BaseApiController
{
	public function init()
	{
		$this->params = [
			'baseUrl'   => Basewind::baseUrl(),
			'homeUrl' 	=> Basewind::homeUrl(),
			'siteUrl'	=> Basewind::siteUrl()
		];
	}
}
