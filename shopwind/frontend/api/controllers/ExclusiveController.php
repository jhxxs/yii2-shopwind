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

use common\models\PromotoolItemModel;

use common\library\Basewind;;
use common\library\Promotool;

use frontend\api\library\Respond;

/**
 * @Id ExclusiveController.php 2018.12.8 $
 * @author yxyc
 */

class ExclusiveController extends \common\base\BaseApiController
{
	/**
	 * 获取手机专享配置
	 * @api 接口访问地址: https://www.xxx.com/api/exclusive/read
	 */
    public function actionRead()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['goods_id']);
		$exclusiveTool = Promotool::getInstance('exclusive')->build(['store_id' => Yii::$app->user->id]);

		// 读商品配置
		if($post->goods_id) {
			if($item = $exclusiveTool->getItemInfo(['goods_id' => $post->goods_id])) {
            	$result = array_merge($item['config'] ? $item['config'] : [], ['status' => $item['status']]);
			}
		}
		// 读店铺配置
		else {
			if(($result = $exclusiveTool->getInfo())) {
				unset($result['rules']['type']);
				$result = array_merge(['status' => $result['status']], $result['rules']);
			}
		}

		return $respond->output(true, null, $result);
	}

	/**
	 * 设置手机专享优惠信息
	 * @api 接口访问地址: https://www.xxx.com/api/exclusive/update
	 */
    public function actionUpdate()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['status']);

		$model = new \frontend\home\models\Seller_exclusiveForm(['store_id' => Yii::$app->user->id]);
		if(!$model->save($post, true)) {
			return $respond->output(Respond::CURD_FAIL, $model->errors);
		}

		return $respond->output(true);		
	}
}