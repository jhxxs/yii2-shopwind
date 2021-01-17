<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace common\business;

use yii;

use common\models\OrderModel;
use common\models\OrderGoodsModel;
use common\models\OrderExtmModel;
use common\models\CartModel;
use common\models\GoodsModel;
use common\models\GoodsStatisticsModel;
use common\models\AddressModel;
use common\models\DeliveryTemplateModel;
use common\models\CouponModel;
use common\models\CouponsnModel;
use common\models\IntegralModel;
use common\models\OrderIntegralModel;
use common\models\DistributeSettingModel;

use common\library\Basewind;
use common\library\Def;
use common\library\Timezone;
use common\library\Language;
use common\library\Promotool;

/**
 * @Id order.base.php 2018.7.12 $
 * @author   mosir
 * @desc The base Library of order
 */
 
class BaseOrder
{
	public $errors = null;
	
    public function __construct($params)
    {
        if (!empty($params))
        {
            foreach ($params as $key => $value)
            {
                $this->$key = $value;
            }
		}
    }
	/* 提交订单信息 */
	public function submit($data = array()) {
		return 0;
	}
	
	/* 插入订单表数据 */
	public function insertOrder($order_info = array())
	{
		$model = new OrderModel();
		foreach($order_info as $key => $val) {
			$model->$key = $val;
		}
		return $model->save() ? $model->order_id : 0;
	}
	
	/* 插入商品信息 */ 
	public function insertOrderGoods($order_id = 0, $list = array()) 
	{
		// 查验是否有邀请成交的商品
		if(Yii::$app->session->get('invite')) {
			$list = DistributeSettingModel::orderInvite($list);
		}
		
		foreach($list['items'] as $value) 
		{
			$model = new OrderGoodsModel();
			$model->order_id = $order_id;
			$model->goods_id = $value['goods_id'];
			$model->goods_name = $value['goods_name'];
			$model->spec_id = $value['spec_id'];
			$model->specification = $value['specification'] ? $value['specification'] : '';
			$model->price = $value['price'];
			$model->quantity = $value['quantity'];
			$model->goods_image = $value['goods_image'] ? $value['goods_image'] : '';
			
			if(!empty($value['invite'])) {
				$model->inviteType = $value['invite']['type'];
				$model->inviteRatio = serialize($value['invite']['ratio']);
				$model->inviteUid  = $value['invite']['uid'];
			}
			$model->save();
		}
		return true;
	}
	
	/* 插入合并付款提交订单过程中，如有插入失败的订单，则删除批次所有订单 */
	public function deleteOrderData($result = array())
	{
		foreach($result as $order_id)
		{
			if(!$order_id) continue;
			OrderModel::deleteAll(['order_id' => $order_id]);
			OrderGoodsModel::deleteAll(['order_id' => $order_id]);
			OrderExtmModel::deleteAll(['order_id' => $order_id]);
		}
	}
	
	/* 插入收货人信息 */
	public function insertOrderExtm($order_id = 0, $consignee_info  = array())
	{
		$model = new OrderExtmModel();
		
		$model->order_id = $order_id;
		foreach($consignee_info as $key => $val) {
			$model->$key = $val;
		}
		return $model->save();
	}
	
	/* 处理订单基本信息，返回有效的订单信息数组 */
    public function handleOrderInfo($goods_info = array(), $post = array())
    {
        // 返回基本信息
		$result = array();
		
		foreach($goods_info['orderList'] as $store_id => $order)
		{
        	$result[$store_id] = array(
				'order_sn'      =>  OrderModel::genOrderSn($store_id),
				'otype'         =>  $goods_info['otype'],
				'gtype'    		=>  $goods_info['gtype'],
				'seller_id'    	=>  $order['store_id'],
				'seller_name'   =>  addslashes($order['store_name']),
				'buyer_id'      =>  Yii::$app->user->id,
				'buyer_name'    =>  addslashes(Yii::$app->user->identity->username),
				'buyer_email'   =>  Yii::$app->user->identity->email ? Yii::$app->user->identity->email : '',
				'status'       	=>  Def::ORDER_PENDING,
				'add_time'      =>  Timezone::gmtime(),
				'goods_amount'  =>  $order['amount'],
				'anonymous'     =>  isset($post['anonymous'][$store_id]) ? intval($post['anonymous'][$store_id]) : 0,
				'postscript'   	=>  trim($post['postscript'][$store_id]),
			);
		}
		if(empty($result)) {
			$this->errors = Language::get('base_info_check_fail');
			return false;
		}
		return $result;
    }
	
