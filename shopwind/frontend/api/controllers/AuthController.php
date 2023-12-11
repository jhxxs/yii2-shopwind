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

use common\models\UserModel;
use common\models\UserTokenModel;
use common\models\BindModel;
use common\models\StoreModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Def;
use common\library\Plugin;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id AuthController.php 2018.10.15 $
 * @author yxyc
 */

class AuthController extends \common\base\BaseApiController
{
	/**
	 * 获取访问TOKEN
	 * @api 接口访问地址: http://api.xxx.com/auth/token
	 */
	public function actionToken()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false, true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		if (!($result = $this->createToken($respond))) {
			return $respond->output(Respond::TOKEN_FAIL, Language::get('loginfail'));
		}
		return $respond->output(true, null, $result);
	}

	/**
	 * 获取用户登录TOKEN
	 * @api 接口访问地址: http://api.xxx.com/auth/login
	 */
	public function actionLogin()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false, true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		$post->logintype = $respond->getRealLogintype($post);

		// 非Web场景
		if (in_array($post->logintype, array_keys(Plugin::getInstance('connect')->build()->getList()))) {
			return $this->unilogin($respond, $post);
		}
		// 手机号加密码登录
		if ($post->logintype == 'password') {
			return $this->phonePasswordLogin($respond, $post);
		}
		// 手机号加短信验证码登录
		if ($post->logintype == 'verifycode') {
			return $this->phoneCodeLogin($respond, $post);
		}
	}

	/**
	 * 第三方账号联合登录授权接口[Web端，适合于授权跳转的场景]
	 * @api 接口访问地址: http://api.xxx.com/auth/connect
	 */
	public function actionConnect()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		$post->logintype = $respond->getRealLogintype($post);

		$connect = Plugin::getInstance('connect')->build($post->logintype, $post);
		if (!$connect->isInstall()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('plugin_disallow'));
		}

		$result = $connect->login();
		return $respond->output(true, null, ['redirect' => $result]);
	}

	/**
	 * UniAPP统一登录接口[APP和小程序场景]
	 * 兼容微信/QQ/支付宝/Apple登录
	 */
	private function unilogin($respond, $post)
	{
		$connect = Plugin::getInstance('connect')->build($post->logintype, $post);
		if (!$connect->isInstall()) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('plugin_disallow'));
		}

		if (!$connect->callback(true)) {
			return $respond->output(Respond::TOKEN_FAIL, $connect->errors);
		}

		$identity = UserModel::findOne($connect->userid);
		if (!$identity || !($result = $this->createToken($respond, $identity))) {
			return $respond->output(Respond::TOKEN_FAIL, Language::get('loginfail'));
		}

		if (!($array = $this->getUserInfo($identity, $post))) {
			return $respond->output(Respond::PARAMS_INVALID, $this->errors);
		}

		// 登录后的处理
		UserModel::afterLogin($identity);

		$result['user_info'] = $array;
		return $respond->output(true, null, $result);
	}

	/**
	 * 手机号和密码登录
	 * @var $phone_mob|$password
	 */
	private function phonePasswordLogin($respond, $post)
	{
		if (!$post->phone_mob) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_mob_required'));
		}
		if (!($identity = UserModel::find()->where(['phone_mob' => $post->phone_mob])->one())) {
			return $respond->output(Respond::USER_NOTEXIST, Language::get('no_such_user'));
		}
		if (!$post->password || !$identity->validatePassword($post->password)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('password_valid'));
		}
		if (!($result = $this->createToken($respond, $identity))) {
			return $respond->output(Respond::TOKEN_FAIL, Language::get('loginfail'));
		}
		if (!($array = $this->getUserInfo($identity, $post))) {
			return $respond->output(Respond::PARAMS_INVALID, $this->errors);
		}

		// 登录后的处理
		UserModel::afterLogin($identity);

		$result['user_info'] = $array;
		return $respond->output(true, null, $result);
	}

	/**
	 * 手机号和短信登录/注册
	 * @var string $phone_mob|$verifycode
	 */
	private function phoneCodeLogin($respond, $post)
	{
		// 手机短信验证
		if (($smser = Plugin::getInstance('sms')->autoBuild())) {
			// 兼容微信session不同步问题
			if ($post->verifycodekey) {
				$smser->setSessionByCodekey($post->verifycodekey);
			}
			if (!Basewind::isPhone($post->phone_mob)) {
				return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_mob_invalid'));
			}
			if (empty($post->verifycode) || (md5($post->phone_mob . $post->verifycode) != Yii::$app->session->get('phone_code'))) {
				return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_code_check_failed'));
			}
			if (Timezone::gmtime() - Yii::$app->session->get('last_send_time_phone_code') > 120) {
				return $respond->output(Respond::PARAMS_INVALID, Language::get('phone_code_check_timeout'));
			}
			// 至此，短信验证码是正确的
			if (!($identity = UserModel::find()->where(['phone_mob' => $post->phone_mob])->one())) {
				if (!($identity = $this->createUser($post))) {
					return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_exception'));
				}
			}

			if (!($result = $this->createToken($respond, $identity))) {
				return $respond->output(Respond::TOKEN_FAIL, Language::get('loginfail'));
			}
			if (!($array = $this->getUserInfo($identity, $post))) {
				return $respond->output(Respond::PARAMS_INVALID, $this->errors);
			}

			// 登录后的处理
			UserModel::afterLogin($identity);

			$result['user_info'] = $array;
			return $respond->output(true, null, $result);
		}

		return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_fail'));
	}

	/**
	 * 生成TOKEN
	 * @desc 设置过期时间为7天
	 */
	private function createToken($respond, $identity = null)
	{
		$expire_time = Timezone::gmtime() + $respond->expired;
		if ($identity) {
			$token = md5($expire_time . $identity->userid . $identity->username . $expire_time . mt_rand(1000, 9999));
			if (!($model = UserTokenModel::find()->where(['userid' => $identity->userid])->one())) {
				$model = new UserTokenModel();
				$model->userid = $identity->userid;
			}
		} else {
			$model = new UserTokenModel();
			$model->userid = 0;
			$token = md5($expire_time . mt_rand(1000000, 9999999) . $expire_time . $respond->expired);
		}
		$model->token = $token;
		$model->expire_time = $expire_time;
		if (!$model->save()) {
			return false;
		}
		return ['token' => $token, 'expire_time' => Timezone::localDate('Y-m-d H:i:s', $expire_time)];
	}

	/**
	 * 返回给客户端的用户信息
	 */
	private function getUserInfo($identity, $post = null)
	{
		// 用户不存在或被锁定（在移动端当做注销处理）
		if (!$identity || $identity->locked) {
			$this->errors = Language::get('user_logoff');
			return false;
		}

		$identity->portrait = Formatter::path($identity->portrait, 'portrait');
		$identity->last_login = Timezone::localDate('Y-m-d H:i:s', $identity->last_login);
		$identity = ArrayHelper::toArray($identity);
		foreach ($identity as $key => $value) {
			if (!in_array($key, ['userid', 'username', 'phone_mob', 'nickname', 'portrait', 'last_login', 'last_ip'])) {
				unset($identity[$key]);
			}
		}

		// 查询是否有店铺
		if ($arary = StoreModel::find()->select('store_id,store_name')->where(['state' => Def::STORE_OPEN, 'store_id' => $identity['userid']])->asArray()->one()) {
			$identity = array_merge($identity, $arary);
		}

		// 微信端(返回给客户端使用，必须返回与终端匹配的值)
		$identity['openid'] = $this->getOpenId($identity['userid'], $post);

		return $identity;
	}

	/**
	 * 创建用户
	 * @param object $post
	 */
	private function createUser($post)
	{
		$model = new \frontend\home\models\UserRegisterForm();

		do {
			$model->username = UserModel::generateName();
			$model->password  = mt_rand(1000, 9999);
			$model->phone_mob = $post->phone_mob ? $post->phone_mob : '';
			$user = $model->register([
				'portrait' => $post->portrait ? $post->portrait : '',
				'nickname' => $post->nickname ? $post->nickname : $model->username,
				'regtype'  => $post->terminal
			]);
		} while (!$user);

		return $user;
	}

	/**
	 * 获取微信平台下OPENID值
	 * 【登录后返回的字段需要该值】
	 */
	private function getOpenId($userid, $post = null)
	{
		$code = $post->logintype;
		if (in_array($code, ['password', 'verifycode'])) {
			$post->terminal = strtoupper($post->terminal);

			if ($post->terminal == 'APP') {
				$code = 'weixinapp';
			} else if ($post->terminal == 'MP') {
				$code = 'weixinmp';
			} elseif (Basewind::isWeixin()) {
				$code = 'weixin';
			} elseif (!Basewind::isMobile()) { // for PC
				$code = 'weixinapp';
			}
		}
		if (in_array($code, ['weixin', 'weixinmp', 'weixinapp'])) {
			$openid = BindModel::find()->select('openid')->where(['and', ['userid' => $userid], ['!=', 'openid', ''], ['code' => $code]])->scalar();
			return $openid ? $openid : '';
		}
		return '';
	}
}
