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

use common\models\UserModel;
use common\models\BindModel;
use common\models\WeixinReplyModel;

use common\library\Message;
use common\library\Weixin;

/**
 * @Id WeixinController.php 2018.12.5 $
 * @author luckey
 */

class WeixinController extends \common\base\BaseController
{
	public function actionIndex()
	{
		$client = Weixin::getInstance();

		// 验证
		if (!$client->valid()) {
			return Message::warning($client->errors);
		}

		$post = $client->getPostData();

		//接收事件推送
		if ($post['MsgType'] == 'event') {

			//关注事件
			if ($post['Event'] == 'subscribe') {
				$wxInfo = $client->getUserInfo($post['FromUserName']);

				if ($client->config['autologin']) {
					$this->register($wxInfo);
				}

				$reply = WeixinReplyModel::find()->where(['userid' => 0, 'action' => 'subscribe'])->asArray()->one();
			}

			//点击菜单拉取消息时的事件推送,后台设定为图文消息
			elseif ($post['Event'] == 'CLICK') {
				$reply = WeixinReplyModel::find()->where(['reply_id' => intval($post['EventKey'])])->asArray()->one();
			}
		}
		// 接受消息推送
		else {
			$reply = $this->getContents($post['Content']);
		}

		if ($reply) {
			// type: 1=图文、0=文本
			$content = $reply['type'] ? [$reply] : $reply['description'];
		}

		if (!$post || !($result = $client->getMsgXML($post['FromUserName'], $post['ToUserName'], $content))) {
			return false;
		}

		exit($result);
	}

	/**
	 * 自动注册
	 * 因前后端分离暂无法实现持久化登录态 
	 */
	private function register($wxInfo)
	{
		if (!$wxInfo) {
			return false;
		}

		if (!isset($wxInfo->unionid)) {
			$wxInfo->unionid = $wxInfo->openid;
		}

		// 已绑定
		$query = BindModel::find()->where(['and', ['unionid' => $wxInfo->unionid], ['code' => 'weixin']])->one();
		if ($query && UserModel::find()->where(['userid' => $query->userid])->exists()) {
			return $query->userid;
		}

		do {
			$model = new \frontend\home\models\UserRegisterForm();
			$model->username = UserModel::generateName('weixin');
			$model->password  = mt_rand(1000, 9999);
			$user = $model->register(['nickname' => $wxInfo->nickname, 'portrait' => $wxInfo->headimgurl]);
		} while (!$user);

		// 将绑定信息插入数据库
		$bind = (object)array('code' => 'weixin', 'unionid' => $wxInfo->unionid, 'openid'  => $wxInfo->openid, 'portrait' => $user->portrait, 'nickname' => $user->nickname);
		if (!BindModel::bindUser($bind, $user->userid)) {
			return false;
		}

		return $user->userid;
	}

	/**
	 * 获取文本消息和图文消息 
	 * 从2018年10月12日起，微信公众平台图文消息被限制为1条
	 */
	private function getContents($word = '')
	{
		// 先找关键词回复
		if ($word) {
			$result = WeixinReplyModel::find()
				->where(['and', ['userid' => 0, 'action' => 'keyword'], ['like', 'keywords', $word]])
				->orderBy(['reply_id' => SORT_DESC])
				->asArray()
				->one();
		}

		// 没有则找消息回复
		if (!$result) {
			$result = WeixinReplyModel::find()
				->where(['userid' => 0, 'action' => 'text'])
				->orderBy(['reply_id' => SORT_DESC])
				->asArray()
				->one();
		}

		return $result;
	}
}