	/* 处理收货人信息，返回有效的收货人信息 */
    public function handleConsigneeInfo($goods_info = array(), $post = array())
    {
		$result = array();
		
        // 验证收货人信息填写是否完整
        if (!($consignee_info = $this->validConsigneeInfo($post))) {
            return false;
        }
		// 验证配送方式信息填写是否完整
		if(!($delivery_info = $this->validDeliveryInfo($post))) {
			return false;
		}
		
        // 计算配送费用 - 运费模板
		$addr_id 		= intval($post['addr_id']);
		$delivery_type 	= $post['delivery_type'];
		
		$shipping_method = $this->getOrderShippings($goods_info);
		foreach($shipping_method as $store_id => $shipping)
		{
        	$result[$store_id] = array(
				'consignee'     =>  $consignee_info['consignee'],
				'region_id'     =>  $consignee_info['region_id'],
				'region_name'   =>  $consignee_info['region_name'],
				'address'       =>  $consignee_info['address'],
				'zipcode'       =>  $consignee_info['zipcode'],
				'phone_tel'     =>  $consignee_info['phone_tel'],
				'phone_mob'     =>  $consignee_info['phone_mob'],
				'shipping_name' =>  addslashes(Language::get($delivery_type[$store_id])),
				'shipping_fee'  =>  $shipping[$addr_id][$delivery_type[$store_id]]['logistic_fees'],
			);
		}
		return $result;
    }
	
	/* 验证收货人信息是否合法 */
    public function validConsigneeInfo($post = array())
    {
		if($post['addr_id']) {
			$post = array_merge($post, $this->getMyAddress($post['addr_id']));
		}
        if (!$post['consignee']) {
       		$this->errors = Language::get('consignee_empty');
            return false;
        }
        if (!$post['region_id']) {
            $this->errors = Language::get('region_empty');
            return false;
        }
        if (!$post['address']) {
            $this->errors = Language::get('address_empty');
            return false;
        }
        if (!$post['phone_tel'] && !$post['phone_mob']) {
            $this->errors = Language::get('phone_required');
            return false;
        }
		return $post;
	}
	
	/* 验证配送方式是否合法 */
	public function validDeliveryInfo($post = array())
	{
		if(!isset($post['delivery_type']) || !is_array($post['delivery_type'])) {
			$this->errors = Language::get('shipping_required');
			return false;
		}
		foreach($post['delivery_type'] as $val) {
			if (!$val || !in_array($val, array('express', 'ems', 'post'))) {
				$this->errors = Language::get('shipping_required');
				return false;
			}
		}
        return true;
    }
	
	/* 我的收货地址 */
	public function getMyAddress($addr_id = 0) {
		$query = AddressModel::find()->where(['userid' => Yii::$app->user->id])->orderBy(['defaddr' => SORT_DESC])->indexBy('addr_id');
		if($addr_id) {
			return $query->andWhere(['addr_id' => intval($addr_id)])->asArray()->one();
		}
		return $query->asArray()->all();
	}
	
	/* 获取本次订单的各个店铺的可用优惠券 */
	public function getStoreCouponList($goods_info = array())
	{
		foreach($goods_info['orderList'] as $store_id => $order) {
			if($order['allow_coupon']) {
				$goods_info['orderList'][$store_id]['coupon_list'] = CouponModel::getAvailableByOrder($order);
			}
		}
		return $goods_info;
	}
	
	/* 取得有效的订单折扣信息，如积分抵扣，店铺优惠券的合理性，返回各个优惠减少的金额 */
	public function getAllDiscountByPost($goods_info = array(), $post = array())
	{
		$result = $discount_info = array();

		// 验证买家使用多少积分抵扣货款的有效性
		if(isset($goods_info['allow_integral']) && $goods_info['allow_integral'])
		{
			$result = $goods_info['integralExchange'];
			
			!isset($post['exchange_integral']) && $post['exchange_integral'] = 0;
			if($post['exchange_integral'] > $result['maxPoints'])
			{
				$this->errors = Language::get('order_can_use_max_integral').$result['maxPoints'];
				return false;
			} 
			elseif($post['exchange_integral'] > 0) {		
				$discount_info['integral'] = array(
					'amount' 		=> round($post['exchange_integral'] * $result['rate'], 2), 
					'points' 		=> $post['exchange_integral'],
					'orderIntegral' => $result['orderIntegral']
				);
			}
		}
		
		// 验证买家使用的优惠券的有效性
		$goods_info = $this->getStoreCouponList($goods_info);
		$result = $goods_info['orderList'];
				
		if(!isset($post['coupon_sn'])) $post['coupon_sn'] = array();
		
		foreach($post['coupon_sn'] as $store_id => $coupon_sn)
		{
			if(isset($result[$store_id]['coupon_list']) && !empty($result[$store_id]['coupon_list']))
			{
				foreach($result[$store_id]['coupon_list'] as $key => $val)
				{
					if($coupon_sn == $val['coupon_sn'])
					{
						$discount_info['coupon'][$store_id] = array('coupon_value' => $val['coupon_value'], 'coupon_sn' => $coupon_sn);
						break;
					}
				}
			}
		}
		
		foreach($goods_info['storeIds'] as $store_id) 
		{
			$promotool = Promotool::getInstance()->build(['store_id' => $store_id]);
			
			// 处理满折满减信息
			if($fullprefer = $promotool->getOrderFullPreferInfo($goods_info['orderList'][$store_id])) {
				$discount_info['fullprefer'][$store_id] = array('amount' => $fullprefer['price']);
			}
		}
		
		return $discount_info;
	}
	
