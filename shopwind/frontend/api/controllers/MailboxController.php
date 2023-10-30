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

use common\models\MailboxModel;

use common\library\Basewind;
use common\library\Page;

use frontend\api\library\Respond;

/**
 * @Id MailboxController.php 2018.10.15 $
 * @author yxyc
 */

class MailboxController extends \common\base\BaseApiController
{
	/**
	 * 获取用户消息列表
	 * @api 接口访问地址: http://api.xxx.com/mailbox/list
	 */
	public function actionList()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);

		$query = MailboxModel::find()->where(['or', ['from_id' => Yii::$app->user->id], ['to_id' => Yii::$app->user->id]]);
		if ($post->title) {
			$query->andWhere(['like', 'title', $post->title]);
		}

		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->orderBy(['new' => SORT_DESC, 'id' => SORT_DESC])->offset($page->offset)->limit($page->limit)->asArray()->all();
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];

		return $respond->output(true, null, $this->params);
	}
}
