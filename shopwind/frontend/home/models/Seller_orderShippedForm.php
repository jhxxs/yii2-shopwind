<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\home\models;

use Yii;
use yii\base\Model;

use common\models\OrderModel;
use common\models\DepositTradeModel;
use common\models\OrderLogModel;
use common\models\OrderExpressModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Def;
use common\library\Plugin;

/**
 * @Id Seller_orderShippedForm.php 2018.9.19 $
 * @author mosir
 */
class Seller_orderShippedForm extends Model
{
	public $store_id = 0;
	public $errors = null;

	/**
	 * 卖家发货
	 */
	public function submit($post = null, $orderInfo = [], $sendNotify = true)
	{
		if ($post->code != 'noneed' && !$post->number) {
			$this->errors = Language::get('express_no_empty');
			return false;
		}

		$model = OrderModel::findOne($orderInfo['order_id']);
		$model->status = Def::ORDER_SHIPPED;

		if (!$model->ship_time) {
			$model->ship_time = Timezone::gmtime();
		}

		if (!$model->save()) {
			$this->errors = $model->errors;
			return false;
		}

		if ($post->code != 'noneed') {

			// 如果需要支持多条快递单，则拓展此
			if (!($express = OrderExpressModel::find()->where(['order_id' => $model->order_id])->one())) {
				$express = new OrderExpressModel();
				$express->order_id = $model->order_id;
			}

			// 取得一个可用的快递跟踪插件
			if (($expresser = Plugin::getInstance('express')->autoBuild())) {
				if (!$post->code) {
					$this->errors = Language::get('express_company_empty');
					return false;
				}
				//$express->express_code = $expresser->getCode();
				$express->company = $expresser->getCompanyName($post->code);
			}

			$express->code = $post->code;
			$express->number = $post->number;
			if (!$express->save()) {
				$this->errors = $model->errors;
				return false;
			}
		}

		DepositTradeModel::updateAll(['status' => 'SHIPPED'], ['bizOrderId' => $orderInfo['order_sn'], 'bizIdentity' => Def::TRADE_ORDER, 'seller_id' => $orderInfo['seller_id']]);

		// 记录订单操作日志
		OrderLogModel::create($orderInfo['order_id'], Def::ORDER_SHIPPED, addslashes(Yii::$app->user->identity->username), $post->remark);

		if ($sendNotify === true) {
			// 短信和邮件提醒： 卖家已发货通知买家
			Basewind::sendMailMsgNotify(
				array_merge($orderInfo, ['express_no' => $post->number]),
				array(
					'key' 		=> 'tobuyer_shipped_notify',
					'receiver' 	=> $orderInfo['buyer_id']
				),
				array(
					'key' 		=> 'tobuyer_shipped_notify',
					'receiver'  => $orderInfo['phone_mob'], // 收货人的手机号
				)
			);
		}
		return true;
	}
}
