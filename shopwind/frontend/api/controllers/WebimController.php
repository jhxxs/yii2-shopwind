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

use common\models\WebimModel;
use common\models\UserModel;
use common\models\StoreModel;

use common\library\Basewind;
use common\library\Timezone;
use common\library\Language;
use common\library\Page;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id WebimController.php 2022.5.25 $
 * @author mosir
 */

class WebimController extends \common\base\BaseApiController
{
	/**
	 * 获取会话列表
	 * @api 接口访问地址: http://api.xxx.com/webim/list
	 */
	public function actionList()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['limit']);

		$list = WebimModel::find()->select('fromid,toid,store_id,store_name')
			->where(['or', ['fromid' => Yii::$app->user->id], ['toid' => Yii::$app->user->id]])
			->groupBy('groupid,fromid,toid,store_id,store_name')
			->limit($post->limit ? $post->limit : 50)
			->asArray()->all();

		foreach ($list as $key => $value) {
			if ($user = UserModel::find()->select('userid,username,nickname,portrait')->where(['userid' => $value['fromid'] == Yii::$app->user->id ? $value['toid'] : $value['fromid']])->asArray()->one()) {
				$user['portrait'] = Formatter::path($user['portrait'], 'portrait');
				$value['to'] = $user;

				// 获取最后一条信息
				$record = WebimModel::find()->select('id,fromid,toid,content,created')->where(['or', ['fromid' => $value['fromid'], 'toid' => $value['toid']], ['fromid' => $value['toid'], 'toid' => $value['fromid']]])->orderBy(['id' => SORT_DESC])->asArray()->one();
				$record['created'] = Timezone::localDate('m/d H:i', $record['created']);

				// 获取未读消息数量
				$record['unreads'] = intval(WebimModel::find()->where(['fromid' => $record['fromid'], 'toid' => Yii::$app->user->id, 'unread' => 1])->sum('unread'));
				$list[$key] = array_merge($value, $record);
			} else unset($list[$key]);
		}

		// 使最后发言显示在前面
		array_multisort(array_column($list, 'id'), SORT_DESC, $list);

		return $respond->output(true, null, $list);
	}

	/**
	 * 获取客服聊天记录
	 * @api 接口访问地址: http://api.xxx.com/webim/logs
	 */
	public function actionLogs()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['toid', 'page', 'page_size']);

		$query = WebimModel::find()->orderBy(['id' => SORT_DESC]);
		$query->where(['or', ['fromid' => Yii::$app->user->id, 'toid' => $post->toid], ['toid' => Yii::$app->user->id, 'fromid' => $post->toid]]);

		if (isset($post->unread)) {
			$query->andWhere(['unread' => $post->unread ? 1 : 0]);
		}

		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();

		$readarray = [];
		foreach ($list as $key => $value) {
			if ($users = UserModel::find()->select('userid,username,nickname,portrait')->where(['in', 'userid', [$value['fromid'], $value['toid']]])->asArray()->all()) {
				foreach ($users as $user) {
					$user['portrait'] = Formatter::path($user['portrait'], 'portrait');
					$list[$key][$user['userid'] == $value['fromid'] ? 'from' : 'to'] = $user;
				}
			}
			if ($value['toid'] == Yii::$app->user->id && $value['unread']) {
				$readarray[] = $value['id'];
			}
			$list[$key]['created'] = Timezone::localDate('m/d H:i', $value['created']);
		}

		// 设置为已读
		if ($readarray) {
			WebimModel::updateAll(['unread' => 0], ['in', 'id', $readarray]);
		}

		// 使最新的记录显示在后面
		array_multisort(array_column($list, 'id'), SORT_ASC, $list);
		return $respond->output(true, null, ['list' => $list, 'pagination' => Page::formatPage($page, false)]);
	}

	/**
	 * 发送消息
	 * @api 接口访问地址: http://api.xxx.com/webim/send
	 */
	public function actionSend()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['toid', 'store_id']);

		if (empty($post->content)) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('content_empty'));
		}
		if ($post->toid == Yii::$app->user->id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('talk_yourself'));
		}
		if (!UserModel::find()->where(['userid' => $post->toid])->exists()) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('talk_empty'));
		}

		$model = new WebimModel();
		$model->toid = $post->toid;
		$model->fromid = Yii::$app->user->id;
		$model->store_id = intval($post->store_id);
		$model->content = $post->content;
		$model->unread = 1;
		$model->created = Timezone::gmtime();

		if ($model->store_id && ($store = StoreModel::find()->select('store_name')->where(['store_id' => $model->store_id])->one())) {
			$model->store_name = $store->store_name;
		}
		if ($query = WebimModel::find()->select('groupid')->where(['or', ['fromid' => $model->fromid, 'toid' => $model->toid], ['fromid' => $model->toid, 'toid' => $model->fromid]])->one()) {
			$model->groupid = $query->groupid;
		} else $model->groupid = md5($model->fromid . ':' . $model->toid);

		if (!$model->save()) {
			return $respond->output(false, $model->errors());
		}

		return $respond->output(true);
	}

	/**
	 * 获取未读消息数
	 * @api 接口访问地址: http://api.xxx.com/webim/unread
	 */
	public function actionUnread()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		$record = intval(WebimModel::find()->where(['toid' => Yii::$app->user->id, 'unread' => 1])->sum('unread'));
		return $respond->output(true, null, $record);
	}
}
