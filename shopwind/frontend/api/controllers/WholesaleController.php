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

use common\models\WholesaleModel;
use common\models\GoodsModel;
use common\models\GoodsSpecModel;

use common\library\Basewind;
use common\library\Language;

use frontend\api\library\Respond;
use frontend\api\library\Formatter;

/**
 * @Id WholesaleController.php 2021.5.13 $
 * @author yxyc
 */

class WholesaleController extends \common\base\BaseApiController
{
	/**
	 * 获取指定商品批发价格
	 * @api 接口访问地址: http://api.xxx.com/wholesale/read
	 */
    public function actionRead()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(false)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['goods_id', 'spec_id']);
		$post->goods_id = $this->getGoodsId($post);

		$record = GoodsModel::find()->select('goods_id,goods_name,store_id,default_image as goods_image')->where(['goods_id' => $post->goods_id])->asArray()->one();
		if($record) {
			$record['rules'] = WholesaleModel::find()->select('price,quantity,status')->where(['goods_id' => $post->goods_id])->orderBy(['quantity' => SORT_ASC])->asArray()->all();
			$record['goods_image'] = Formatter::path($record['goods_image'], 'goods');
		}

		return $respond->output(true, null, $record);
    }

	/**
	 * 新增/编辑批发商品
	 * @api 接口访问地址: http://api.xxx.com/wholesale/update
	 */
	public function actionUpdate()
    {
        // 验证签名
        $respond = new Respond();
        if (!$respond->verify(true)) {
            return $respond->output(false);
        }

        // 业务参数
        $post = Basewind::trimAll($respond->getParams(), true);

		$model = new \frontend\home\models\WholesaleForm(['store_id' => Yii::$app->user->id]);
		if(!$model->save($post, true)) {
			return $respond->output(Respond::HANDLE_INVALID, $model->errors);
		}
			
        return $respond->output(true, null);
    }

	/**
	 * 删除批发商品
	 * @api 接口访问地址: http://api.xxx.com/wholesale/delete
	 */
	public function actionDelete()
    {
		// 验证签名
        $respond = new Respond();
        if (!$respond->verify(true)) {
            return $respond->output(false);
        }

        // 业务参数
        $post = Basewind::trimAll($respond->getParams(), true, ['goods_id']);

		if(!$post->goods_id) {
			return $respond->output(Respond::RECORD_NOTEXIST, Language::get('no_such_item'));
		}
		
		if(!WholesaleModel::deleteAll(['goods_id' => $post->goods_id, 'store_id' => Yii::$app->user->id])) {
			return $respond->output(Respond::HANDLE_INVALID, Language::get('drop_fail'));
		}

        return $respond->output(true);
    }

	/**
	 * 获取指定商品批发价格
	 * @api 接口访问地址: http://api.xxx.com/wholesale/price
	 */
    public function actionPrice()
    {
		// 验证签名
		$respond = new Respond();
		if(!$respond->verify(false)) {
			return $respond->output(false);
		}
		
		// 业务参数
		$post = Basewind::trimAll($respond->getParams(), true, ['goods_id', 'spec_id', 'quantity']);
		$post->goods_id = $this->getGoodsId($post);

		$list =  WholesaleModel::find()->select('price,quantity')->where(['goods_id' => $post->goods_id, 'status' => 1])->orderBy(['quantity' => SORT_ASC])->asArray()->all();
		foreach($list as $key => $value) {
			$this->params['list'][] = array('price' => $value['price'], 'min' => intval($value['quantity']), 'max' => (isset($list[$key+1]) && $list[$key+1]['quantity'] > 1) ? $list[$key+1]['quantity']-1 : 0);
		}

		// 读取商品批发价格
		$query = WholesaleModel::find()->where(['goods_id' => $post->goods_id, 'status' => 1])->andWhere(['<=', 'quantity', $post->quantity])->orderBy(['quantity' => SORT_DESC])->one();
		if($query) {
			$this->params = array_merge($this->params, [
				'price' => round($query->price, 2),
				'quantity' => $query->quantity
			]);
		}

		return $respond->output(true, null, $this->params);
    }

	private function getGoodsId($post = null) 
	{
		if(isset($post->goods_id) && $post->goods_id) {
			return $post->goods_id;
		}

		return GoodsSpecModel::find()->select('goods_id')->where(['spec_id' => $post->spec_id])->scalar();
	}
}