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

use common\models\DistributeOrderModel;
use common\library\Page;

/**
 * @Id DistributeOrderForm.php 2018.9.19 $
 * @author mosir
 */
class DistributeOrderForm extends Model
{
	public $errors = null;
	
	public function formData($post = null, $pageper = 4)
	{
		$query = DistributeOrderModel::find()->alias('do')->select('do.order_sn,do.type,o.seller_id,o.seller_name')->joinWith('order o', false)->where(['userid' => Yii::$app->user->id])->orderBy(['doid' => SORT_DESC])->indexBy('order_sn');
		$page = Page::getPage($query->count(), $pageper);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		
		foreach($list as $key => $val)
		{
			$goodslist = DistributeOrderModel::find()->select('do.tradeNo,do.money,do.layer,do.ratio,og.goods_id,og.goods_name,og.price,og.quantity,og.goods_image')->alias('do')->joinWith('orderGoods og', false)->where(['order_sn' => $val['order_sn'], 'type' => $val['type']])->asArray()->all();
			
			$amount = 0;
			foreach($goodslist as $k => $v) {
				$amount += $v['money'];
				$goodslist[$k]['ratio'] = ($v['ratio'] * 100).'%';
				$list[$key]['tradeNo'][] = $v['tradeNo'];
			}
			$list[$key]['amount'] = $amount;
			$list[$key]['orderGoods'] = $goodslist;
			$list[$key]['tradeNo'] = implode(',', $list[$key]['tradeNo']);
		}
		
		return array($list, $page);
	}
}
