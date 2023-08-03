<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace backend\controllers;

use Yii;

use common\library\Basewind;
use common\library\Language;
use common\library\Message;
use common\library\Page;

/**
 * @Id ChannelController.php 2018.9.10 $
 * @author mosir
 */

class ChannelController extends \common\base\BaseAdminController
{
	public function actionAdd()
	{
		if (!Yii::$app->request->isPost) {
			$this->params['page'] = Page::seo(['title' => Language::get('channel_add')]);
			return $this->render('../channel.form.html', $this->params);
		} else {
			$post = Basewind::trimAll(Yii::$app->request->post(), true);

			$model = new \backend\models\ChannelForm(['client' => $post->client]);
			if (!($model->save($post, true))) {
				return Message::popWarning($model->errors);
			}

			return Message::popSuccess(Language::get('add_successed'));
		}
	}
	public function actionEdit()
	{
		$get = Basewind::trimAll(Yii::$app->request->get(), true);

		$model = new \backend\models\ChannelForm(['client' => $get->client, 'page' => $get->page]);
		if (!Yii::$app->request->isPost) {
			if ($array = require($model->getFile($get->page, true))) {
				$this->params['channel'] = $array['page'];
			}

			$this->params['page'] = Page::seo(['title' => Language::get('channel_edit')]);
			return $this->render('../channel.form.html', $this->params);
		} else {
			$post = Basewind::trimAll(Yii::$app->request->post(), true);
			if (!$model->save($post, true)) {
				return Message::popWarning($model->errors);
			}
			return Message::popSuccess(Language::get('edit_successed'), ['template/index']);
		}
	}

	public function actionDelete()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true);

		$model = new \backend\models\ChannelForm(['client' => $post->client, 'page' => $post->page]);
		if (!$model->delete($post, true)) {
			return Message::warning($model->errors);
		}
		return Message::display(Language::get('drop_successed'), ['template/index']);
	}
}
