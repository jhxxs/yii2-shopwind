<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace common\base;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

use common\library\Basewind;
use common\library\Language;

/**
 * @Id BaseController.php 2018.10.20 $
 * @author mosir
 */

class BaseController extends Controller
{
	/**
	 * 不启用布局
	 */
	public $layout = false;

	/**
	 * 关闭CSRF验证
	 */
	public $enableCsrfValidation = false;

	/**
	 * 当前视图
	 */
	public $view    = null;

	/**
	 * 允许游客访问的例外Action
	 */
	public $extraAction = null;

	/**
	 * 公共参数
	 */
	public $params 	= null;

	/**
	 * 错误载体
	 */
	public $errors  = null;

	/**
	 * 初始化
	 * @var array $params 传递给视图的公共参数
	 */
	public function init()
	{
		parent::init();

		// 环境/安装检测
		Basewind::environment();

		// 视图公共参数
		$this->params = [
			'baseUrl'		=> Basewind::baseUrl(),
			'homeUrl'		=> Basewind::homeUrl(),
			'siteUrl'		=> Basewind::siteUrl(),
			'sysversion'	=> Basewind::getVersion(),
			'priceFormat'	=> isset(Yii::$app->params['price_format']) ? Yii::$app->params['price_format'] : '',
			'enablePretty'	=> Yii::$app->urlManager->enablePrettyUrl ? true : false,
			'lang' 			=> Language::find($this->id)
		];
	}

	/**
	 * 公用操作，避免重复代码
	 * 当两个控制器如：AController和BController需要一个captcha方法,那就可以放到actions
	 * 比如在 site/captcha 的时候，会先在actions方法中找对应请求的 captcha 方法
	 * 如果没有那么就会在控制器中找actionCaptcha
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
				//'view' => '@webroot/404.html'
				'view' => '@public/404.html'
			],
			'captcha' => [
				'class' 			=> 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' 	=> YII_ENV_TEST ? 'shopwind' : null,
				'maxLength' 		=> 4,
				'minLength' 		=> 4,
				'width' 			=> 108,
				'fontFile'			=> '@common/font/gordon.ttf',
			],
			'checkCaptcha' => [
				'class' => 'common\actions\CheckCaptchaAction'
			],
			'checkUser' => [
				'class' => 'common\actions\CheckUserAction'
			],
			'checkEmail' => [
				'class' => 'common\actions\CheckEmailAction'
			],
			'checkPhone' => [
				'class' => 'common\actions\CheckPhoneAction'
			],
			'sendCode' => [
				'class'	=> 'common\actions\SendCodeAction'
			],
			'sendEmail' => [
				'class'	=> 'common\actions\SendEmailAction'
			],
			'jslang' => [
				'class' => 'common\actions\JsLangAction',
				'lang'  => $this->params['lang']
			],
			'clearCache' => [
				'class' => 'common\actions\ClearCacheAction'
			]
		];
	}

	public function beforeAction($action)
	{
		// 网站关闭
		if (Basewind::getApp() != 'admin' && !Yii::$app->params['site_status']) {
			if ($action->controller->id != 'site' || $action->id != 'closed') {
				header('location:' . Url::toRoute(['site/closed'], Basewind::homeUrl()));
				return false;
			}
		}

		return parent::beforeAction($action);
	}

	/**
	 * 访问权限
	 */
	public function checkAccess($action)
	{
		return true;
	}

	public function accessWarning($params = [])
	{
		$this->params = array_merge($this->params, $params, ['notice' => ['done' => false, 'icon' => 'warning', 'msg' => Language::get('access_limit')]]);
		Yii::$app->response->data = Yii::$app->controller->render('../message.html', $this->params);
		return false;
	}

	/**
	 * 给视图调用的用户登录信息
	 * @return array $visitor
	 * @desc 该处不应该连接AR（model）获取数据，会导致数据恢复操作报错（待验证）
	 */
	public function getVisitor()
	{
		if (Yii::$app->user->isGuest) {
			return ['userid' => 0, 'username' => Language::get('guest'), 'store_id' => 0, 'portrait' => Yii::$app->params['default_user_portrait']];
		}

		$identity = Yii::$app->user->identity;
		if (!$identity->portrait) $identity->portrait = Yii::$app->params['default_user_portrait'];

		// 去掉敏感字段
		unset($identity->password, $identity->password_reset_token, $identity->auth_key);

		return ArrayHelper::toArray($identity);
	}
}
