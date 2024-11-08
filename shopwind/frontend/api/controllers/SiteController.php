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
use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id SiteController.php 2018.11.20 $
 * @author yxyc
 */

class SiteController extends \common\base\BaseApiController
{
	/**
	 * 获取站点配置参数(该接口为内部使用，不要开放)
	 * @api 接口访问地址: https://www.xxx.com/api/site/read
	 */
	public function actionRead()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		$this->params = [
			'homeUrl' => Basewind::homeUrl(),
			'mobileUrl' => Basewind::mobileUrl(true),
			'site_name' => Yii::$app->params['site_name'],
			'site_desc' => Yii::$app->params['site_keywords'],
			'site_logo' => Formatter::path(Yii::$app->params['site_logo']),
			'site_keywords'  => explode(',', Yii::$app->params['site_keywords']),
			'hot_keywords' => explode(',', Yii::$app->params['hot_keywords']),
			'store_allow' => floatval(Yii::$app->params['store_allow']),
			'baidukey' => Yii::$app->params['baidukey'],
			'icp' => Yii::$app->params['icp_number'],
			'ibl' => Yii::$app->params['ibl'],
			'qrcode' => [
				'android' => Formatter::path(Yii::$app->params['androidqrcode']),
				'ios' => Formatter::path(Yii::$app->params['iosqrcode'])
			]
		];

		return $respond->output(true, null, $this->params);
	}

	public function actionError()
	{
		return \yii\helpers\Json::encode(['message' => 'The page you want to visit does not exist']);
	}
}
