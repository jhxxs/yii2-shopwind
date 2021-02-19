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

use common\models\DistributeItemsModel;
use common\models\DistributeSettingModel;

use common\library\Page;

/**
 * @Id DistributeGoodsForm.php 2018.10.19 $
 * @author mosir
 */
class DistributeGoodsForm extends Model
{
	public $errors = null;
	
	public function formData($post = null, $pageper = 4)
	{
		// 可分销的商品
		if($post->type == 'pending') {
			$query = DistributeSettingModel::find()->alias('ds')->select('ds.item_id,ds.ratio1,ds.ratio2,ds.ratio3,ds.enabled,g.goods_name,g.goods_id,g.default_image,g.price')->joinWith('goods g', false, 'INNER JOIN')->where(['enabled' => 1, 'ds.type' => 'goods'])->andWhere(['not in', 'item_id', DistributeItemsModel::find()->select('item_id')->where(['userid' => Yii::$app->user->id, 'type' => 'goods'])->column()])->orderBy(['dsid' => SORT_DESC]);
		}
		// 已经分销的商品
		else {
			$query = DistributeItemsModel::find()->alias('di')->select('di.item_id,ds.ratio1,ds.ratio2,ds.ratio3,ds.enabled,g.goods_name,g.goods_id,g.default_image,g.price')->joinWith('distributeSetting ds', false, 'INNER JOIN')->joinWith('goods g', false, 'INNER JOIN')->where(['di.type' => 'goods', 'userid' => Yii::$app->user->id])->orderBy(['diid' => SORT_DESC]);
		}
			
		$page = Page::getPage($query->count(), $pageper);
		$list = $query->offset($page->offset)->limit($page->limit)->asArray()->all();
		
		foreach($list as $key => $val)
		{
			$list[$key]['ratio1'] = ($val['ratio1'] * 100) . '%';
			$list[$key]['ratio2'] = ($val['ratio2'] * 100) . '%';
			$list[$key]['ratio3'] = ($val['ratio3'] * 100) . '%';
			$list[$key]['hasDistribute'] = ($post->type == 'pending') ? true : false;
			$list[$key]['default_image'] = empty($val['default_image']) ? Yii::$app->params['default_goods_image'] : $val['default_image'];
		}
		return array($list, $page);
	}
}
