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

use common\models\UserModel;
use common\models\SmsModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Plugin;

use frontend\api\library\Respond;

/**
 * @Id SmsController.php 2018.10.19 $
 * @author yxyc
 */

class SmsController extends \common\base\BaseApiController
{
	/**
	 * 获取用户手机短信配置
	 * @api 接口访问地址: http://api.xxx.com/sms/read
	 */
	public function actionRead()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 初始化检查
		if (!($smser = Plugin::getInstance('sms')->autoBuild()) || !$smser->verify(false)) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('msgkey_not_config'));
		}

		$array = [];
		if ($record = SmsModel::find()->where(['userid' => Yii::$app->user->id])->asArray()->one()) {
			$array = explode(',', $record['functions']);
		}

		return $respond->output(true, null, $array);
	}

	/**
	 * 获取手机短信发送场景
	 * @api 接口访问地址: http://api.xxx.com/sms/scene
	 */
	public function actionScene()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		// 初始化检查
		if (!($smser = Plugin::getInstance('sms')->autoBuild()) || !$smser->verify(false)) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('msgkey_not_config'));
		}

		$list = [];
		if ($array = $smser->getFunctions((bool) $post->all)) {
			foreach ($array as $value) {
				$list[] = ['code' => $value, 'name' => Language::get($value)];
			}
		}

		return $respond->output(true, null, $list);
	}

	/**
	 * 手机短信配置
	 * @api 接口访问地址: http://api.xxx.com/sms/update
	 */
	public function actionUpdate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['state']);
		if($post->scene) {
			$array = [];
			foreach($post->scene as $key => $value) {
				if($value) $array[] = $key;
			}
		}

		if (!($model = SmsModel::find()->where(['userid' => Yii::$app->user->id])->one())) {
			$model = new SmsModel();
			$model->userid = Yii::$app->user->id;
		}
		$model->state = $post->state ? 1 : 0;
		$model->functions = implode(',', $array);
		if (!$model->save()) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}
		return $respond->output(true);
	}

	/**
	 * 发送手机短信
	 * @api 接口访问地址: http://api.xxx.com/sms/send
	 */
	public function actionSend()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		if (!($smser = Plugin::getInstance('sms')->autoBuild())) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('msgkey_not_config'));
		}

		// 验证手机号
		if (!Basewind::isPhone($post->phone_mob)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_mob_invalid'));
		}

		// 如果是注册，则验证传递的手机号是否唯一
		if (in_array($post->purpose, array('register'))) {
			if (UserModel::find()->where(['phone_mob' => $post->phone_mob])->exists()) {
				return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_mob_existed'), $post->phone_mob);
			}
			$smser->scene = 'touser_register_verify';
		}

		$code = mt_rand(1000, 9999);
		$smser->receiver = $post->phone_mob;
		$smser->templateParams = ['code' => $code];

		if (!($codekey = $smser->send())) {
			return $respond->output(Respond::HANDLE_INVALID, $smser->errors);
		}

		Yii::$app->session->set('phone_code', md5($post->phone_mob . $code));
		Yii::$app->session->set('last_send_time_phone_code', Timezone::gmtime());

		return $respond->output(true, Language::get('send_msg_successed'), ['codekey' => $codekey]);
	}
}
