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
use common\models\BindModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Page;
use common\library\Timezone;
use common\library\Plugin;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id UserController.php 2018.10.13 $
 * @author yxyc
 */

class UserController extends \common\base\BaseApiController
{
	/**
	 * 获取用户信息列表
	 * @api 接口访问地址: http://api.xxx.com/user/list
	 */
	public function actionList()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);

		$query = UserModel::find()->alias('u')
			->select('u.userid,u.username,u.email,u.nickname,u.real_name,u.gender,u.birthday,u.phone_mob,u.qq,u.portrait,u.last_login,s.store_id,i.amount as integral,da.money')
			->joinWith('store s', false)
			->joinWith('integral i', false)
			->joinWith('depositAccount da', false);

		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach ($list as $key => $value) {
			$list[$key]['portrait'] = Formatter::path($value['portrait'], 'portrait');
			$list[$key]['last_login'] = Timezone::localDate('Y-m-d H:i:s', $value['last_login']);
			$list[$key]['integral'] = floatval($value['integral']);
			$list[$key]['money'] = floatval($value['money']);
		}
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];
		return $respond->output(true, Language::get('user_list'), $this->params);
	}

	/**
	 * 获取用户单条信息
	 * @api 接口访问地址: http://api.xxx.com/user/read
	 */
	public function actionRead()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['userid']);

		$query = UserModel::find()->alias('u')
			->select('u.userid,u.username,u.email,u.nickname,u.real_name,u.gender,u.birthday,u.phone_mob,u.qq,u.portrait,u.last_login,s.store_id,i.amount as integral,da.money')
			->joinWith('store s', false)
			->joinWith('integral i', false)
			->joinWith('depositAccount da', false);
		if ($post->userid) $query->where(['u.userid' => $post->userid]);
		elseif ($post->username) $query->where(['u.username' => $post->username]);
		elseif ($post->phone_mob) $query->where(['u.phone_mob' => $post->phone_mob]);
		else $query->where(['u.userid' => Yii::$app->user->id]);

		if (!($record = $query->asArray()->one())) {
			return $respond->output(Respond::USER_NOTEXIST, Language::get('no_such_user'));
		}
		$record['portrait'] = Formatter::path($record['portrait'], 'portrait');
		$record['last_login'] = Timezone::localDate('Y-m-d H:i:s', $record['last_login']);
		$record['integral'] = floatval($record['integral']);
		$record['money'] = floatval($record['money']);

		$record['thirds'] = BindModel::find()->select('openid')->where(['userid' => Yii::$app->user->id])->indexBy('code')->column();
		return $respond->output(true, null, $record);
	}

	/**
	 * 插入用户信息
	 * @api 接口访问地址: http://api.xxx.com/user/add
	 */
	public function actionAdd()
	{
	}

	/**
	 * 更新用户信息
	 * @api 接口访问地址: http://api.xxx.com/user/update
	 */
	public function actionUpdate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['gender']);

		$model = new \frontend\api\models\UserForm(['userid' => Yii::$app->user->id]);
		if (!$model->exists($post)) {
			return $respond->output(Respond::RECORD_NOTEXIST, $model->errors);
		}
		if (!$model->valid($post)) {
			return $respond->output(Respond::PARAMS_INVALID, $model->errors);
		}
		if (!$model->save($post, false)) {
			return $respond->output(Respond::CURD_FAIL, Language::get('user_update_fail'));
		}
		$record = UserModel::find()->select('userid,username,nickname,real_name,phone_mob,email,gender,portrait,birthday,qq')->where(['userid' => $model->userid])->asArray()->one();
		$record['portrait'] = Formatter::path($record['portrait'], 'portrait');

		return $respond->output(true, null, $record);
	}

	/**
	 * 更新用户登录密码
	 * @api 接口访问地址: http://api.xxx.com/user/password
	 */
	public function actionPassword()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		if (empty($post->password)) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('passwod_empty'));
		}

		// 手机短信验证
		if (!($smser = Plugin::getInstance('sms')->autoBuild())) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_exception'));
		}

		// 兼容微信等session不同步问题
		if ($post->verifycodekey) {
			$smser->setSessionByCodekey($post->verifycodekey);
		}
		if (empty($post->verifycode) || (md5($post->phone_mob . $post->verifycode) != Yii::$app->session->get('phone_code'))) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_code_check_failed'));
		}
		if (Timezone::gmtime() - Yii::$app->session->get('last_send_time_phone_code') > 120) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_code_check_timeout'));
		}

		// 至此，短信验证码是正确的
		$model = UserModel::findOne(Yii::$app->user->id);
		$model->setPassword($post->password);
		if (!$model->save()) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}
		return $respond->output(true);
	}

	/**
	 * 更新用户手机号
	 * @api 接口访问地址: http://api.xxx.com/user/phone
	 */
	public function actionPhone()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		if (!Basewind::isPhone($post->phone_mob)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_mob_invalid'));
		}
		if (!Basewind::checkPhone($post->phone_mob, Yii::$app->user->id)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_mob_existed'));
		}

		// 验证登录密码（非必须）
		if (isset($post->password)) {
			$identity = UserModel::find()->where(['userid' => Yii::$app->user->id])->one();
			if (!$post->password || !$identity->validatePassword($post->password)) {
				return $respond->output(Respond::PARAMS_INVALID, Language::get('username_password_error'));
			}
		}

		// 手机短信验证
		if (!($smser = Plugin::getInstance('sms')->autoBuild())) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_exception'));
		}

		// 兼容微信等session不同步问题
		if ($post->verifycodekey) {
			$smser->setSessionByCodekey($post->verifycodekey);
		}
		if (empty($post->verifycode) || (md5($post->phone_mob . $post->verifycode) != Yii::$app->session->get('phone_code'))) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_code_check_failed'));
		}
		if (Timezone::gmtime() - Yii::$app->session->get('last_send_time_phone_code') > 120) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_code_check_timeout'));
		}

		// 至此，短信验证码是正确的
		UserModel::updateAll(['phone_mob' => $post->phone_mob], ['userid' => Yii::$app->user->id]);
		return $respond->output(true, null, ['userid' => Yii::$app->user->id, 'phone_mob' => $post->phone_mob]);
	}

	/**
	 * 删除用户信息
	 * @api 接口访问地址: http://api.xxx.com/user/delete
	 */
	public function actionDelete()
	{
	}

	/**
	 * 注销当前账号
	 * @api 接口访问地址: http://api.xxx.com/user/logoff
	 */
	public function actionLogoff()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		if (!($model = UserModel::find()->where(['userid' => Yii::$app->user->id])->one())) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('no_such_user'));
		}
		$model->locked = 1;
		if (!$model->save()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_exception'));
		}

		return $respond->output(true);
	}

	/**
	 * 关联账号绑定[已有用户且登录的情况下]
	 * @api 接口访问地址: http://api.xxx.com/user/bind
	 */
	public function actionBind()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		$post->logintype = $respond->getRealLogintype($post);

		$connect = Plugin::getInstance('connect')->build($post->logintype, $post);
		if (!$connect->isInstall()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('plugin_disallow'));
		}

		if (!($response = $connect->getAccessToken())) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_exception'));
		}

		// 如果已有绑定，则提示解除
		if (BindModel::find()->where(['and', ['unionid' => $response->unionid], ['!=', 'userid', Yii::$app->user->id]])->exists()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('bind_existed'));
		}

		$response->code = $post->logintype;
		if (!$connect->createBind($response, Yii::$app->user->id)) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_exception'));
		}

		$list = BindModel::find()->select('openid')->where(['userid' => Yii::$app->user->id])->indexBy('code')->column();
		return $respond->output(true, null, $list);
	}

	/**
	 * 关联账号解绑[已有用户且登录的情况下]
	 * @api 接口访问地址: http://api.xxx.com/user/unbind
	 */
	public function actionUnbind()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		//$post->logintype = $respond->getRealLogintype($post);

		// 对微信来说，微信公众号登录、APP登录与小程序登录CODE值不同，必须都删去关联
		$wxarray = ['weixin', 'weixinmp', 'weixinapp'];
		$array = in_array($post->logintype, $wxarray) ? $wxarray : [$post->logintype];
		BindModel::deleteAll(['and', ['userid' => Yii::$app->user->id], ['in', 'code', $array]]);

		$list = BindModel::find()->select('openid')->where(['userid' => Yii::$app->user->id])->indexBy('code')->column();
		return $respond->output(true, null, $list);
	}
}