	/* 检验折扣信息和订单总价的合理性 */
	public function checkAllDiscountForOrderAmount(&$base_info, &$discount_info, $consignee_info, $integralExchangeRate = 0)
	{
		$amount = 0;
		foreach($base_info as $store_id => $order_info)
		{
			// 商品总价
			$goodsAmount 	= $order_info['goods_amount'];
			// 包含运费的订单总价
			$storeAmount 	= $order_info['goods_amount'] + $consignee_info[$store_id]['shipping_fee']; 

			$couponDiscount = $fullpreferDiscount = 0;
			
			// 每个订单的店铺优惠券优惠
			if(isset($discount_info['coupon'][$store_id]['coupon_value']))
			{
				$couponDiscount = $discount_info['coupon'][$store_id]['coupon_value'];
				if($couponDiscount > 0)
				{
					// 如果优惠折扣大于订单总价
					if($couponDiscount > $storeAmount)
					{
						$this->errors = Language::get('discount_gt_storeAmount');
						return false;
					}
					
					$storeAmount -= $couponDiscount;
				}
			}
			// 每个订单的满折满减优惠
			if(isset($discount_info['fullprefer'][$store_id]['amount']))
			{
				$fullpreferDiscount 	= $discount_info['fullprefer'][$store_id]['amount'];
				if($fullpreferDiscount > 0)
				{
					// 如果优惠折扣大于订单总价
					if($fullpreferDiscount > $storeAmount)
					{
						$this->errors = Language::get('discount_gt_storeAmount');
						return false;
					}
					
					$storeAmount -= $fullpreferDiscount;
				}
			}
			
			// 返回的数据
			$base_info[$store_id]['order_amount'] 	= $storeAmount;
			$base_info[$store_id]['goods_amount']   = $goodsAmount;
			$base_info[$store_id]['discount']		= $couponDiscount + $fullpreferDiscount;
			
			// 所有订单实际支付的金额汇总（未使用积分前）
			$amount	+= $storeAmount;
		}
		
		/*  情况一：所有订单减去折扣之后的总额为零，那么说明已经不能再使用积分来抵扣了 */
		/*  情况二：所有订单减去折扣之后的总额不为零，则判断积分抵扣的金额是否合理（如使用积分抵扣后订单总额为负，则不合理）*/
		if(isset($discount_info['integral']['amount']) && ($discount_info['integral']['amount'] > 0)) 
		{
			if(($amount <= 0) || ($discount_info['integral']['amount'] > $amount))
			{
				$this->errors = Language::get('integral_gt_amount');
				return false;
			}
		}
		
		/* 至此说明所使用的积分抵扣值是合理的（不大于订单总价了，或者本次订单没有使用积分来抵扣，如果使用了积分抵扣，还要继续判断哪个订单使用了多少积分来抵扣，用分摊来计算） */
		
		if(($amount > 0) && (isset($discount_info['integral']['amount']) && ($discount_info['integral']['amount'] > 0)))
		{
			foreach($base_info as $store_id => $order_info)
			{
				$rate = $discount_info['integral']['orderIntegral']['items'][$store_id] / $discount_info['integral']['orderIntegral']['totalPoints'];
				$sharePoints = round($rate * $discount_info['integral']['points'], 2);
				$shareAmount = round($rate * $discount_info['integral']['points'] * $integralExchangeRate, 2);
				
				// 在这里已经不用判断各个订单分摊的积分，是否抵扣完订单总价甚至抵扣为负值的情况了，因为最多能抵扣完， 不会出现负值的情况
				$discount_info['integral']['shareIntegral'][$store_id] = array('amount' => $shareAmount, 'points' => $sharePoints);
				
				// 返回的数据
				$base_info[$store_id]['order_amount'] 	-= $shareAmount;
				$base_info[$store_id]['discount']		+= $shareAmount;
			}
		}
		
		return true;
	}
	
