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
use yii\helpers\ArrayHelper;

use common\models\OrderModel;
use common\models\OrderGoodsModel;
use common\models\OrderExtmModel;
use common\models\DepositTradeModel;
use common\models\RefundModel;
use common\models\RegionModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Business;
use common\library\Plugin;
use common\library\Page;
use common\library\Def;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id OrderController.php 2019.11.20 $
 * @author yxyc
 */

class OrderController extends \common\base\BaseApiController
{
	/**
	 * 获取所有订单列表数据
	 * @api 接口访问地址: http://api.xxx.com/order/list
	 */
	public function actionList()
	{
		// TODO
	}

	/**
	 * 获取单条订单信息
	 * @api 接口访问地址: http://api.xxx.com/order/read
	 */
	public function actionRead()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		$query = OrderModel::find()->alias('o')->select('o.order_id,o.order_sn,o.gtype,o.otype,o.buyer_id,o.buyer_name,o.seller_id,o.seller_name,o.status,o.evaluation_status,o.payment_code,o.payment_name,o.express_no,o.express_company,o.goods_amount,o.order_amount,o.postscript,o.memo,o.add_time,o.pay_time,o.ship_time,o.receive_time,o.finished_time,o.evaluation_time,o.guider_id,oe.shipping_fee')
			->joinWith('orderExtm oe', false)->where(['or', ['buyer_id' => Yii::$app->user->id], ['seller_id' => Yii::$app->user->id]])
			->andWhere(['o.order_id' => $post->order_id]);

