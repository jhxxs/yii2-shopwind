<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace backend\controllers;

use Yii;

use common\library\Basewind;
use common\library\Language;
use common\library\Message;
use common\library\Page;

/**
 * @Id ThemeController.php 2018.9.5 $
 * @author mosir
 */

class ThemeController extends \common\controllers\BaseAdminController
{
	/**
	 * 初始化
	 */
	public function init()
	{
		parent::init();
	}
	
	public function actionIndex()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true);

		$list = array();
		$templates = Page::listTemplate('mall', $post->client);
		foreach($templates as $template) {
			$list[$template] = Page::listStyle('mall', $post->client, $template);
		}
		$this->params['templates'] = $list;
		$this->params['cur_template_name'] = Yii::$app->params['template_name'];
		$this->params['cur_style_name'] = Yii::$app->params['style_name'];
		$this->params['webroot'] = Yii::$app->params['frontendUrl'];
		
		$this->params['page'] = Page::seo(['title' => Language::get('template_list')]);
		return $this->render('../theme.index.html', $this->params);
	}
	
	public function actionSet()
    {
		$post = Basewind::trimAll(Yii::$app->request->get(), true);
	
        if (!$post->template_name) {
			return Message::warning(Language::get('no_such_template'));
        }
        if (!$post->style_name) {
            return Message::warning(Language::get('no_such_style'));
        }
		
		if($post->client == 'wap'){
			$data = array('wap_template_name' => $post->template_name, 'wap_style_name' => $post->style_name);
		}
		else {
			$data = array('template_name' => $post->template_name, 'style_name' => $post->style_name);
		}
		
        $model = new \backend\models\SettingForm();
		if(!$model->save($data, true)) {
			return Message::warning($model->errors);
		}
		return Message::display(Language::get('set_theme_successed'), ['theme/index']);
    }
	
	public function actionPreview()
    {
		$post = Basewind::trimAll(Yii::$app->request->post(), true);
		
        if (!$post->template_name) {
			return Message::warning(Language::get('no_such_template'));
        }
        if (!$post->style_name) {
            return Message::warning(Language::get('no_such_style'));
        }
		$cl = ($post->client == 'wap') ? Yii::$app->params['mobileUrl'] : Yii::$app->params['frontendUrl'];
		return $this->redirect($cl.'/templates/mall/'.$post->template_name . '/styles/'.$post->style_name.'/screenshot.jpg');
    }
}
