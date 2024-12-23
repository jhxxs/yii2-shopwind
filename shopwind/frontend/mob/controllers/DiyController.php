<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\mob\controllers;

use Yii;
use yii\helpers\FileHelper;

use common\library\Basewind;
use common\library\Language;
use common\library\Message;
use common\library\Page;

/**
 * @Id DiyController.php 2018.4.16 $
 * @author mosir
 */

class DiyController extends \common\base\BaseController
{
	public function actionIndex()
	{
		$this->params['page'] = Page::seo(['title' => Language::get('home')]);
		return $this->render('../diy.index.html', $this->params);
	}
	public function actionCommunity()
	{
		$this->params['page'] = Page::seo(['title' => Language::get('community')]);
		return $this->render('../diy.community.html', $this->params);
	}

	public function actionPage()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true);
		$file = $this->getFile($post->id);

		$basePath = Yii::getAlias('@public') . '/data/pagediy/h5';
		if ($list = FileHelper::findFiles($basePath, ['recursive' => false, 'only' => ['*.config.php']])) {
			foreach ($list as $value) {
				$checkfile = substr($value, strripos($value, DIRECTORY_SEPARATOR) + 1);
				if ($file != $checkfile) continue;

				if ($array = include($value)) {
					$page = $array['page'];
					break;
				}
			}
		}
		if (!$page) {
			return Message::warning(Language::get('no_such_page'));
		}

		$this->params['pageid'] = $post->id;
		$this->params['page'] = Page::seo(['title' => $page['title']]);
		return $this->render('../diy.page.html', $this->params);
	}

	private function getFile($page)
	{
		return Page::getTheme('h5') . '.' . $page . '.config.php';
	}
}
