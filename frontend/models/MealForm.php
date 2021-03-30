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

use common\models\MealModel;
use common\models\MealGoodsModel;
use common\models\GoodsModel;
use common\models\GoodsSpecModel;

use common\library\Language;

/**
 * @Id MealForm.php 2018.10.23 $
 * @author mosir
 */
class MealForm extends Model
{
	public $errors = null;
	
	public function formData($post = null)
	{
		if (!$post->id && !$post->goods_id){
			$this->errors = Language::get('Hacking Attempt');
			return false;
        }
		
		// 点击的的某个具体的套餐
		if($post->id) {
			$meal = MealModel::find()->with('mealGoods')->where(['status' => 1, 'meal_id' => $post->id])->asArray()->one();
		}
		// 点击的是某个商品所有的套餐
		if($post->goods_id)
		{
			if(!($all = MealGoodsModel::find()->alias('mg')->select('mg.meal_id,title')->joinWith('meal m', false)->where(['status' => 1, 'goods_id' => $post->goods_id])->asArray()->all())){
				$this->errors = Language::get('not_existed_or_invalid');
				return false;
			}
			if(!$meal) {
				$first = current($all);
				$meal = MealModel::find()->with('mealGoods')->where(['status' => 1, 'meal_id' => $first['meal_id']])->asArray()->one();
			}
		}
		
		if(!$meal){
			$this->errors = Language::get('not_existed_or_invalid');
			return false;
		}
		
		if(!isset($meal['mealGoods']) || empty($meal['mealGoods'])) {
			$this->errors = Language::get('no_such_meal');
			return false;
		}
		
		$price_old_total = array('min' => 0, 'max' => 0);
		$price_default_total = 0;	
		foreach($meal['mealGoods'] as $key => $val)  
		{
			if(($goods = $this->getSpecs($val['goods_id']))) 
			{
				empty($goods['default_image']) && $goods['default_image'] = Yii::$app->params['default_goods_image'];
				
				// 去重复
				$spec_1 = [];
				$spec_2 = [];  
				foreach($goods['goodsSpec'] as $k => $v)
				{
					$spec_1[$k] = $v['spec_1'];
					$spec_2[$k] = $v['spec_2'];
				}
				$goods['spec_1'] = array_unique($spec_1);
				$goods['spec_2'] = array_unique($spec_2);
				
				// 兼容规格图片功能，给每项增加图片路径，第二个规格不需要图片（BEGIN）
				$format_spec = array();
				foreach($goods['spec_1'] as $k => $v) {
					$format_spec[$k] = array('name' => $v, 'image' => $goods['goodsSpec'][$k]['spec_image']); 
				}
				$goods['spec_1'] = $format_spec;
				
				$format_spec = array();
				foreach($goods['spec_2'] as $k => $v) {
					$format_spec[$k] = array('name' => $v);
				}
				$goods['spec_2'] = $format_spec;
				// END
				
				// 找出这个商品的最高价与最低价
				if(($price_data = $this->getSpecMinMax($goods['goods_id']))) {
					$price_old_total['min'] += $price_data['min'];
					$price_old_total['max'] += $price_data['max'];
				}
				
				// 默认价格总计
				$price_default_total += $goods['price'];
				
				$meal['mealGoods'][$key] = array_merge($meal['mealGoods'][$key], $goods);
			}
			else
			{
				$model = MealModel::findOne($post->id);
				$model->status = 0;
				if(!$model->save()) {
					$this->errors = $model->errors;
					return false;
				}
				$this->errors = Language::get('not_existed_or_invalid');
				return false;
			}	
		}
		
		// 判断价格是否合适，如果套餐价格大于原商品总价的最小价格，则认为该套餐价格不合理，设置为无效套餐
		if($meal['price'] > $price_old_total['min']) 
		{
			$model = MealModel::findOne($meal['meal_id']);
			$model->status = 0;
			if(!$model->save()) {
				$this->errors = $model->errors;
				return false;
			}
			$this->errors = Language::get('not_existed_or_invalid');
			return false;
		}
		
		$meal['default_save'] = $price_default_total - $meal['price'];
		$meal['price_old_total'] = $price_old_total;
		
		return array($meal, $all);
	}
	
	private function getSpecs($goods_id = 0)
	{
		$goods = GoodsModel::find()->with('goodsSpec')->select('goods_id,goods_name,default_image,price,spec_name_1,spec_name_2,default_spec,spec_qty')->where(['goods_id' => $goods_id])->asArray()->one();
		return $goods;
	}
	
	private function getSpecMinMax($goods_id = 0)
	{	
		if(!($specs = GoodsSpecModel::find()->select('price')->where(['goods_id' => $goods_id])->orderBy(['spec_id' => SORT_ASC])->all())) {
			return array();
		}
		
		$result = array();
		foreach($specs as $key => $item)
		{
			if(!isset($result['min'])) $result['min'] = $item->price;
			if(!isset($result['max'])) $result['max'] = $item->price;
			
			if($result['min'] > $item->price) $result['min'] = $item->price;
			if($result['max'] < $item->price) $result['max'] = $item->price;	
		}
		return $result;
	}
}
