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
use common\library\Taskqueue;

use frontend\api\library\Respond;

/**
 * @Id OrderController.php 2019.11.20 $
 * @author yxyc
 */

class OrderController extends \common\base\BaseApiController
{
	public function init()
	{
		parent::init();
		Taskqueue::run();
	}
	
	/**
	 * 获取订单管理列表数据
	 * @api 接口访问地址: http://api.xxx.com/seller/order/list
	 */
    public function actionList()
	{
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['buyer_id', 'page', 'page_size']);
		$model = new \frontend\api\models\OrderForm(['enter' => 'seller']);
		list($list, $page) = $model->formData($post);
		
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];
		return $respond->output(true, null, $this->params);
	}

	/**
	 * 获取卖家订单提醒数据
	 * @api 接口访问地址: http://api.xxx.com/seller/order/remind
	 */
    public function actionRemind()
	{
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		$model = new \frontend\home\models\UserForm();
	
		return $respond->output(true, null, $model->getSellerStat());
	}

	/** 
	 * 获取我管理的订单评价列表
	 * @api 接口访问地址: http://api.xxx.com/seller/order/evaluates
	 */
    public function actionEvaluates()
	{
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);

		$model = new \frontend\home\models\Seller_orderEvaluateForm();
		list($list, $page) = $model->getEvaluateList($post);
		
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];
		return $respond->output(true, null, $this->params);
	}
}