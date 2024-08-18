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
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\FileHelper;

use common\library\Basewind;
use common\library\Language;
use common\library\Message;
use common\library\Resource;
use common\library\Page;
use common\library\Widget;

/**
 * @Id TemplateController.php 2018.9.5 $
 * @author mosir
 */

class TemplateController extends \common\base\BaseAdminController
{
	public function actionIndex()
	{
		$dir = Yii::getAlias('@public/data/pagediy');
		$list = FileHelper::findDirectories($dir, ['recursive' => false]);
		foreach ($list as $item) {
			$value = substr($item, strripos($item, DIRECTORY_SEPARATOR) + 1);
			$this->params['pages'][$value] = $this->getEditablePages($value);
		}

		$this->params['mobileUrl'] = Basewind::mobileUrl(true);
		$this->params['_head_tags'] = Resource::import(['style' => 'javascript/treetable/treetable.css,javascript/dialog/dialog.css']);
		$this->params['_foot_tags'] = Resource::import(['script' => 'javascript/jquery.ui/jquery.ui.js,javascript/dialog/dialog.js']);

		$this->params['page'] = Page::seo(['title' => Language::get('template_diy')]);
		return $this->render('../template.index.html', $this->params);
	}

	/* 保存页面 */
	public function actionSave()
	{
		$post = Yii::$app->request->post();

		// 初始化变量 页面中所有的挂件 id => name
		$widgets = !empty($post['widgets']) ? $post['widgets'] : [];

		// 页面中所有挂件的位置配置数据
		$config = !empty($post['config']) ? $post['config'] : [];

		// 当前所编辑的页面
		list($client, $template, $page) = $this->getClientParams();

		if (!$page) {
			return Message::warning(Language::get('no_such_page'));
		}

		$pages = $this->getEditablePages($client);
		if (empty($pages[$page])) {
			return Message::warning(Language::get('no_such_page'));
		}

		$page_config = Widget::getInstance($client)->getConfig($template, $page);
		if (!is_array($page_config)) {
			return Message::warning(Language::get('config_page_fail'));
		}

		// 写入位置配置信息
		$page_config['config'] = $config;

		// 原始挂件信息
		$old_widgets = $page_config['widgets'];

		// 清空原始挂件信息
		$page_config['widgets']  = [];

		// 写入挂件信息,指明挂件ID是哪个挂件以及相关配置
		foreach ($widgets as $widget_id => $widget_name) {
			// 写入新的挂件信息
			$page_config['widgets'][$widget_id]['name'] = $widget_name;
			$page_config['widgets'][$widget_id]['options']  = [];

			// 如果进行了新的配置，则写入
			if (isset($page_config['tmp'][$widget_id])) {
				$page_config['widgets'][$widget_id]['options'] = $page_config['tmp'][$widget_id]['options'];
				continue;
			}

			// 写入旧的配置信息
			if (!isset($old_widgets[$widget_id])) $old_widgets[$widget_id]['options'] = [];
			$page_config['widgets'][$widget_id]['options'] = $old_widgets[$widget_id]['options'];
		}

		// 清除临时的配置信息
		unset($page_config['tmp']);

		// 保存配置
		$this->saveConfig($client, $template, $page, $page_config);
		$vueurl = ($client == 'pc' ? Basewind::baseUrl() : Basewind::mobileUrl(true)) . $pages[$page]['route'];

		return Message::result(['vueurl' => $vueurl], Language::get('publish_successed'));
	}

	/* 编辑页面 */
	public function actionEdit()
	{
		// 当前所编辑的页面
		list($client, $template, $page) = $this->getClientParams();

		if (!$page) {
			return Message::warning(Language::get('no_such_page'));
		}

		// 注意，通过这种方式获取的页面中跟用户相关的数据都是游客，这样就保证了统一性，所见即所得编辑不会因为您是否已登录而出现不同
		if (!($html = $this->getPageHtml($client, $page))) {
			return Message::warning(Language::get('no_such_page'));
		}

		// 给BODY内容加上外标签，以便控制样式
		preg_match("/<body.*?>(.*?)<\/body>/is", $html, $match);
		$html = str_replace($match[0], "<div id='template_page'><div class='ewraper hidden'>" . $match[0] . "</div></div>", $html);

		// 让页面可编辑，并输出HTML
		echo $this->makeEditable($client, $page, $html);
		exit(0);
	}

