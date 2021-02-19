<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use common\models\StoreModel;
use common\models\SgradeModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Message;
use common\library\Page;

/**
 * @Id My_themeController.php 2018.4.22 $
 * @author mosir
 */

class My_themeController extends \common\controllers\BaseSellerController
{
	/**
	 * 初始化
	 * @var array $view 当前视图
	 * @var array $params 传递给视图的公共参数
	 */
	public function init()
	{
		parent::init();
		$this->view  = Page::setView('mall');
		$this->params = ArrayHelper::merge($this->params, Page::getAssign('user'));
	}
	
    public function actionIndex()
    {
		// 获取当前所使用的风格
        if(!($store = StoreModel::find()->select('store_id,store_name,theme,sgrade')->where(['store_id' => $this->visitor['store_id']])->one())) {
			return Message::warning(Language::get('no_such_store'));
		}
		$theme = $store->theme ? $store->theme : 'default|default';
		list($curr_template_name, $curr_style_name) = explode('|', $theme);
		
		// 该店铺所有主题
		$themes = $this->getThemes($store);
		if(empty($themes)) {
			return Message::warning(Language::get('no_themes'));
		}
		
		$this->params['themes'] = $themes;
		$this->params['curr_template_name'] = $curr_template_name;
		$this->params['curr_style_name']	= $curr_style_name;
		$this->params['store'] = $store;
	
		// 当前位置
		$this->params['_curlocal'] = Page::setLocal(Language::get('my_theme'), Url::toRoute('my_theme/index'), Language::get('theme_list'));
		
		// 当前用户中心菜单
		$this->params['_usermenu'] = Page::setMenu('my_theme', 'theme_list');
		
		$this->params['page'] = Page::seo(['title' => Language::get('theme_list')]);
		return $this->render('../my_theme.index.html', $this->params);
	}
	
	public function actionUsed()
	{
		$post = Basewind::trimAll(Yii::$app->request->get(), true, [], ['template', 'style']);
		
		if(empty($post->template) || empty($post->style)) {
			return Message::warning(Language::get('no_such_theme'));
		}
		
		$themes = array();
		if(($store = StoreModel::find()->select('store_id,sgrade')->where(['store_id' => $this->visitor['store_id']])->one())) {
			$themes = $this->getThemes($store);
		}
		
		$theme = $post->template . '|' . $post->style;
		if(!isset($themes[$theme])) {
			return Message::warning(Language::get('no_such_theme'));
		}
		
		$store->theme = $theme;
		if(!$store->save()) {
			return Message::warning($store->errors);
		}
		return Message::display(Language::get('set_theme_successed'));
	}
	
	private function getThemes($store)
	{
		$themes = array();
		$sgrade = SgradeModel::find()->select('skins')->where(['grade_id' => $store->sgrade])->one();
		if($sgrade && $sgrade->skins) {
			foreach(explode(',', $sgrade->skins) as $skin) {
				list($template_name, $style_name) = explode('|', $skin);
				$themes[$skin] = ['template_name' => $template_name, 'style_name' => $style_name];
			}
		} else {
			$themes['default|default'] = ['template_name' => 'default', 'style_name' => 'default'];
		}
		
		return $themes;
	}
	
	
	/* 三级菜单 */
    public function getUserSubmenu()
    {
        $submenus =  array(
            array(
                'name'  => 'theme_list',
                'url'   => Url::toRoute('my_theme/index'),
            ),
        );

        return $submenus;
    }
}