<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {res} function plugin
 *
 * @author  shopwind <shopwind.net>
 * @version 1.0
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @throws SmartyException
 * @return string
 */

use common\models\StoreModel;
use common\library\Basewind;

/**
 * 除非有特殊指定，要不一律建议返回相对路径，可以解决多个域名访问的资源路径问题
 */
function smarty_function_res($params, $template)
{
	$client = Basewind::getCurrentApp();
	$baseUrl = isset($params['baseUrl']) ? $params['baseUrl'] : substr(Yii::$app->homeUrl, 0, -1);
	$folder = isset($params['folder']) ? $params['folder'] : '';

	// 设置默认值
	list($theme, $style) = ['default', 'default'];

	// 后台（有可能在后台会访问前台的文件）
	if($client == 'admin') {
		return $baseUrl . "/templates/" . ($folder ? "{$folder}/" : "") . "{$theme}/styles/{$style}/" . $params['file'];
	}

	// 前台和移动端
	if(in_array($client, ['pc', 'wap']))
	{
		// 安装程序的主题，暂时不支持多主题
		if($folder == 'install') {
			return $baseUrl . "/{$folder}/templates/{$theme}/styles/{$style}/" . $params['file'];
		}
		
		// 店铺主题
		if($folder == 'store') {
			$theme_fields = ($client != 'wap') ? 'theme' : 'wap_theme as theme';
			if(isset($params['id']) && ($store = StoreModel::find()->select($theme_fields)->where(['store_id' => intval($params['id'])])->one())) {
				list($theme, $style) = $store->theme ? explode('|', $store->theme) : ['default', 'default'];
			}
		}
		else
		{
			$template_fields = $client != 'wap' ? 'template_name' : 'wap_template_name';
			$style_fields = ($client != 'wap') ? 'style_name' : 'wap_style_name';
			if(isset(Yii::$app->params[$template_fields]) && !empty(Yii::$app->params[$template_fields])) {
				$theme = Yii::$app->params[$template_fields];
			}
			if(isset(Yii::$app->params[$style_fields]) && !empty(Yii::$app->params[$style_fields])) {
				$style = Yii::$app->params[$style_fields];
			}
		}
	}

	// 如果是编辑模式（当在后台进入模板编辑的时候，通过后台访问前台页面，必须是绝对路径）
	if(Yii::$app->request->get('editmode') && empty($params['baseUrl'])) {
		$baseUrl = $client == 'wap' ? Yii::$app->params['mobileUrl'] : Basewind::homeUrl();
	}
	$folder = $folder ? $folder : 'mall';
	return $baseUrl . "/templates/{$folder}/{$theme}/styles/{$style}/" . $params['file'];
}
