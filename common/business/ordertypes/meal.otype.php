<?php

/**
 * @link http://www.shopwind.net/
 * @copyright Copyright (c) 2018 shopwind, Inc. All Rights Reserved.
 *
 * This is not free software. Do not use it for commercial purposes. 
 * If you need commercial operation, please contact us to purchase a license.
 * @license http://www.shopwind.net/license/
 */
 
namespace common\business\ordertypes;

use yii;

use common\models\MealModel;
use common\models\GoodsSpecModel;

/**
 * @Id meal.otype.php 2018.7.12 $
 * @author   mosir
 * @desc  goods from meal
 */
 
class MealOrder extends NormalOrder
{
	public $otype = 'meal';
	
	/* 获取搭配套餐中的商品数据，用来计算订单可使用的最大积分值 */
	public function getOrderGoodsList($extraParams = array())
	{
		extract($extraParams);
		
		$result = array();
		if(!$meal_id || empty($specs)) {
			return false;
		}

		if(!($meal = MealModel::find()->select('meal_id,price,store_id,status,title')->with('mealGoods')->where(['meal_id' => $meal_id])->asArray()->one())) {
			return false;
		}
		
		if(empty($meal['mealGoods'])) {
			return false;
		}
				
		// 记录套餐商品
		foreach($meal['mealGoods'] as $goods) {
			$check_goods_1[] = $goods['goods_id'];
		}
		
		// 记录客户选中的商品规格
		foreach($specs as $key => $spec_id)
		{
			if(($goods = GoodsSpecModel::find()->alias('gs')->select('gs.spec_id,gs.price,gs.spec_1,gs.spec_2,gs.stock,gs.spec_image,g.goods_id,g.store_id,g.goods_name,g.default_image as goods_image,g.spec_name_1,g.spec_name_2')->where(['spec_id' => $spec_id])->joinWith('goods g', false)->asArray()->one())) 
			{
				$goods['quantity'] = 1;//  套餐商品默认都是购买一件
				!empty($goods['spec_1']) && $goods['specification'] = $goods['spec_name_1'] . ':' . $goods['spec_1'];	
				!empty($goods['spec_2']) && $goods['specification'] .= ' ' . $goods['spec_name_2'] . ':' . $goods['spec_2']; 
				
				// 兼容规格图片功能
				if(isset($goods['spec_image']) && $goods['spec_image']) {
					$goods['goods_image'] = $goods['spec_image'];
					unset($goods['spec_image']);
				}
				
				$result[$key] = $goods;
				$check_goods_2[] = $goods['goods_id'];
			}
		}
		if(empty($check_goods_1) || empty($check_goods_2)) {
			return false;
		}
		sort($check_goods_1);
		sort($check_goods_2);
			
		// 买家选择购买的商品和套餐商品不一致
		if(array_diff($check_goods_1, $check_goods_2) || array_diff($check_goods_2, $check_goods_1)) {
			return false;
		}
		return array($result, $meal);
	}
}