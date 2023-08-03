<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\controllers\seller;

use Yii;

use common\library\Basewind;
use common\library\Page;

use frontend\api\library\Respond;

/**
 * @Id MealbuyController.php 2018.12.28 $
 * @author yxyc
 */

class MealbuyController extends \common\base\BaseApiController
{
	/**
	 * 获取搭配购列表
	 * @api 接口访问地址: http://api.xxx.com/seller/mealbuy/list
	 */
    public function actionList()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(false)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['goods_id', 'page', 'page_size']);
		$post->store_id = Yii::$app->user->id;

		$orderBy = [];
		if($post->orderby) {
			$orderBy = Basewind::trimAll(explode('|', $post->orderby));
			if(in_array($orderBy[0], array_keys($this->getOrders())) && in_array(strtolower($orderBy[1]), ['desc', 'asc'])) {
				$orderBy = [$orderBy[0] => strtolower($orderBy[1]) == 'asc' ? SORT_ASC : SORT_DESC];
			} 
		}

		$model = new \frontend\home\models\MealForm();
		list($list, $page) = $model->formData($post, isset($post->queryitem) ? (bool)$post->queryitem : true, $orderBy, true, $post->page_size, false, $post->page);
		return $respond->output(true, null, ['list' => $list, 'pagination' => Page::formatPage($page, false)]);
	}
}