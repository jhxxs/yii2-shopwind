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

use common\models\CodModel;

use common\library\Basewind;
use common\library\Language;

use frontend\api\library\Respond;

/**
 * @Id CodController.php 2018.10.15 $
 * @author yxyc
 */

class CodController extends \common\base\BaseApiController
{
	/**
	 * 获取指定店铺货到付款
	 * @api 接口访问地址: https://www.xxx.com/api/cod/read
	 */
	public function actionRead()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['store_id']);

		if (!($record = CodModel::find()->where(['store_id' => $post->store_id])->asArray()->one())) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('no_such_item'));
		}

		if ($record['regions']) {
			$record['regions'] = unserialize($record['regions']);
		}

		return $respond->output(true, null, $record);
	}

	/**
	 * 获取指定店铺货到付款
	 * @api 接口访问地址: https://www.xxx.com/api/cod/update
	 */
	public function actionUpdate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams());

		if ($post['regions'] && is_array($post['regions'])) $post['regions'] = serialize($post['regions']);
		else $post['regions'] = '';

		if (!($model = CodModel::find()->where(['store_id' => Yii::$app->user->id])->one())) {
			$model = new CodModel();
			$model->store_id = Yii::$app->user->id;
		}

		$model->regions = $post['regions'];
		$model->status = intval($post['status']);
		if (!$model->save()) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true);
	}
}
