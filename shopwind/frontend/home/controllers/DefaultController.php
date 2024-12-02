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

use common\library\Page;

/**
 * @Id DefaultController.php 2018.3.1 $
 * @author mosir
 */

class DefaultController extends \common\base\BaseController
{
	/**
	 * VUE页面视图入口文件
	 * 目的为通过服务器去渲染TKD，处理SEO问题
	 */
	public function actionIndex()
	{
		$root = Yii::getAlias('@public') . '/pc';
		if (file_exists($root . '/index.html') && is_dir($root . '/assets')) {

			// 获取VUE编译后的入口文件
			$file = file_get_contents($root . '/index.html');

			// 替换TDK
			list($title, $keywords, $description) = array_values($this->getTkd());
			preg_match("/<title.*?>(.*?)<\/title>/is", $file, $match);
			$html = str_replace(
				$match[0],
				"<title>{$title}</title><meta name=\"Keywords\" content=\"{$keywords}\" /><meta name=\"Description\" content=\"{$description}\" />",
				$file
			);

			echo $html;
			exit();
		}

		if (stripos($_SERVER['REQUEST_URI'], 'home') > -1) {
			return $this->redirect(['diy/index']);
		}

		$this->params['page'] = Page::seo();
		return $this->render('../index.html', $this->params);
	}

	private function getTkd()
	{
		$array = Page::seo();
		unset($array['menu']);
		return $array;
	}
}
