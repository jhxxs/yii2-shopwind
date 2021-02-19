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
use yii\helpers\ArrayHelper;

use common\models\GoodsModel;
use common\models\AppmarketModel;
use common\models\ApprenewalModel;
use common\models\LimitbuyModel;
use common\models\UploadedFileModel;

use common\library\Basewind;
use common\library\Language;
use common\library\Timezone;
use common\library\Promotool;
use common\library\Def;

/**
 * @Id Seller_limitbuyForm.php 2018.10.7 $
 * @author mosir
 */
class Seller_limitbuyForm extends Model
{
	public $pro_id = 0;
	public $store_id = null;
	public $errors = null;
	
	public function valid(&$post)
	{
		$result = array();
		
		if(($query = Promotool::getInstance('limitbuy')->build(['store_id' => $this->store_id])->checkAvailable(true, true)) !== true) {
			$this->errors = $query['msg'];
			return false;
		}
		
		if (empty($post->pro_name)) {
			$this->errors = Language::get('fill_pro_name');
			return false;
		}
		if (Timezone::gmstr2time_end($post->start_time) <= Timezone::gmtime() && !$this->pro_id) {
            $this->errors = Language::get('start_not_le_today');
			return false;
        }
        else {
            $post->start_time = Timezone::gmstr2time($post->start_time);
        }
        if ($post->end_time) {
			if(Timezone::gmstr2time_end($post->end_time) < Timezone::gmtime()) {
				$this->errors = Language::get('end_not_le_today');
				return false;
			}
			$post->end_time = Timezone::gmstr2time_end($post->end_time); // 前台时间允许到天
        }
        else {
        	$this->errors = Language::get('fill_end_time');
		    return false;
        }
        if ($post->start_time > $post->end_time) {
			$this->errors = Language::get('start_not_gt_end');
			return false;
        }
		
		// 如果是订购模式
		if(AppmarketModel::find()->select('purchase')->where(['appid' => 'limitbuy'])->scalar())
		{
			// 如果结束的时间大于该应用的购买时限，则不允许
			$apprenewal = ApprenewalModel::find()->select('expired')->where(['appid' => 'limitbuy', 'userid' => Yii::$app->user->id])->orderBy(['rid' => SORT_DESC])->one();
				
			if(!$apprenewal) {
				$this->errors = Language::get('appHasNotBuy');
				return false;
			}
			if($apprenewal->expired <= $post->end_time) {
				$this->errors = sprintf(Language::get('limitbuy_end_time_gt_app_expired'), Timezone::localDate('Y-m-d', $apprenewal->expired));
				return false;
			}
		}

        if (!$post->goods_id) {
            $this->errors = Language::get('fill_goods');
			return false;
        }
		if(LimitbuyModel::find()->where(['goods_id' => $post->goods_id])->andWhere(['!=', 'pro_id', $this->pro_id])->exists()) {
			$this->errors = Language::get('goods_has_set_limitbuy');
			return false;
		}
        if (!$post->spec_id || !is_object($post->spec_id)) {
            $this->errors = Language::get('fill_spec');
			return false;
        }
		
		$post = ArrayHelper::toArray($post);
        foreach ($post['spec_id'] as $key => $val)
        {
			if (empty($post['pro_price'][$val]))
            {
				$this->errors = Language::get('invalid_pro_price');
				return false;
            }
			else
			{
				if(in_array($post['pro_type'][$val], ['discount']) && ($post['pro_price'][$val] >= 10 || $post['pro_price'][$val] <= 0)) {
                	$this->errors = Language::get('invalid_pro_price_discount');
					return false;
				}
				if(in_array($post['pro_type'][$val], ['price']) && ($post['pro_price'][$val] >= $post['price'][$val] || $post['pro_price'][$val] == 0)) {
                	$this->errors = Language::get('invalid_pro_price_price');
					return false;
				}
			}
            $result[$val] = array('price' => $post['pro_price'][$val], 'pro_type' => $post['pro_type'][$val]);
        }
		if($result) {
			$post = (object)ArrayHelper::merge($post, ['spec_price' => $result]);
		}
		
		return true;
	}
	
	public function save($post, $valid = true)
	{
		if($valid === true && !$this->valid($post)) {
			return false;
		}

		if(!$this->pro_id || !($model = LimitbuyModel::find()->where(['pro_id' => $this->pro_id, 'store_id' => $this->store_id])->one())) {
			$model = new LimitbuyModel();
		}
		
		$model->pro_name = $post->pro_name;
		$model->pro_desc = $post->pro_desc ? $post->pro_desc : '';
		$model->start_time = $post->start_time;
		$model->end_time = $post->end_time;
		$model->goods_id = $post->goods_id;
		$model->spec_price = serialize(ArrayHelper::toArray($post->spec_price));
		$model->store_id = $this->store_id;
		
		if(Basewind::getCurrentApp() == 'pc') {
			$post->image = UploadedFileModel::getInstance()->upload($post->fileVal, $this->store_id, Def::BELONG_LIMITBUY, Yii::$app->user->id);
		}
		// 注意：PC提交后上传图片，WAP先上传图片后提交，所以WAP端有$post->image，但PC没有
		if($post->image) {
			$model->image = $post->image;
		}	
		if(!$model->save()) {
			$this->errors = $model->errors;
			return false;
		}
        return true;
	}
	
	public function queryInfo($id, $limitbuy = array())
    {
		if(!$id && $limitbuy) {
			$id = $limitbuy['goods_id'];
		}
		
		$goods = GoodsModel::find()->select('goods_id,goods_name,spec_name_1,spec_name_2,spec_qty,default_spec,default_image')->with('goodsSpec')->where(['goods_id' => $id, 'store_id' => $this->store_id])->asArray()->one();
		if(!$goods) {
			return false;
		}
		
		empty($goods['default_image']) && $goods['default_image'] = Yii::$app->params['default_goods_image'];
		
        if ($goods['spec_qty'] == 1 || $goods['spec_qty'] == 2) {
            $goods['spec_name'] = htmlspecialchars($goods['spec_name_1'] . ($goods['spec_name_2'] ? ' ' . $goods['spec_name_2'] : ''));
        }
        else {
            $goods['spec_name'] = Language::get('spec');
        }
		
        foreach ($goods['goodsSpec'] as $key => $spec)
        {	
            if ($goods['spec_qty'] == 1 || $goods['spec_qty'] == 2) {
                $goods['goodsSpec'][$key]['spec'] = htmlspecialchars($spec['spec_1'] . ($spec['spec_2'] ? ' ' . $spec['spec_2'] : ''));
			}
		    else {
                $goods['goodsSpec'][$key]['spec'] = Language::get('default_spec');
            }
			
			if($limitbuy) {
				$goods['goodsSpec'][$key]['pro_price'] = $limitbuy['spec_price'][$spec['spec_id']]['price'];
				$goods['goodsSpec'][$key]['pro_type'] = $limitbuy['spec_price'][$spec['spec_id']]['pro_type'];
			}
        }
        return $goods;
    }
}
