<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\api\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\OrderModel;
use common\models\OrderGoodsModel;
use common\models\RefundModel;
use common\models\DepositTradeModel;
use common\models\GoodsStatisticsModel;
use common\models\OrderExtmModel;
use common\models\OrderLogModel;
use common\models\GuideshopModel;
use common\models\RegionModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Page;
use common\library\Def;

use frontend\api\library\Formatter;

/**
 * @Id OrderForm.php 2019.1.3 $
 * @author yxyc
 */
class OrderForm extends Model
{
	public $enter = 'buyer';
	public $errors = null;

	/**
	 * 获取订单数据
	 */
	public function formData($post = null)
	{
		$query = OrderModel::find()->alias('o')
			->select('o.order_id,o.order_sn,o.gtype,o.otype,o.buyer_id,o.buyer_name,o.seller_id,o.seller_name,o.status,o.evaluation_status,o.payment_code,o.payment_name,o.goods_amount,o.order_amount,o.postscript,o.memo,o.add_time,o.pay_time,o.ship_time,o.finished_time,o.evaluation_time,o.guider_id,oe.freight')
			->joinWith('orderExtm oe', false)
			->where(['>', 'o.order_id', 0])
			->orderBy(['o.order_id' => SORT_DESC]);

		// 指定获取某个店铺的订单
		if ($post->store_id) {
			$query->andWhere(['o.seller_id' => $post->store_id]);
		}

		if ($post->otype) {
			$query->andWhere(['otype' => $post->otype]);
		}
		if ($post->gtype) {
			$query->andWhere(['gtype' => $post->gtype]);
		}

		// 卖家获取订单管理数据
		if ($this->enter == 'seller') {
			$query->andWhere(['o.seller_id' => Yii::$app->user->id]);
			$query->addSelect('oe.consignee,oe.phone_mob,region_id,address,phone_tel,deliveryName');
		}
		// 团长获取订单管理数据
		elseif ($this->enter == 'guider') {
			$query->andWhere(['o.guider_id' => Yii::$app->user->id]);
			$query->addSelect('oe.consignee,oe.phone_mob');
		}
		// 买家获取我的订单数据
		else {
			$query->andWhere(['o.buyer_id' => Yii::$app->user->id]);
		}
		// 根据订单状态筛选订单
		if (isset($post->type) && ($status = Def::getOrderStatusTranslator($post->type)) > -1) {
			// 待收货包含（已发货，待配送，待取货，待使用）
			if ($status == Def::ORDER_SHIPPED) {
				$query->andWhere(['in', 'o.status', [Def::ORDER_SHIPPED, Def::ORDER_PICKING, Def::ORDER_DELIVERED, Def::ORDER_USING]]);
			} else $query->andWhere(['o.status' => $status]);
		}
		// 根据评价状态筛选
		if (isset($post->evaluation_status) && $post->evaluation_status != '' && $post->evaluation_status != null) {
			$query->andWhere(['evaluation_status' => intval($post->evaluation_status)]);
		}

		// 获取指定的时间段的订单
		if ($post->begin) {
			$query->andWhere(['>=', 'o.add_time', Timezone::gmstr2time($post->begin)]);
		}
		if ($post->end) {
			$query->andWhere(['<=', 'o.add_time', Timezone::gmstr2time($post->end)]);
		}
		// 根据订单编号筛选
		if ($post->order_sn) {
			$query->andWhere(['o.order_sn' => $post->order_sn]);
		}
		// 根据买家用户名筛选
		if ($post->buyer_name) {
			$query->andWhere(['o.buyer_name' => $post->buyer_name]);
		}
		// 根据收货人姓名或手机号筛选
		if ($post->consignee) {
			$query->andWhere(['or', ['oe.consignee' => $post->consignee], ['oe.phone_mob' => $post->consignee]]);
		}
		// 根据商品名称
		if ($post->keyword) {
			$allId = OrderGoodsModel::find()->select('order_id')->where(['like', 'goods_name', $post->keyword])->column();
			$query->andWhere(['in', 'o.order_id', $allId]);
		}

		// 是否获取订单商品数据
		if (isset($post->queryitem) && ($post->queryitem === true)) {
			$query->with(['orderGoods' => function ($model) {
				$model->select('rec_id,spec_id,order_id,goods_id,goods_name,goods_image,specification,price,quantity');
			}]);
		}

		$page = Page::getPage($query->count(), $post->page_size, false, $post->page);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		foreach ($list as $key => $value) {
			$list[$key] = $this->formatDate($value);

			if (($trade = DepositTradeModel::find()->select('tradeNo,bizOrderId,bizIdentity')->where(['bizOrderId' => $value['order_sn'], 'bizIdentity' => Def::TRADE_ORDER])->asArray()->one())) {
				$list[$key] = array_merge($list[$key], $trade);
			}

			// 查看是否有退款
			if (($refund = $this->getOrderRefund($value))) {
				$list[$key] = array_merge($list[$key], $refund);
			}
			if (isset($value['orderGoods'])) {
				$list[$key]['items'] = $this->formatImage($value['orderGoods']);
				unset($list[$key]['orderGoods']);
			}

			// 对卖家订单和团长订单返回收（取）货人信息
			if (in_array($this->enter, ['seller', 'guider'])) {
				$array = RegionModel::getArray($value['region_id']);
				$shipping = array_merge([
					'name' => $value['consignee'],
					'phone_mob' => $value['phone_mob'] ? $value['phone_mob'] : $value['phone_tel'],
					'address' => $value['address']
				], $array ? $array : []);
				unset($list[$key]['phone_mob'], $list[$key]['phone_tel'], $list[$key]['address']);
				$list[$key]['consignee'] = $shipping;
			}
		}

		return array($list, $page);
	}

