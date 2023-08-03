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

use common\library\Basewind;
use common\library\Def;
use common\library\Page;

use frontend\api\library\Respond;

/**
 * @Id TemplateController.php 2018.10.13 $
 * @author yxyc
 */

class TemplateController extends \common\base\BaseApiController
{
	/**
	 * 获取模板拖拽模块配置信息(该接口为内部使用，不要开放)
	 * @api 接口访问地址: http://api.xxx.com/template/block
	 */
	public function actionBlock()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(false)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		if (!isset($post->page) || empty($post->page)) $post->page = 'index';
		if (!isset($post->client) || empty($post->client)) $post->client = 'h5';

		// 读取配置文件
		$config = require_once(Yii::getAlias('@public') .
			'/data/pagediy/' . $post->client . '/' . Page::getTheme($post->client) . '.' . $post->page . '.config.php'
		);

		if (!$config || !is_array($config)) {
			return $respond->output(true, null, []);
		}

		$setting = [];
		foreach ($config['config'] as $list) {
			foreach ($list as $value) {
				$widget = $config['widgets'][$value];
				if (!$widget['options']) $widget['options'] = [];

				// 头部模块
				if (isset($post->header)) {
					if ($post->header === true && (!isset($widget['options']['header']) || !$widget['options']['header'])) {
						continue;
					}
					if ($post->header === false && isset($widget['options']['header']) && $widget['options']['header']) {
						continue;
					}
				}

				// 底部模块
				if (isset($post->footer)) {
					if ($post->footer === true && (!isset($widget['options']['footer']) || !$widget['options']['footer'])) {
						continue;
					}
					if ($post->footer === false && isset($widget['options']['footer']) && $widget['options']['footer']) {
						continue;
					}
				}

				$setting[] = $widget;
			}
		}

		// 处理本地图片路径问题
		$this->replaceAll($setting, 'data/files', Def::fileSaveUrl() .  '/data/files');
		if (isset($post->block) && $post->block) {
			foreach ($setting as $value) {
				if ($value['name'] == $post->block) {
					$setting = $value;
					break;
				}
			}
		}

		$result = array_merge([
			'list' => $setting,
			'enabled' => 1
		], (isset($config['page']) && is_array($config['page'])) ? $config['page'] : []);

		return $respond->output(true, null, $result);
	}

	/**
	 * 将配置文件中的图片路径转为绝对路径
	 * 递归处理，支持无限级
	 */
	private function replaceAll(&$array, $search, $replace)
	{
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					$this->replaceAll($array[$key], $search, $replace);
				} else if (is_string($value) && substr($value, 0, strlen($search)) == $search) {
					$array[$key] = str_replace($search, $replace, $value);
				}
			}
		} else if (is_string($array) && substr($array, 0, strlen($search)) == $search) {
			str_replace($search, $replace, $array);
		}
	}
}
