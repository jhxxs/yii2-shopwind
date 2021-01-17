<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\plugins;

use yii;

use common\models\BindModel;
use common\models\UserModel;

use common\library\Language;
use common\library\Timezone;

/**
 * @Id BaseConnect.php 2018.6.1 $
 * @author mosir
 */
 
class BaseConnect extends BasePlugin
{
	/**
	 * 第三方登录插件系列
	 * @var string $instance 
	 */
	protected $instance = 'connect';

	/**
	 * 检测账号是否绑定过
	 * @param string $unionid
	 * @param string $code
	 */
	public function isBind($unionid = null, $code = null)
	{
		$bind = BindModel::find()->select('userid,enabled')->where(['unionid' => $unionid, 'code' => $code])->one();
		
		// 包含登录状态绑定的情况，如果当前登录用户与原有绑定用户不一致，则修改为新绑定
		if($bind && $bind->userid && $bind->enabled && (Yii::$app->user->isGuest || ($bind->userid == Yii::$app->user->id))) 
		{
			// 如果该unionid已经绑定， 则检查该用户是否存在
			if(!UserModel::find()->where(['userid' => $bind->userid])->exists()) {
				// 如果没有此用户，则说明绑定数据过时，删除绑定
				BindModel::deleteAll(['userid' => $bind->userid]);
				return $this->setErrors(Language::get('bind_data_error'));
			}
			return $bind->userid;
		}
		return false;
	}

	/**
	 * 跳转至绑定页面
	 * @param object $response
	 */
	public function goBind($response = null)
	{
		$result = array(
			'code' 			=> $response->code, 
			'unionid' 		=> $response->unionid,
			'expire_time' 	=> Timezone::gmtime() + 600,
			'access_token' 	=> $response->access_token,
			'refresh_token' => $response->refresh_token,
			'portrait'		=> isset($response->portrait) ? $response->portrait : null,
			'nickname'		=> isset($response->nickname) ? $response->nickname : null
		);
		$this->setErrors('redirect...'); // use this line to exit()
		return Yii::$app->controller->redirect(['connect/bind', 'token' => base64_encode(json_encode($result))]);
	}
}