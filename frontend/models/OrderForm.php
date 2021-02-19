<?php

/**
 * @link https://www.shopwind.net/
 * @copyright Copyright (c) 2018 ShopWind Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license https://www.shopwind.net/license/
 */

namespace frontend\models;

use Yii;
use yii\base\Model;

use common\models\StoreModel;
use common\models\IntegralModel;
use common\models\IntegralSettingModel;

use common\library\Language;
use common\library\Business;

/**
 * @Id OrderForm.php 2018.7.12 $
 * @author mosir
 */
class OrderForm extends Model
{
	// order type, item data from where, eg: cart|meal ...
	public $otype 	= 'normal';
	
	// goods type, eg: material|virtual 
	public $gtype 	= 'material';
	
	public $errors 	= null;
	
	/*
	 * 从购物车/搭配套餐等实例中取商品
	 */
	public function getGoodsInfo($post = null)
	{
		$extraParams = array();
		
		if($this->otype == 'normal') {
			$extraParams = ['store_id' => isset($post->store_id) ? $post->store_id : 0]; // 如果传值只结算一个店铺
		}
		if($this->otype == 'meal') {
			$extraParams = ['meal_id' => $post->id, 'specs' => explode('|', $post->sp)];
		}

		list($goodsList, $extra) = Business::getInstance('order')->build(['type' => $this->otype])->getOrderGoodsList($extraParams);
		
		if(empty($goodsList)) {
			$this->errors = Language::get('goods_empty');
			return false;
		}
		
		// 按店铺归类商品，顺带验证库存够不够
		$storeGoodsList = array();
		foreach($goodsList as $rec_id => $goods) {
			$storeGoodsList[$goods['store_id']][$rec_id] = $goods;
		}
		
		// 库存够不够
		if(($errorMsg = $this->checkBeyondStock($goodsList)) !== true) {
			$this->errors = $errorMsg;
			return false;
		}
		
		$result = array_merge($this->formatData($storeGoodsList, $extra), ['otype' => $this->otype, 'gtype' => $this->gtype]);
				
		// 是否允许使用积分抵扣
		if(($allow_integral = IntegralSettingModel::getSysSetting('enabled'))) {
			$result['allow_integral'] 	= $allow_integral;
			$result['integralExchange']	= IntegralModel::getIntegralByOrders($goodsList);
		}
		return $result;
	}
	
	private function formatData(array $storeGoodsList, $extra = array())
	{
		if($this->otype == 'meal') {
			return $this->formatDataOfMeal($storeGoodsList, $extra);
		}
		return $this->formatDataOfNormal($storeGoodsList);
	}
	
	private function formatDataOfNormal(array $storeGoodsList, $cart = array())
	{
		$result = array('amount' => 0);

		foreach($storeGoodsList as $store_id => $items) {
			$storeAmount = $storeQuantity = 0;
			foreach($items as $key => $goods) {
				$items[$key]['subtotal'] = sprintf('%.2f', $goods['quantity'] * $goods['price']);
				$storeAmount += floatval($items[$key]['subtotal']);
				$storeQuantity += intval($goods['quantity']);
			}
			$storeInfo = StoreModel::find()->select('store_id, store_name, sgrade as sgrade_id, im_qq')->where(['store_id' => $store_id])->asArray()->one();
			$result['orderList'][$store_id] = array_merge(array('items' => $items, 'amount' => $storeAmount, 'quantity' => $storeQuantity), (array)$storeInfo);
			
			// 是否允许使用优惠券
			$result['orderList'][$store_id]['allow_coupon'] = true;
			
			// 记录本次订单有多少个店铺的商品，以便其他地方使用
			$result['storeIds'][] = $store_id;
			
			// 统计各个订单的总额（商品的原价之和，并非订单最终的优惠价格，此值仅作为后续计算各个订单所占总合并订单金额的分摊比例用）
			$result['amount'] += $storeAmount;
		}
		return $result;
	}
	
	private function formatDataOfMeal(array $storeGoodsList, $meal = array())
	{
		$result = array('amount' => 0);

		foreach($storeGoodsList as $store_id => $items) {
			$storeAmount = $storeQuantity = 0;
			foreach($items as $key => $goods) {
				$items[$key]['subtotal'] = sprintf('%.2f', $goods['quantity'] * $goods['price']);
				$storeAmount += floatval($items[$key]['subtotal']);
				$storeQuantity += intval($goods['quantity']);
			}
			$storeInfo = StoreModel::find()->select('store_id, store_name, sgrade as sgrade_id, im_qq')->where(['store_id' => $store_id])->asArray()->one();
			$result['orderList'][$store_id] = array_merge(array('items' => $items, 'oldAmount' => $storeAmount, 'amount' => $meal['price'], 'quantity' => $storeQuantity), (array)$storeInfo);
			
			// 是否允许使用优惠券
			$result['orderList'][$store_id]['allow_coupon'] = true;
			
			// 记录本次订单有多少个店铺的商品，以便其他地方使用
			$result['storeIds'][] = $store_id;
			
			// 统计各个订单的总额（商品的原价之和，并非订单最终的优惠价格，此值仅作为后续计算各个订单所占总合并订单金额的分摊比例用）
			$result['amount'] += $meal['price'];
		}
		
		// 可能没有什么作用，先暂时屏蔽
		//$result['extId'] = $meal['meal_id'];
		
		return $result;
	}
	
	/* 验证库存够不够 */
	private function checkBeyondStock(array $goodsList)
	{
		$message = '';
		foreach($goodsList as $rec_id => $goods) {
			if($goods['stock'] < $goods['quantity']) {
				$message = '商品【'.$goods['goods_name'] . '】' . $goods['specification'] . '，' . Language::get('stock') . '仅剩' . $goods['stock'] . '件';
				return $message;
			}
		}
		return true;
	}
}