	/**
	 * 取消订单
	 * @desc 适用买家或卖家取消订单
	 */
	public function orderCancel($post, $orderInfo = [])
	{
		// 只有待付款且未发货（针对货到付款）的订单才可以取消
		if (!($orderInfo['status'] == Def::ORDER_PENDING && !$orderInfo['ship_time'])) {
			$this->errors = Language::get('unsupport_status');
			return false;
		}

		if ($orderInfo['buyer_id'] == Yii::$app->user->id) {
			$model = new \frontend\home\models\Buyer_orderCancelForm();
		} else {
			$model = new \frontend\home\models\Seller_orderCancelForm();
		}

		$orders = array($orderInfo['order_id'] => $orderInfo);
		return $model->submit($post, $orders, false);
	}

	/**
	 * 卖家发货
	 */
	public function orderShipped($post, $orderInfo = [])
	{
		if ($orderInfo['seller_id'] != Yii::$app->user->id) {
			$this->errors = Language::get('handle_invalid');
			return false;
		}
		// 如果订单状态不是待发货 & 已发货，则不允许发货操作
		if (!in_array($orderInfo['status'], [Def::ORDER_ACCEPTED, Def::ORDER_SHIPPED])) {
			$this->errors = Language::get('unsupport_status');
			return false;
		}
		$model = new \frontend\home\models\Seller_orderShippedForm();
		if (!$model->submit($post, $orderInfo, false)) {
			$this->errors = $model->errors;
			return false;
		}
		return true;
	}

