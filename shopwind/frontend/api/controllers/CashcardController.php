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
use common\library\Page;

use frontend\api\library\Respond;

/**
 * @Id CashcardController.php 2018.10.15 $
 * @author yxyc
 */

class CashcardController extends \common\base\BaseApiController
{
	/**
	 * 获取充值卡列表
	 * @api 接口访问地址: https://www.xxx.com/api/cashcard/list
	 */
    public function actionList()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['page', 'page_size']);
		
		$model = new \frontend\home\models\My_cashcardForm();
		list($list, $page) = $model->formData($post, $post->page_size, false, $post->page);
		$this->params = ['list' => $list, 'pagination' => Page::formatPage($page, false)];

		return $respond->output(true, null, $this->params);
	}
	
	/**
	 * 绑定充值卡并充值到余额
	 * @api 接口访问地址: https://www.xxx.com/api/cashcard/bind
	 */
    public function actionBind()
    {
		// 验证签名
		 $respond = new Respond();
		if(!$respond->verify(true)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);
		
		$model = new \frontend\home\models\DepositCardrechargeForm();
		if(!$model->submit($post)) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}
		
		return $respond->output(true, null, ['tradeNo' => $model->tradeNo]);
	}
}