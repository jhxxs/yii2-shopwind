<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */

namespace frontend\models;

use Yii;
use yii\base\Model;

use common\models\StoreModel;
use common\models\GoodsSpecModel;
use common\models\CouponModel;

use common\library\Language;
use common\library\Timezone;
use common\library\Promotool;
use common\library\Page;

/**
 * @Id CartForm.php 2018.7.10 $
 * @author mosir
 */
class CartForm extends Model
{
	public $errors = null;
	
	/**
	 *  used in PC/H5/API
	 */
	public function formData($products = array())
	{
		$list = array();
		if($products && $products['items']) {
			foreach($products['items'] as $goods) {
				$goods['subtotal'] = sprintf('%.2f', round($goods['price'] * $goods['quantity'], 2));
				$goods['goods_image'] = Page::urlFormat($goods['goods_image']);
				$list[$goods['store_id']]['items'][$goods['product_id']] = $goods;
				$list[$goods['store_id']]['store_name'] = StoreModel::find()->select('store_name')->where(['store_id' => $goods['store_id']])->scalar();
				$list[$goods['store_id']]['store_id'] = $goods['store_id'];

				// 店铺金额统计
				if(!isset($list[$goods['store_id']]['total'])) $list[$goods['store_id']]['total'] = 0;
				$list[$goods['store_id']]['total'] += $goods['subtotal'];
			}
			return array('list' => $list, 'amount' => sprintf('%.2f', $products ? $products['amount'] : 0));
		}
		return null;
	}
	
	/**
	 * 店铺满折满减
	 */
	public function getCartFullprefer($carts = array())
	{
		if(empty($carts)) return null;
		
		foreach($carts as $store_id => $cart)
		{
			$fullpreferTool = Promotool::getInstance('fullprefer')->build(['store_id' => $store_id]);
			if($fullpreferTool->checkAvailable()){
				$fullprefer = $fullpreferTool->getInfo();
				if(isset($fullprefer['status']) && $fullprefer['status']) {
					if($fullprefer['rules']['type'] == 'discount') {
						$carts[$store_id]['storeFullPreferInfo'] = array(
							'text' => sprintf('购满%s元可享%s折', $fullprefer['rules']['amount'], $fullprefer['rules']['discount']),
							'amount' => $fullprefer['rules']['amount'],
							'prefer' => ['label' => '满折', 'type' => 'discount', 'value' => sprintf('%.2f', $fullprefer['rules']['discount'])],
						);
					} 
					else 
					{
						$carts[$store_id]['storeFullPreferInfo'] = array(
							'text' => sprintf('购满%s元可减%s元', $fullprefer['rules']['amount'], $fullprefer['rules']['decrease']),
							'amount' => $fullprefer['rules']['amount'],
							'prefer' => ['label' => '满减', 'type' => 'decrease', 'value' => sprintf('%.2f', $fullprefer['rules']['decrease'])],
						);
					}
				}
			}
		}	
		return $carts;
	}
	
	/**
	 * 是否显示领取优惠券按钮
	 */
	public function getCouponEnableReceive($carts = array())
	{
		if(empty($carts)) return null;
		
		foreach($carts as $store_id => $cart)
		{
			if(CouponModel::find()->where(['clickreceive' => 1, 'if_issue' => 1, 'store_id' => $store_id])->andWhere(['>', 'end_time', Timezone::gmtime()])->andWhere(['or', ['total' => 0], ['and', ['>', 'total', 0], ['>', 'surplus', 0]]])->exists()) {
				$carts[$store_id]['couponReceive'] = 1;
			}
		}
		return $carts;
	}
	
	public function valid($post)
	{
		if(!$post->spec_id || !$post->quantity) {
			$this->errors = Language::get('no_such_goods');
			return false;
		}
		
        // 是否有商品
		if(!($specInfo = GoodsSpecModel::find()->alias('gs')->select('g.store_id, g.goods_id, g.goods_name, g.spec_name_1, g.spec_name_2, g.default_image, gs.spec_id, gs.spec_1, gs.spec_2, gs.stock, gs.price,gs.spec_image')->joinWith('goods g', false)->where(['spec_id' => $post->spec_id])->asArray()->one())) {
			$this->errors = Language::get('no_such_goods');
			return false;
		}
		
		// 如果是自己店铺的商品，则不能购买
		if($specInfo['store_id'] == Yii::$app->user->id) {
			$this->errors = Language::get('can_not_buy_yourself');
			return false;
		}
		if($specInfo['stock'] < $post->quantity) {
			$this->errors = Language::get('no_enough_goods');
			return false;
		}

		// 读取促销价格
		$promotool = Promotool::getInstance()->build();
		if(($result = $promotool->getItemProInfo($specInfo['goods_id'], $post->spec_id)) !== false) {
			if($result['price'] != $specInfo['price']) {
				$specInfo['price'] = $result['price'];
			}
		}
		
		return $specInfo;
    }
	
	/**
	 * 用于判断购物车的商品价格是否有过变更
	 * 要考虑登录状态的下数据（DB） 和 未登录状态的数据（SESSION）
	 * 所以不应该是从数据库购物车表去查找比对
	 */
	public function ifchange($spec_id = 0, $product_id)
	{
		$cart = Yii::$app->cart->find();
		
		if(($cart['kinds'] > 0)) {
			if(isset($cart['items'][$product_id])) {
				return false;
			}
			else
			{
				foreach($cart['items'] as $item)
				{
					if(($item['spec_id'] == $spec_id) && ($item['userid'] == intval(Yii::$app->user->id))) {
						
						// 从购物车删除该数据
						Yii::$app->cart->remove($item['product_id']);
						break;
					}
				}
			}
		}

		return true;
	}
}
