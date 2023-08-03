<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\controllers;

use Yii;

use common\library\Basewind;
use common\library\Message;

/**
 * @Id SiteController.php 2018.11.20 $
 * @author mosir
 */

class SiteController extends \common\base\BaseController
{
	public function actionClosed()
	{
		if (Yii::$app->params['site_status']) {
			return $this->redirect(Basewind::baseUrl());
		}

		$reason = Yii::$app->params['closed_reason'] ? Yii::$app->params['closed_reason'] : '站点已关闭！';
		return Message::warning($reason);
	}

	public function actionError()
	{
		//echo 'error action config in main.php';
		exit('The page you want to visit does not exist');
	}
}