	/* 获取本次订单的运费资费（多个店铺） */
	public function getOrderShippings($goods_info = array())
	{
		$shipping_methods = array();

       	 // 获取我的收货地址
        $my_address = $this->getMyAddress();
		if(empty($my_address)) {
			return array();
		}

		// 根据goods_info找出所有店铺每个商品的运费模板id
		$base_deliverys = array();
		foreach($goods_info['orderList'] as $store_id => $order)
		{
			foreach($order['items'] as $goods)
			{
				$template_id = GoodsModel::find()->select('dt_id')->where(['goods_id' => $goods['goods_id']])->scalar();
				
				// 如果商品的运费模板id为0，即未设置运费模板，则获取店铺默认的运费模板（取第一个）
				if(!$template_id || !($delivery = DeliveryTemplateModel::find()->where(['template_id' => $template_id])->asArray()->one()))
				{
					$delivery = DeliveryTemplateModel::find()->where(['store_id' => $store_id])->orderBy(['template_id' => SORT_ASC])->asArray()->one();
					// 如果店铺也没有默认的运费模板
					if(empty($delivery)){
						$delivery = DeliveryTemplateModel::addFirstTemplate();
					}			
				}
				$base_deliverys[$store_id][$goods['goods_id']] = $delivery;
			}
		}
		
		// 根据运送目的地，获取运费情况
		foreach($my_address as $addr_id => $address)
		{
			$city_id = $address['region_id']; // 此处不是 city_id 的话，可能影响也不大。
			foreach($base_deliverys as $store_id => $goods_deliverys)
			{
				$deliverys = array();
				foreach($goods_deliverys as $key => $delivery){
					$deliverys[$key] = DeliveryTemplateModel::getCityLogistic($delivery, $city_id);
				}
		
				// 一、如果每个商品可用的运送方式都一致，则统一计算；二、 如果有一个商品的运送方式不同，则进行组合计算
				// 注：目前已经强制每个运费模板都必须设置三个运送方式，所以不存在不全等的情况。 

				// 1. 分别计算每个运送方式的费用：找出首费最大的那个运费方式，作为首费，并且找出作为首费的那个商品id，便于在统计运费总额时，该商品使用首费，其他商品使用续费计算
				$merge_info = array(
					'express' => array('start_fees'=>0,'goods_id'=>0),
					'ems'     => array('start_fees'=>0,'goods_id'=>0),
					'post'    => array('start_fees'=>0,'goods_id'=>0),
				);
				foreach($deliverys as $goods_id	=> $delivery)
				{
					foreach($delivery as $template_types)
					{
						if($merge_info[$template_types['type']]['start_fees'] <= $template_types['start_fees']){
							$merge_info[$template_types['type']]['start_fees'] = $template_types['start_fees'];
							$merge_info[$template_types['type']]['goods_id'] = $goods_id;
						}
					}
				}
				
				// 2. 计算每个订单（店铺）的商品的总件数（包括不同规格）和每个商品的总件数（包括不同规格），以下会用到总件数来计算运费
				$total_quantity = 0;
				$quantity = array();
				foreach($goods_info['orderList'][$store_id]['items'] as $goods)
				{
					!isset($quantity[$goods['goods_id']]) && $quantity[$goods['goods_id']] = 0;
					$quantity[$goods['goods_id']] += $goods['quantity'];
					$total_quantity += $goods['quantity'];
				}
				// 3. 计算总运费
				$logistic = array();
				foreach($deliverys as $goods_id => $delivery)
				{
					foreach($delivery as $template_types)
					{
						if($goods_id == $merge_info[$template_types['type']]['goods_id']){
							if($total_quantity > $template_types['start_standards'] && $template_types['add_standards'] > 0){
								if($quantity[$goods_id] > $template_types['start_standards']) {
									$goods_fees = $merge_info[$template_types['type']]['start_fees'] + ($quantity[$goods_id]- $template_types['start_standards'])/$template_types['add_standards'] * $template_types['add_fees'];
								}
								else {
									$goods_fees = $merge_info[$template_types['type']]['start_fees'];
								}
								
							} else {
								$goods_fees = $merge_info[$template_types['type']]['start_fees'];
							}
							//$logistic[$template_types['type']]['list_fee'][$goods_id]['logistic_fee'] +=  $goods_fees;	
						}
						else
						{
							if($template_types['add_standards']>0){
								$goods_fees = $quantity[$goods_id]/$template_types['add_standards'] * $template_types['add_fees'];
							} else {
								$goods_fees = $template_types['add_fees'];
							}
							//$logistic[$template_types['type']]['list_fee'][$goods_id]['logistic_fee'] += $goods_fees;
						}
						
						!isset($logistic[$template_types['type']]['logistic_fees']) && $logistic[$template_types['type']]['logistic_fees'] = 0;
						$logistic[$template_types['type']]['logistic_fees'] += round($goods_fees, 2);
						$logistic[$template_types['type']]['addr_id'] = $addr_id;
						$logistic[$template_types['type']] += $template_types;	
					}
				}
				
				// 检查是否满足满包邮条件
				if(($result = Promotool::getInstance()->build(['store_id' => $store_id])->getOrderFullfree($goods_info['orderList'][$store_id])) !== false) {
					foreach($logistic as $k => $v) {
						$logistic[$k]['logistic_fees'] = 0;
						$logistic[$k]['name'] = $v['name'].'('.$result['title'].')';
					}
				}
				$shipping_methods[$store_id][$addr_id] = $logistic;
			}
		}
		
		// 返回本次订单所有地址的运费资费
		return $shipping_methods;
	}
	