	/* 获取编辑器面板 */
	public function actionPanel()
	{
		list($client, $template, $page) = $this->getClientParams();

		// 获取挂件列表
		$widgets = Widget::getInstance($client)->getList();
		$pages = $this->getEditablePages($client);

		// 将不属于此页面的挂件去除		
		$pageDetail = isset($pages[$page]) ? $pages[$page] : [];
		$pageKey = (isset($pageDetail['name']) && !empty($pageDetail['name'])) ? $pageDetail['name'] : $page;

		// 匹配某个模板某个页面 如：default.index
		$pageKey1 = $template . '.' . $pageKey;
		// 匹配某个模板所有页面 如: default.*
		$pageKey2 = $template . '.*';

		foreach ($widgets as $key => $widget) {
			if (isset($widget['belongs']) && !empty($widget['belongs'])) {
				$belongs = explode(',', $widget['belongs']);
				if (!in_array($pageKey1, $belongs) && !in_array($pageKey2, $belongs)) {
					unset($widgets[$key]);
				}
			}
		}

		header('Content-Type:text/html;charset=' . Yii::$app->charset);
		$this->params['widgets'] = Json::encode($widgets);
		$this->params['page'] = $this->getPage();

		return $this->render('../template.panel.html', $this->params);
	}

	/* 配置挂件 */
	public function actionConfig()
	{
		$get = Basewind::trimAll(Yii::$app->request->get(), true);

		// 当前所编辑的页面
		list($client, $template, $page) = $this->getClientParams();

		if (!Yii::$app->request->isPost) {
			if (!$get->name || !$get->id || !$page) {
				return Message::warning(Language::get('no_such_widget'));
			}

			$page_config = Widget::getInstance($client)->getConfig($template, $page);

			$options = isset($page_config['widgets'][$get->id]) ? $page_config['widgets'][$get->id]['options'] : [];
			if (isset($page_config['tmp'][$get->id])) {
				$options = $page_config['tmp'][$get->id]['options'];
			}

			$widget = Widget::getInstance($client)->build($get->id, $get->name, $options);
			header('Content-Type:text/html;charset=' . Yii::$app->charset);
			$widget->displayConfig();
		} else {
			if (!$get->name || !$get->id || !$page) {
				return Message::warning(Language::get('no_such_widget'));
			}
			$page_config = Widget::getInstance($client)->getConfig($template, $page);
			$widget = Widget::getInstance($client)->build($get->id, $get->name, $page_config['widgets'][$get->id]['options']);

			if (($options = $widget->parseConfig(Yii::$app->request->post())) === false) {
				return Message::warning(Language::get('no_such_widget'));
			}
			$page_config['tmp'][$get->id] = ['name' => $get->name, 'options' => $options];

			// 保存配置信息
			$this->saveConfig($client, $template, $page, $page_config);

			// 返回即时更新的数据
			$widget->setOptions($options);
			$contents = $widget->getContents();

			return Message::result($contents, Language::get('save_successed'));
		}
	}

	/* 保存页面配置文件 */
	public function saveConfig($client = 'pc', $template, $page, $page_config)
	{
		$config_file = Widget::getInstance($client)->getConfigPath($template, $page);
		$page_config = Basewind::filterAll($page_config); // 安全过滤

		$php_data = "<?php\n\nreturn " . var_export($page_config, true) . ";";
		return file_put_contents($config_file, $php_data, LOCK_EX);
	}

	/* 添加挂件到页面中 */
	public function actionAddwidget()
	{
		$name = Yii::$app->request->get('name', '');

		// 当前所编辑的页面
		list($client, $template, $page) = $this->getClientParams();

		if (!$name || !$page) {
			return Message::warning(Language::get('no_such_widget'));
		}

		$page_config = Widget::getInstance($client)->getConfig($template, $page);
		$id = Widget::getInstance($client)->genUniqueId($page_config);
		$widget = Widget::getInstance($client)->build($id, $name, []);
		$contents = $widget->getContents();

		return Message::result(['contents' => $contents, 'widget_id' => $id]);
	}

	public function getPage()
	{
		list($client, $template, $page) = $this->getClientParams();

		$pages = $this->getEditablePages($client);
		if (!$pages || !isset($pages[$page]) || empty($pages[$page])) {
			return false;
		}
		return $pages[$page];
	}

	/* 获取欲编辑的页面的HTML */
	public function getPageHtml($client = 'pc', $page = 'index')
	{
		$contextOptions = [
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false
			]
		];

		$pages = $this->getEditablePages($client);
		if (isset($pages[$page])) {
			$url = $pages[$page]['url'];
			return file_get_contents($url, false, stream_context_create($contextOptions));
		}