		if (!($record = $query->asArray()->one())) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_such_order'));
		}
		if (($trade = DepositTradeModel::find()->select('tradeNo,bizOrderId,bizIdentity,payType')->where(['bizOrderId' => $record['order_sn'], 'bizIdentity' => Def::TRADE_ORDER])->asArray()->one())) {
			$record = array_merge($record, $trade);
			if (($refund = RefundModel::find()->select('refund_sn,status as refund_status')->where(['tradeNo' => $trade['tradeNo']])->andWhere(['!=', 'status', 'CLOSED'])->asArray()->one())) {
				$record = array_merge($record, $refund);
			}
		}
		$model = new \frontend\api\models\OrderForm();
		$record = $model->formatDate($record);

		return $respond->output(true, null, $record);
	}

	/**
	 * 提交预支付购物订单
	 * @api 接口访问地址: http://api.xxx.com/order/create
	 */
	public function actionCreate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true);

		// 购物车/搭配套餐/拼团订单/社区团购订单
		if (!isset($post->otype) || !in_array($post->otype, ['normal', 'mealbuy', 'teambuy', 'guidebuy'])) {
			$post->otype = 'normal';
		}

		$model = new \frontend\home\models\OrderForm(['otype' => $post->otype]);
		if (($goods_info = $model->getGoodsInfo($post)) === false) {
			return $respond->output(Respond::RECORD_NOTEXIST, $model->errors);
		}

		// 不能同时结算实物商品和虚拟服务类商品
		if (count($goods_info['gtype']) > 1) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('goodstype_invalid'));
		}

		// 如果是自己店铺的商品，则不能购买
		if (in_array(Yii::$app->user->id, $goods_info['storeIds'])) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('can_not_buy_yourself'));
		}

		// 获取订单模型
		$order_type = Business::getInstance('order')->build($post->otype, $post);
		$result = $order_type->submit(array(
			'goods_info' => $goods_info
		));
		if (empty($result)) {
			return $respond->output(Respond::PARAMS_INVALID, $order_type->errors);
		}

		// 清理购物车商品等操作
		foreach ($result as $store_id => $order_id) {
			$order_type->afterInsertOrder($order_id,  $store_id, $goods_info['orderList'][$store_id]);
		}

		// 有可能是支付多个订单
		$bizOrderId = implode(',', OrderModel::find()->select('order_sn')->where(['in', 'order_id', array_values($result)])->column());

		// 到收银台付款
		return $respond->output(true, null, ['bizOrderId' => $bizOrderId, 'bizIdentity' => Def::TRADE_ORDER]);
	}

	/**
	 * 更新订单状态
	 * @api 接口访问地址: http://api.xxx.com/order/update
	 */
	public function actionUpdate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id', 'status']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		$query = OrderModel::find()->where(['or', ['buyer_id' => Yii::$app->user->id], ['seller_id' => Yii::$app->user->id]])->andWhere(['order_id' => $post->order_id]);
		if (!($record = $query->one())) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_such_order'));
		}

		// 只接受目标为：取消订单/发货/确认收货/待付款（必须是货到付款）的状态变更
		if (!isset($post->status) || !in_array($post->status, [Def::ORDER_CANCELED, Def::ORDER_SHIPPED, Def::ORDER_FINISHED, Def::ORDER_PENDING])) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('unsupport_status'));
		}

		// 如果目标为待付款，则必须是货到付款的订单
		if ($post->status == Def::ORDER_PENDING && $record->payment_code != 'cod') {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('unsupport_status'));
		}

		$model = new \frontend\api\models\OrderForm();

		// 取消订单
		if ($post->status == Def::ORDER_CANCELED) {
			if (!$model->orderCancel($post, ArrayHelper::toArray($record))) {
				return $respond->output(Respond::HANDLE_INVALID, Language::get('handle_fail'));
			}
		}

		// 卖家发货
		if ($post->status == Def::ORDER_SHIPPED) {
			if (!$model->orderShipped($post, ArrayHelper::toArray($record))) {
				return $respond->output(Respond::HANDLE_INVALID, $model->errors);
			}
		}

		// 买家确认收货[完成交易]
		if ($post->status == Def::ORDER_FINISHED) {
			if (!$model->orderFinished($post, ArrayHelper::toArray($record))) {
				return $respond->output(Respond::HANDLE_INVALID, $model->errors);
			}
		}

		// 买家确认收货（针对货到付款，买家确实收货后，下一步是 待付款）
		if ($post->status == Def::ORDER_PENDING) {
			if (!$model->orderConfirm($post, ArrayHelper::toArray($record))) {
				return $respond->output(Respond::HANDLE_INVALID, $model->errors);
			}
		}

		$result = OrderModel::find()
			->select('buyer_id,seller_id,status,add_time,pay_time,ship_time,express_no,receive_time,finished_time')
			->where(['order_id' => $post->order_id])
			->asArray()->one();
		return $respond->output(true, null, $result);
	}

	/**
	 * 获取预提交订单数据集合
	 * @api 接口访问地址: http://api.xxx.com/order/build
	 */
	public function actionBuild()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['store_id']);

		// 购物车/搭配购/拼团订单/社区团购订单
		if (!in_array($post->otype, ['normal', 'mealbuy', 'teambuy', 'guidebuy'])) {
			$post->otype == 'normal';
		}

		return $this->build($respond, $post->otype, $post);
	}

	/**
	 * 获取订单商品数据
	 * @api 接口访问地址: http://api.xxx.com/order/goods
	 */
	public function actionGoods()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		$list = OrderGoodsModel::find()->select('rec_id,goods_id,spec_id,goods_name,goods_image,price,quantity,specification,comment,evaluation,o.order_id,o.order_sn,o.buyer_id,o.seller_id')
			->joinWith('order o', false)->where(['or', ['buyer_id' => Yii::$app->user->id], ['seller_id' => Yii::$app->user->id]])
			->andWhere(['o.order_id' => $post->order_id])->asArray()->all();

		foreach ($list as $key => $value) {
			$list[$key]['subtotal'] = sprintf('%.2f', round($value['price'] * $value['quantity'], 2));
			$list[$key]['goods_image'] = Formatter::path($value['goods_image'], 'goods');
		}

		return $respond->output(true, null, ['list' => $list]);
	}

	/**
	 * 获取订单收货人数据
	 * @api 接口访问地址: http://api.xxx.com/order/extm
	 */
	public function actionExtm()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		$order = OrderModel::find()->select('order_id,order_sn,buyer_id,seller_id')
			->where(['or', ['buyer_id' => Yii::$app->user->id], ['seller_id' => Yii::$app->user->id]])
			->andWhere(['order_id' => $post->order_id])->asArray()->one();

		if (!$order) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_such_order'));
		}

		$extm = OrderExtmModel::find()->select('consignee,region_id,region_name,address,zipcode,phone_tel,phone_mob')
			->where(['order_id' => $post->order_id])->asArray()->one();

		if (!$extm) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_order_extm'));
		}

		$this->params = array_merge($order, $extm, RegionModel::getArrayRegion($extm['region_id'], $extm['region_name']));
		unset($this->params['region_name']);

		return $respond->output(true, null, $this->params);
	}

	/** 
	 * 对订单的评价
	 * @api 接口访问地址: http://api.xxx.com/order/evaluate
	 */
	public function actionEvaluate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		$model = new \frontend\home\models\Buyer_orderEvaluateForm();
		if (!($orderInfo = $model->formData($post))) {
			return $respond->output(Respond::PARAMS_INVALID, $model->errors);
		}

		if (!$model->submit(ArrayHelper::toArray($post), $orderInfo)) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true);
	}

	/** 
	 * 对买家的评价回复
	 * @api 接口访问地址: http://api.xxx.com/order/replyevaluate
	 */
	public function actionReplyevaluate()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['spec_id', 'order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		$model = new \frontend\home\models\Seller_orderEvaluateForm();
		if (!($result = $model->submit($post))) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true, null, $result);
	}

	/**
	 * 获取订单物流跟踪数据
	 * @api 接口访问地址: http://api.xxx.com/order/logistic
	 */
	public function actionLogistic()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		if (!($order = OrderModel::find()->select('order_id,order_sn,express_code,express_no,express_comkey,express_company,buyer_id,seller_id')->where(['and', ['order_id' => $post->order_id], ['>', 'ship_time', 0]])->andWhere(['or', ['buyer_id' => Yii::$app->user->id], ['seller_id' => Yii::$app->user->id]])->one())) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_such_order'));
		}

		// 每个订单发货用的快递插件会有不同
		$model = Plugin::getInstance('express')->build($order->express_code);
		if (!$model->isInstall(null, true)) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('no_such_express_plugin'));
		}

		if (($result = $model->submit($post, $order)) === false) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}

		return $respond->output(true, null, $result);
	}

	/**
	 * 获取订单核验码（目前仅有虚拟商品订单）
	 */
	public function actionQrcode()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		if (!OrderModel::find()->where(['order_id' => $post->order_id])->exists()) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_such_order'));
		}

		$url = Basewind::mobileUrl(true) . '/pages/trade/order/writeoff?id=' . $post->order_id;
		list($fileurl) = Page::generateQRCode('qrcode/order/', ['text' => $url, 'size' => 200]);
		return $respond->output(true, null, ['qrcode' => $fileurl]);
	}

	/**
	 * 服务商品订单核销（卖家扫码）
	 */
	public function actionWriteoff()
	{
		// 验证签名
		$respond = new Respond();
		if (!$respond->verify(true)) {
			return $respond->output(false);
		}

		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['order_id']);
		$post->order_id = $this->getOrderId($post);

		if (!$post->order_id) {
			return $respond->output(Respond::PARAMS_INVALID, Language::get('order_id_sn_empty'));
		}

		// 必须是服务商品订单
		if (!($record = OrderModel::find()->select('order_id,buyer_id,seller_id,status')->where(['and', ['order_id' => $post->order_id], ['>', 'pay_time', 0], ['gtype' => 'service']])->asArray()->one())) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_such_order'));
		}

		if ($record['seller_id'] != Yii::$app->user->id) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('writeoff_invalid'));
		}

		// 未核销
		if ($record['status'] == Def::ORDER_ACCEPTED) {
			$model = new \frontend\api\models\OrderForm();
			if (!$model->orderWriteoff($post, $record)) {
				return $respond->output(Respond::HANDLE_INVALID, $model->errors);
			}

			$record = OrderModel::find()
				->select('order_id,buyer_id,seller_id,status')
				->where(['order_id' => $post->order_id])
				->asArray()->one();
		}

		return $respond->output(true, null, $record);
	}

	/**
	 * 从具体实例获取预支付订单数据
	 * @param string $otype = 'normal' 取购物车商品(可能包含多个店铺的商品)
	 * 				 $otype = 'mealbuy' 取搭配套餐商品(只会有一个店铺的商品)
	 * 				 $otype = 'teambuy' 拼团商品(只会有一个店铺且一个商品)
	 * 				 $otype = 'guidebuy' 社区团购(可能包含多个店铺的商品)
	 */
	public function build($respond, $otype = 'normal', $post = null)
	{
		$model = new \frontend\home\models\OrderForm(['otype' => $otype]);
		if (($goods_info = $model->getGoodsInfo($post)) === false) {
			return $respond->output(Respond::RECORD_NOTEXIST, $model->errors);
		}

		// 如果是自己店铺的商品，则不能购买
		if (in_array(Yii::$app->user->id, $goods_info['storeIds'])) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('can_not_buy_yourself'));
		}

		// 获取订单模型
		$order_type = Business::getInstance('order')->build($otype, $post);

		// 获取表单数据
		if (($form = $order_type->formData($goods_info)) === false) {
			return $respond->output(Respond::RECORD_NOTEXIST, $order_type->errors);
		}

		$list = array_merge(['list' => $goods_info], $form);
		return $respond->output(true, null, $list);
	}

	private function getOrderId($post)
	{
		if (isset($post->order_id)) {
			return $post->order_id;
		}

		if (isset($post->order_sn) && !empty($post->order_sn)) {
			return OrderModel::find()->select('order_id')->where(['order_sn' => $post->order_sn])->scalar();
		}

		return 0;
	}
}
