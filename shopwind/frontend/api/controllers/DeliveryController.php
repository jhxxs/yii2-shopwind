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
use yii\helpers\ArrayHelper;

use common\models\StoreModel;
use common\models\DeliveryTimerModel;
use common\models\DeliveryTemplateModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Plugin;

use frontend\api\library\Respond;

/**
 * @Id DeliveryController.php 2018.10.20 $
 * @author yxyc
 */

class DeliveryController extends \common\base\BaseApiController
{
	/**
	 * 读取运费模板
	 * @api 接口访问地址: https://www.xxx.com/api/delivery/read
	 */
	public function actionRead()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['id']);

		if ($delivery = DeliveryTemplateModel::findOne($post->id)) {
			$record = DeliveryTemplateModel::formatTemplateForEdit($delivery);
			$record['created'] = Timezone::localDate('Y-m-d H:i:s', $record['created']);
		}

		return $respond->output(true, null, $record);
	}

	/**
	 * 更新运费模板
	 * @api 接口访问地址: https://www.xxx.com/api/delivery/update
	 */
	public function actionUpdate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['id']);

		// 如果不是卖家，不给与更新
		if (!StoreModel::find()->where(['store_id' => Yii::$app->user->id])->exists()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('no_seller'));
		}

		$model = new \frontend\home\models\My_deliveryForm(['store_id' => Yii::$app->user->id, 'id' => $post->id]);
		if (!$model->save($post, true)) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true);
	}

	/**
	 * 删除运费模板
	 * @api 接口访问地址: https://www.xxx.com/api/delivery/delete
	 */
	public function actionDelete()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['id']);

		if (!DeliveryTemplateModel::find()->where(['store_id' => Yii::$app->user->id])->andWhere(['<>', 'template_id', $post->id])->exists()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('last_tempalte'));
		}

		if (!$post->id || (!$model = DeliveryTemplateModel::find()->where(['store_id' => Yii::$app->user->id,  'template_id' => $post->id])->one()) || !$model->delete()) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('drop_fail'));
		}

		return $respond->output(true);
	}

	/**
	 * 获取指定店铺的运费模板
	 * @api 接口访问地址: https://www.xxx.com/api/delivery/template
	 */
	public function actionTemplate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['store_id', 'goods_id']);

		// 运费模板列表
		$list = DeliveryTemplateModel::find()->where(['store_id' => $post->store_id])->asArray()->all();
		foreach ($list as $key => $value) {
			$list[$key] = DeliveryTemplateModel::formatTemplateForEdit($value);
			$list[$key]['created'] = Timezone::localDate('Y-m-d H:i:s', $value['created']);
		}

		return $respond->output(true, null, ['list' => $list]);
	}

	/**
	 * 获取快递公司列表
	 * @api 接口访问地址: https://www.xxx.com/api/delivery/company
	 */
	public function actionCompany()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		$list = [];
		if ($model = Plugin::getInstance('express')->autoBuild()) {
			$companys = $model->getCompanys();
			foreach ($companys as $key => $value) {
				$list[] = array('code' => $key, 'name' => $value);
			}
			// $this->params = ['plugin' => $model->getCode(), 'config' => $model->getConfig(), 'list' => $list];
		}
		return $respond->output(true, null, ['list' => $list]);
	}

	/**
	 * 获取指定店铺物流配送时效
	 * @api 接口访问地址: https://www.xxx.com/api/delivery/timer
	 */
	public function actionTimer()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['store_id']);
		if (!isset($post->store_id) || !$post->store_id) $post->store_id = Yii::$app->user->id;

		$record = DeliveryTimerModel::find()->select('id,store_id,rules')
			->where(['store_id' => $post->store_id])
			->orderBy(['id' => SORT_DESC])
			->asArray()->one();

		if ($record && $record['rules']) {
			$rules = unserialize($record['rules']);

			$now = Timezone::gmtime();
			$today = Timezone::localDate('Y-m-d 00:00:00', true);

			// 格式化时间显示文本
			if (is_array($rules)) {
				foreach ($rules as $key => $value) {
					$day = intval($value['day']);
					$rules[$key]['arrived'] = ($day == 0 ? '（今日）' : ($day == 1 ? '（明天）' : '')) . Timezone::localDate('m月d日', Timezone::gmstr2time($today) + $day * 24 * 3600) . ' ' . $value['time'];

					$start = Timezone::gmstr2time(Timezone::localDate('Y-m-d') . $value['start'] . ':00');
					$end = Timezone::gmstr2time(Timezone::localDate('Y-m-d') . $value['end'] . ':59');

					if (empty($record['result']) && ($now > $start && $now < $end)) {
						$record['result'] = '最快' . $rules[$key]['arrived'] . ' 前送达';
					}
				}
				$record['rules'] = $rules;
			}
		}

		return $respond->output(true, null, $record ? $record : []);
	}

	/**
	 * 更新店铺物流配送时效
	 * @api 接口访问地址: https://www.xxx.com/api/delivery/timerupdate
	 */
	public function actionTimerupdate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		if (isset($post->rules)) $post->rules = ArrayHelper::toArray($post->rules);

		// 如果不是卖家，不给与更新
		if (!StoreModel::find()->where(['store_id' => Yii::$app->user->id])->exists()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('no_seller'));
		}

		if (!($model = DeliveryTimerModel::find()->where(['store_id' => Yii::$app->user->id])->one())) {
			$model = new DeliveryTimerModel();
			$model->created = Timezone::gmtime();
			$model->store_id = Yii::$app->user->id;
		}
		$model->name = '物流配送时效';
		$model->rules = $post->rules ? serialize($post->rules) : '';

		if (!$model->save()) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true);
	}
}