		return false;
	}

	/* 让页面具有编辑功能 */
	public function makeEditable($client = 'pc', $page, $html)
	{
		$editmode = '<script type="text/javascript" src="' . Url::toRoute(['template/jslang']) . '"></script><script type="text/javascript">__PAGE__ = "' . $page . '"; __CLIENT__ ="' . $client . '"; BACK_URL = "' . Basewind::adminUrl() . '";</script>' . Resource::import([
			'script' => 'javascript/jquery.ui/jquery.ui.js,javascript/jquery.ui/i18n/' . Yii::$app->language . '.js,javascript/dialog/dialog.js,javascript/layui/layui.js,javascript/jquery.plugins/jquery.form.js',
			'style' => 'javascript/jquery.ui/themes/smoothness/jquery.ui.css,javascript/dialog/dialog.css,javascript/layui/css/layui.css'
		]) . '<script type="text/javascript" src="' . Resource::getThemeAssetsUrl('js/template_panel.js') . '"></script><link type="text/css" href="' .  Resource::getThemeAssetsUrl('css/template_panel.css') . '" rel="stylesheet" />';

		return str_replace('<!--<editmode></editmode>-->', $editmode, $html);
	}

	/* 获取可以编辑的页面列表 */
	private function getEditablePages($client = 'pc')
	{
		$data = [];
		if (in_array($client, ['pc'])) {
			$siteUrl = Basewind::homeUrl();
			$data['index'] = [
				'title' => Language::get('index'),
				'url' => Url::toRoute(['diy/index'], $siteUrl),
				'route' => '/'
			];
			$data['sellerindex'] = [
				'title' => Language::get('sellerindex'),
				'url' => Url::toRoute(['diy/sellerindex'], $siteUrl),
				'route' => '/seller/index'
			];
		} else {
			$siteUrl = Basewind::mobileUrl();
			$data['index'] = [
				'title' => Language::get('index'),
				'url' => Url::toRoute(['diy/index'], $siteUrl),
				'route' => '/pages/index/index'
			];
			$data['community'] = [
				'title' => Language::get('community'),
				'url' => Url::toRoute(['diy/community'], $siteUrl),
				'route' => '/pages/community/index/index'
			];
		}

		return $this->getOtherPages($data, $client, $siteUrl);
	}

	/**
	 * 获取自定义页面
	 */
	private function getOtherPages($data, $client = 'pc', $siteUrl = '')
	{
		$dir = Yii::getAlias('@public/data/pagediy/' . ($client ? $client : 'pc'));
		if (!is_dir($dir)) return [];

		if ($list = FileHelper::findFiles($dir, ['recursive' => false, 'only' => ['*.config.php']])) {
			foreach ($list as $value) {
				if ($array = require($value)) {
					$page = $array['page'];
					if ($page && !in_array($page['id'], $data)) {
						$data[$page['id']] = [
							'title' => $page['title'],
							'url' => Url::toRoute(['diy/page', 'id' => $page['id']], $siteUrl),
							'route' => ($client == 'pc' ? '/channel/page/' : '/pages/channel/page?id=') . $page['id'],
							'action' => ['edit', 'delete'],
							'enabled' => $page['enabled']
						];
					}
				}
			}
		}

		return $data;
	}

	private function getClientParams()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true);
		if (!isset($post->client)) $post->client = 'pc';
		if (!isset($post->page)) $post->page = 'index';

		return array($post->client, Page::getTheme($post->client), $post->page);
	}

	/**
	 * 清除模板编辑上传图片后产生的无效图片
	 * @desc 包含PC及移动端
	 */
	public function actionClearfile()
	{
		// 清除的文件数
		$quantity = 0;

		// 配置文件内容集合
		$contents = '';

		$dir = Yii::getAlias('@public/data/pagediy');
		$list = FileHelper::findDirectories($dir, ['recursive' => false]);
		foreach ($list as $folder) {
			$files = FileHelper::findFiles($folder, ['recursive' => false]);
			foreach ($files as $file) {
				$contents .= file_get_contents($file);
			}
		}

		$preg = '/data\/files\/mall\/template\/[A-Za-z0-9]+(.jpg|.jpeg|.png|.gif|.bmp)/i';
		preg_match_all($preg, $contents, $configImageAll, 0);

		// 模板配置中的所有图片
		if (!isset($configImageAll[0]) || empty($configImageAll[0])) {
			return Message::warning(Language::get('clear_empty'));
		}

		// 模板配置上传过的所有图片
		$folder = Yii::getAlias('@public/data/files/mall/template');
		if (!is_dir($folder)) FileHelper::createDirectory($folder);
		$uploadImageAll = FileHelper::findFiles($folder, ['recursive' => false]);
		foreach ($uploadImageAll as $value) {
			$file = str_replace(DIRECTORY_SEPARATOR, '/', str_replace(Yii::getAlias('@public') . '/', '', $value));

			// 如果已上传的图片不在当前配置文件中，说明图片已失效，删除之
			if (!in_array($file, $configImageAll[0])) {
				unlink(Yii::getAlias('@public') . '/' . $file);
				$quantity++;
			}
		}
		if (!$quantity) {
			return Message::warning(Language::get('clear_empty'));
		}

		return Message::result(null, sprintf(Language::get('clear_ok'), $quantity));
	}
}