	/* 更新优惠券的使用次数 */
	public function updateCouponRemainTimes($result = array(), $coupon = array())
	{
		foreach($result as $store_id => $order_id) {
			if(isset($coupon[$store_id]['coupon_sn'])) {
				$query = CouponsnModel::find()->select('remain_times')->where(['coupon_sn' => $coupon[$store_id]['coupon_sn']])->one(); 
 				if ($query->remain_times > 0) {
					$query->updateCounters(['remain_times' => -1]);
  				}
			}
		}
	}
	
	/* 保存订单使用的积分数额 */
	public function saveIntegralInfoByOrder($result = array(), $integral = array())
	{
		if(!empty($result) && isset($integral['points']) && ($integral['points'] > 0))
		{
			foreach($result as $store_id => $order_id)
			{
				if($integral['shareIntegral'][$store_id]['points'] > 0)
				{
					$order = OrderModel::find()->select('order_sn, buyer_id')->where(['order_id' => $order_id])->one(); 
				
					// 扣减操作
					IntegralModel::updateIntegral([
						'userid' 	=> $order->buyer_id,
						'type'    	=> 'buying_pay_integral',
						'order_id'	=> $order_id,
						'order_sn'	=> $order->order_sn,
						'amount'  	=> $integral['shareIntegral'][$store_id]['points'],
						'state'   	=> 'frozen',
						'flow'    	=> 'minus'
					]);
								
					// 积分扣减
					$model = new OrderIntegralModel();
					$model->order_id = $order_id;
					$model->buyer_id = $order->buyer_id;
					
					// 买家抵价的积分，该积分会在交易完成后付给卖家，从买家账户中扣除
					$model->frozen_integral = $integral['shareIntegral'][$store_id]['points'];
					$model->save();
				}
			}
		}
	}
	
	/* 下单完成后的操作，如清空购物车，更新库存等 */
	public function afterInsertOrder($order_id, $store_id, $goods_info)
	{
		// 订单下完后清空指定购物车
		if(in_array($this->otype, ['normal'])) {
			CartModel::deleteAll(['userid' => Yii::$app->user->id, 'selected' => 1, 'store_id' => $store_id]);
		}

		// 减去商品库存
		OrderModel::changeStock('-', $order_id);

  		// 更新下单次数（非销量）
    	foreach ($goods_info['items'] as $goods) {
			GoodsStatisticsModel::updateAllCounters(['orders' => 1], ['goods_id' => $goods['goods_id']]);
     	}
		
		$orderInfo = OrderModel::find()->where(['order_id' => $order_id])->asArray()->one();
		
		// 邮件提醒： 买家已下单通知自己 
		Basewind::sendMailMsgNotify($orderInfo, array('key' => 'tobuyer_new_order_notify', 'receiver' => Yii::$app->user->id));
		
		// 短信和邮件提醒： 买家已下单通知卖家 
		Basewind::sendMailMsgNotify($orderInfo, array(
				'key' => 'toseller_new_order_notify',
			),
			array(
				'key' => 'toseller_new_order_notify', 
			)
		);
    }
}