	/**
	 * 团长通知取货（针对社区团购订单）
	 */
	public function orderDelivered($post, $orderInfo = [])
	{
		// 交易信息 
		if (!($tradeInfo = DepositTradeModel::find()->where(['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $orderInfo['order_sn']])->asArray()->one())) {
			$this->errors = Language::get('no_such_order');
			return false;
		}
		OrderModel::updateAll(['status' => Def::ORDER_DELIVERED, 'ship_time' => Timezone::gmtime()], ['order_id' => $orderInfo['order_id'], 'guider_id' => Yii::$app->user->id]);

		// 记录订单操作日志
		OrderLogModel::create($orderInfo['order_id'], Def::ORDER_DELIVERED);

		// 短信/邮件提醒： 可以取货通知买家
		if ($receiver = OrderExtmModel::find()->select('phone_mob')->where(['order_id' => $orderInfo['order_id']])->scalar()) {
			$orderInfo['shop_name'] = GuideshopModel::find()->select('name as shop_name')->where(['userid' => Yii::$app->user->id])->scalar();
			Basewind::sendMailMsgNotify(
				$orderInfo,
				['key' => 'tobuyer_deliver_notify'],
				['key' => 'tobuyer_deliver_notify', 'receiver' => $receiver]
			);
		}

		return true;
	}

	/**
	 * 买家确认收货
	 * 不包含货到付款的收货操作
	 */
	public function orderFinished($post, $orderInfo = [])
	{
		// 交易信息 
		if (!($tradeInfo = DepositTradeModel::find()->where(['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $orderInfo['order_sn'], 'buyer_id' => Yii::$app->user->id])->asArray()->one())) {
			$this->errors = Language::get('no_such_order');
			return false;
		}

		// 如果不是已发货，则不可以确认收货
		if (!$orderInfo['ship_time']) {
			$this->errors = Language::get('unsupport_status');
			return false;
		}

		$model = new \frontend\home\models\Buyer_orderConfirmForm();
		if (!$model->submit($post, $orderInfo, $tradeInfo, false)) {
			$this->errors = $model->errors;
			return false;
		}
		return true;
	}

	/**
	 * 卖家核销订单（针对虚拟商品订单），交易完成
	 */
	public function orderVerused($post, $orderInfo = [])
	{
		// 更新订单状态 
		$model = OrderModel::findOne($orderInfo['order_id']);
		$model->status = Def::ORDER_FINISHED;
		$model->ship_time = Timezone::gmtime();
		$model->receive_time = Timezone::gmtime();
		$model->finished_time = Timezone::gmtime();
		if (!$model->save()) {
			$this->errors = $model->errors;
			return false;
		}

		// 记录订单操作日志 
		OrderLogModel::create($model->order_id, Def::ORDER_FINISHED, '', Language::get('verused_confirm'));

		// 转到对应的业务实例，不同的业务实例用不同的文件处理，如购物，卖出商品，充值，提现等，每个业务实例又继承支出或者收入 
		$depopay_type = \common\library\Business::getInstance('depopay')->build('sellgoods', $post);

		$result = $depopay_type->submit(array(
			'trade_info' => array('userid' => $model->seller_id, 'party_id' => $model->buyer_id, 'amount' => $model->order_amount),
			'extra_info' => ArrayHelper::toArray($model) + ['tradeNo' => DepositTradeModel::find()->select('tradeNo')->where(['bizOrderId' => $model->order_sn, 'bizIdentity' => Def::TRADE_ORDER])->scalar()]
		));

		if (!$result) {
			$this->errors = $depopay_type->errors;
			return false;
		}

		// 更新累计销售件数
		$ordergoods = OrderGoodsModel::find()->select('rec_id,goods_id,quantity')->where(['order_id' => $model->order_id])->asArray()->all();
		foreach ($ordergoods as $key => $goods) {
			GoodsStatisticsModel::updateStatistics($goods['goods_id'], 'sales', $goods['quantity']);
		}

		return true;
	}

	/**
	 * 买家确认收货
	 * 针对货到付款情形，将订单状态修改为 待支付
	 */
	public function orderConfirm($post, $orderInfo = [])
	{
		// 如果不是已发货，则不可以确认收货
		if (!$orderInfo['ship_time']) {
			$this->errors = Language::get('unsupport_status');
			return false;
		}

		// 修改订单状态
		OrderModel::updateAll(['status' => $post->status, 'receive_time' => Timezone::gmtime()], ['order_id' => $orderInfo['order_id']]);

		// 修改交易状态
		DepositTradeModel::updateAll(['status' => 'PENDING'], ['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $orderInfo['order_sn'], 'buyer_id' => Yii::$app->user->id]);

		// 记录订单操作日志
		OrderLogModel::create($orderInfo['order_id'], Language::get('order_received'));
		OrderLogModel::create($orderInfo['order_id'], $post->status, addslashes(Yii::$app->user->identity->username), Language::get('buyer_confirm'));

		return true;
	}

	/**
	 * 格式化时间
	 */
	public function formatDate($record)
	{
		$fields = ['add_time', 'pay_time', 'ship_time', 'receive_time', 'finished_time', 'evaluation_time'];
		foreach ($fields as $field) {
			isset($record[$field]) && $record[$field] = Timezone::localDate('Y-m-d H:i:s', $record[$field]);
		}
		return $record;
	}

	/**
	 * 格式化路径
	 */
	public function formatImage($list)
	{
		foreach ($list as $key => $value) {
			if (isset($list[$key]['goods_image'])) {
				$list[$key]['goods_image'] = Formatter::path($value['goods_image'], 'goods');
			}
		}
		return $list;
	}

	/**
	 * 获取订单是否有退款
	 */
	private function getOrderRefund($order = [])
	{
		// 是否申请过退款
		$tradeNo = DepositTradeModel::find()->select('tradeNo')->where(['bizIdentity' => Def::TRADE_ORDER, 'bizOrderId' => $order['order_sn']])->scalar();

		if (!empty($tradeNo) && ($refund = RefundModel::find()->select('refund_id,refund_sn,status as refund_status')->where(['tradeNo' => $tradeNo])->asArray()->one())) {
			return $refund;
		}

		return false;
	}
}
